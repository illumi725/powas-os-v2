<?php

namespace App\Livewire\Transactions;

use App\Models\Transactions;
use App\Models\IssuedReceipts;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class TransactionsList extends Component
{
    public $powas;
    public $powasID;
    public $powasMembers;
    public $selectedMonthYear;
    public $noTransactions;
    public $transactions;
    public $monthYear;
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $transactionsList = [];
    public $receiptsList = [];

    protected $pageName = 'journal-entries';

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

    public function fetchData2()
    {
        $date = Carbon::parse($this->selectedMonthYear);

        $this->reset([
            'totalDebit',
            'totalCredit',
            'transactionsList',
        ]);

        $journalEntryNumbers = Transactions::whereYear('transaction_date', $date->year)
            ->whereMonth('transaction_date', $date->month)
            ->where('powas_id', $this->powasID)
            ->orderBy('transaction_date', 'asc')->get('journal_entry_number');

        $journalEntryCounter = 0;

        $journalEntry = '';

        $datePart = $date->format('m');

        foreach ($journalEntryNumbers as $journalEntryNumber) {
            $transaction = Transactions::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('journal_entry_number', $journalEntryNumber->journal_entry_number)
                ->where('powas_id', $this->powasID)
            ->orderByRaw("CASE WHEN transaction_side = 'DEBIT' THEN 1 ELSE 2 END")
                ->orderBy('transaction_date', 'asc')
                ->orderBy('account_number', 'asc')
                ->get();

            $this->transactionsList[$journalEntryNumber->journal_entry_number] = $transaction;
        }

        $allTrxnIDs = [];
        foreach ($this->transactionsList as $je => $transactions) {
            foreach ($transactions as $trxn) {
                $allTrxnIDs[] = $trxn->trxn_id;
            }
        }

        $receipts = IssuedReceipts::whereIn('trxn_id', $allTrxnIDs)->get();

        // Group receipts by journal entry number
        foreach ($receipts as $receipt) {
            foreach ($this->transactionsList as $je => $transactions) {
                foreach ($transactions as $trxn) {
                    if ($trxn->trxn_id == $receipt->trxn_id) {
                        // Store all receipts for this journal entry
                        if (!isset($this->receiptsList[$je])) {
                            $this->receiptsList[$je] = [];
                        }
                        $this->receiptsList[$je][] = $receipt;
                        break 2;
                    }
                }
            }
        }
    }



    public function render()
    {
        return view('livewire.transactions.transactions-list');
    }
}
