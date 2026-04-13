<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class BooksOfAccounts extends Component
{
    public $powasID;
    public $powas;

    // Filters
    public $selectedBook = 'general_journal';
    public $dateFrom;
    public $dateTo;
    public $selectedAccountNumber = null;

    // Data
    public $entries = [];
    public $chartOfAccounts = [];
    public $baseBalancesDate;

    public $books = [
        'general_journal'        => 'General Journal',
        'cash_receipts'          => 'Cash Receipts Journal',
        'cash_disbursements'     => 'Cash Disbursements Journal',
        'general_ledger'         => 'General Ledger',
    ];

    public function mount($powasID, $powas)
    {
        $this->powasID = $powasID;
        $this->powas   = $powas;

        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Load COA for ledger filter dropdown
        $this->chartOfAccounts = ChartOfAccounts::orderBy('account_number')->get();

        // Load beginning balances date
        $balances = Storage::json('beginning_balances/' . $this->powasID . '.json');
        if ($balances) {
            $this->baseBalancesDate = array_keys($balances)[0];
        }

        $this->fetchEntries();
    }

    public function updatedSelectedBook()
    {
        $this->selectedAccountNumber = null;
        $this->fetchEntries();
    }

    public function updatedDateFrom()    { $this->fetchEntries(); }
    public function updatedDateTo()      { $this->fetchEntries(); }
    public function updatedSelectedAccountNumber() { $this->fetchEntries(); }

    public function fetchEntries()
    {
        $query = Transactions::with(['chartofaccounts'])
            ->where('powas_id', $this->powasID)
            ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('journal_entry_number', 'asc')
            ->orderByRaw("CASE WHEN transaction_side = 'DEBIT' THEN 1 ELSE 2 END")
            ->orderBy('account_number', 'asc');

        switch ($this->selectedBook) {
            case 'cash_receipts':
                // Transactions where Cash (101) is DEBITED — cash comes in
                $jeNumbers = Transactions::where('powas_id', $this->powasID)
                    ->where('account_number', 101)
                    ->where('transaction_side', 'DEBIT')
                    ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo])
                    ->pluck('journal_entry_number');
                $query->whereIn('journal_entry_number', $jeNumbers);
                break;

            case 'cash_disbursements':
                // Transactions where Cash (101) is CREDITED — cash goes out
                $jeNumbers = Transactions::where('powas_id', $this->powasID)
                    ->where('account_number', 101)
                    ->where('transaction_side', 'CREDIT')
                    ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo])
                    ->pluck('journal_entry_number');
                $query->whereIn('journal_entry_number', $jeNumbers);
                break;

            case 'general_ledger':
                if ($this->selectedAccountNumber) {
                    $query->where('account_number', $this->selectedAccountNumber);
                }
                break;

            case 'general_journal':
            default:
                // All transactions
                break;
        }

        $raw = $query->get();

        // For ledger, build running balance per account
        if ($this->selectedBook === 'general_ledger') {
            $this->entries = $this->buildLedger($raw);
        } else {
            $this->entries = $raw->map(function ($t) {
                $coa = $t->chartofaccounts;
                return [
                    'date'                 => $t->transaction_date,
                    'journal_entry_number' => $t->journal_entry_number,
                    'or_number'            => $t->or_number,
                    'account_number'       => $t->account_number,
                    'account_name'         => $coa ? $coa->account_name : '-',
                    'description'          => $t->description,
                    'debit'                => $t->transaction_side === 'DEBIT'  ? $t->amount : null,
                    'credit'               => $t->transaction_side === 'CREDIT' ? $t->amount : null,
                ];
            })->toArray();
        }
    }

    protected function buildLedger($transactions): array
    {
        // Group by account number
        $grouped = $transactions->groupBy('account_number');
        $result  = [];

        foreach ($grouped as $accountNumber => $rows) {
            $coa     = ChartOfAccounts::find($accountNumber);
            $type    = $coa ? $coa->account_type : 'ASSET';

            // Beginning balance from JSON storage
            $balances    = Storage::json('beginning_balances/' . $this->powasID . '.json') ?? [];
            $balanceDate = $this->baseBalancesDate ?? null;
            $runningBal  = $balances[$balanceDate][$accountNumber] ?? 0;

            // Adjust beginning balance for all transactions PRIOR to the dateFrom
            $query = Transactions::where('powas_id', $this->powasID)
                ->where('account_number', $accountNumber)
                ->where('transaction_date', '<', $this->dateFrom);

            if ($balanceDate) {
                $query->where('transaction_date', '>', $balanceDate);
            }

            $priorTrxns = $query->get();

            foreach ($priorTrxns as $pt) {
                $runningBal = $this->applyTransaction($runningBal, $pt->transaction_side, $pt->amount, $type);
            }

            $accountRows = [[
                'type'    => 'header',
                'account' => ($coa ? $coa->account_number . ' — ' . $coa->account_name : $accountNumber),
                'opening' => $runningBal,
            ]];

            foreach ($rows as $t) {
                $runningBal = $this->applyTransaction($runningBal, $t->transaction_side, $t->amount, $type);
                $accountRows[] = [
                    'type'                 => 'entry',
                    'date'                 => $t->transaction_date,
                    'journal_entry_number' => $t->journal_entry_number,
                    'or_number'            => $t->or_number,
                    'description'          => $t->description,
                    'debit'                => $t->transaction_side === 'DEBIT'  ? $t->amount : null,
                    'credit'               => $t->transaction_side === 'CREDIT' ? $t->amount : null,
                    'balance'              => $runningBal,
                ];
            }

            $result = array_merge($result, $accountRows);
        }

        return $result;
    }

    protected function applyTransaction(float $balance, string $side, float $amount, string $type): float
    {
        $normalDebit = in_array($type, ['ASSET', 'EXPENSE']);
        if ($side === 'DEBIT') {
            return $normalDebit ? $balance + $amount : $balance - $amount;
        }
        return $normalDebit ? $balance - $amount : $balance + $amount;
    }

    /**
     * Export the current view to CSV.
     */
    public function exportCsv()
    {
        $bookLabel = $this->books[$this->selectedBook] ?? 'Book';
        $filename  = str_replace(' ', '_', $bookLabel) . '_' . $this->dateFrom . '_to_' . $this->dateTo . '.csv';

        $rows = [];

        // Header meta
        $rows[] = ['"' . ($this->powas->barangay ?? '') . ' POWAS ' . ($this->powas->phase ?? '') . '"'];
        $rows[] = ['"' . $bookLabel . '"'];
        $rows[] = ['"From: ' . $this->dateFrom . ' To: ' . $this->dateTo . '"'];
        $rows[] = [];

        if ($this->selectedBook === 'general_ledger') {
            $rows[] = ['Date', 'JE No.', 'OR No.', 'Description', 'Debit', 'Credit', 'Balance'];
            foreach ($this->entries as $e) {
                if ($e['type'] === 'header') {
                    $rows[] = [];
                    $rows[] = ['Account: ' . $e['account'], '', '', 'Opening Balance', '', '', number_format($e['opening'], 2)];
                } else {
                    $rows[] = [
                        $e['date'],
                        $e['journal_entry_number'],
                        $e['or_number'] ?? '',
                        $e['description'],
                        $e['debit']  !== null ? number_format($e['debit'], 2)  : '',
                        $e['credit'] !== null ? number_format($e['credit'], 2) : '',
                        number_format($e['balance'], 2),
                    ];
                }
            }
        } else {
            $rows[] = ['Date', 'JE No.', 'OR No.', 'Account No.', 'Account Name', 'Description', 'Debit', 'Credit'];
            foreach ($this->entries as $e) {
                $rows[] = [
                    $e['date'],
                    $e['journal_entry_number'],
                    $e['or_number'] ?? '',
                    $e['account_number'],
                    $e['account_name'],
                    $e['description'],
                    $e['debit']  !== null ? number_format($e['debit'], 2)  : '',
                    $e['credit'] !== null ? number_format($e['credit'], 2) : '',
                ];
            }
        }

        $csvContent = '';
        foreach ($rows as $row) {
            $csvContent .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        return view('livewire.accounting.books-of-accounts');
    }
}
