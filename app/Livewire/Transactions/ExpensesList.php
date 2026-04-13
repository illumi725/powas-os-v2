<?php

namespace App\Livewire\Transactions;

use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use App\Models\Vouchers;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ExpensesList extends Component
{
    use WithPagination;
    public $powas;
    public $powasID;
    public $powasMembers;
    public $selectedMonthYear;
    public $noTransactions;
    public $transactions;
    public $monthYear;
    public $totalExpenses = 0;
    public $transactionsList = [];
    public $voucherLists = [];

    // Bulk print modal
    public $showingBulkPrintModal = false;
    public $bulkPrintStartDate;
    public $bulkPrintEndDate;
    public $bulkPrintUrl = '';

    protected $pageName = 'expenses-list';

    public function mount($powasID, $powas)
    {
        $this->powas = $powas;
        $this->powasID = $powasID;

        $this->monthYear = Transactions::selectRaw('DATE_FORMAT(transaction_date, "%M %Y") AS month_year, transaction_date')
            ->distinct()
            ->where('powas_id', $this->powasID)
            ->orderByDesc('transaction_date')
            ->get()
            ->pluck('month_year')
            ->unique()
            ->toArray();

        if ($this->monthYear == null || count($this->monthYear) == 0) {
            $this->selectedMonthYear = Carbon::now()->format('F Y');
        } else {
            $this->selectedMonthYear = reset($this->monthYear);
        }

        $this->fetchData2();
    }

    #[On('transaction-added')]
    #[On('transaction-updated')]
    public function reloadList()
    {
        $this->fetchData2();
    }

    public function fetchData2Bak()
    {
        $date = Carbon::createFromFormat('F Y', $this->selectedMonthYear);

        $this->reset([
            'totalExpenses',
            'transactionsList',
        ]);

        $journalEntryNumbers = Transactions::whereYear('transaction_date', $date->year)
            ->whereMonth('transaction_date', $date->month)
            ->where('powas_id', $this->powasID)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('received_from', 'asc')
            ->get('journal_entry_number');

        foreach ($journalEntryNumbers as $journalEntryNumber) {
            $transaction = Transactions::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('journal_entry_number', $journalEntryNumber->journal_entry_number)
                ->where('powas_id', $this->powasID)
                ->where('transaction_side', 'DEBIT')
                ->orderBy('transaction_side', 'asc')
                ->orderBy('transaction_date', 'asc')
                ->orderBy('account_number', 'asc')
                ->get();

            foreach ($transaction as $key => $value) {
                // Removed 207 (CENTRAL FUND) - damayan abolished
                if (ChartOfAccounts::find($value->account_number)->account_type == 'EXPENSE' || $value->account_number == '103' || $value->account_number == '201' || $value->account_number == '202' || $value->account_number == '203' || $value->account_number == '204' || $value->account_number == '205') {
                    $voucherNumber = Vouchers::where('trxn_id', $value->trxn_id)->get();
                    // dd($voucherNumber[0]->voucher_id);
                    $this->transactionsList[$journalEntryNumber->journal_entry_number] = $transaction;
                    $this->voucherLists[$value->trxn_id] = $voucherNumber[0]->voucher_id;
                    }
            }
        }
    }

    public function fetchData2()
    {
        $date = Carbon::createFromFormat('F Y', $this->selectedMonthYear);

        $this->reset([
            'totalExpenses',
            'transactionsList',
        ]);

        // Eager load relationships to avoid N+1 queries
        // 'chartofaccounts' to check account_type
        // 'transactionsvoucher' to get voucher ID
        $transactions = Transactions::with(['chartofaccounts', 'transactionsvoucher'])
            ->whereYear('transaction_date', $date->year)
            ->whereMonth('transaction_date', $date->month)
            ->where('powas_id', $this->powasID)
            ->where('transaction_side', 'DEBIT')
            ->orderBy('transaction_date', 'asc')
            ->orderBy('received_from', 'asc')
            ->orderBy('account_number', 'asc')
            ->get();

        foreach ($transactions as $transaction) {
            // Filter logic: using eager loaded relationship
            $accountType = $transaction->chartofaccounts ? $transaction->chartofaccounts->account_type : null;
            
            // Removed 207 (CENTRAL FUND) - damayan abolished
            $isExpense = $accountType == 'EXPENSE';
            $isSpecialAccount = in_array($transaction->account_number, ['103', '201', '202', '203', '204', '205']);

            if ($isExpense || $isSpecialAccount) {
                // Add to list grouped by Journal Entry Number
                $this->transactionsList[$transaction->journal_entry_number][] = $transaction;

                // Handle voucher: use eager loaded relation
                // transactionsvoucher is HasMany, so we take the first one
                $voucher = $transaction->transactionsvoucher->first();
                
                if ($voucher) {
                    $this->voucherLists[$transaction->trxn_id] = $voucher->voucher_id;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.transactions.expenses-list');
    }

    public function showBulkPrintModal()
    {
        $this->bulkPrintStartDate = '';
        $this->bulkPrintEndDate = '';
        $this->bulkPrintUrl = '';
        $this->showingBulkPrintModal = true;
    }

    public function generateBulkPrintUrl()
    {
        $this->validate([
            'bulkPrintStartDate' => 'required|date',
            'bulkPrintEndDate'   => 'required|date|after_or_equal:bulkPrintStartDate',
        ], [
            'bulkPrintEndDate.after_or_equal' => 'End date must be on or after the start date.',
        ]);

        $this->bulkPrintUrl = route('bulk-print-vouchers', [
            'powasID'   => $this->powasID,
            'startDate' => $this->bulkPrintStartDate,
            'endDate'   => $this->bulkPrintEndDate,
        ]);

        $this->dispatch('open-bulk-print', url: $this->bulkPrintUrl);
    }
}
