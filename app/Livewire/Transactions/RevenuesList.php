<?php

namespace App\Livewire\Transactions;

use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RevenuesList extends Component
{
    use WithPagination;
    public $powas;
    public $powasID;
    public $powasMembers;
    public $selectedMonthYear;
    public $noTransactions;
    public $transactions;
    public $monthYear;
    public $totalRevenues = 0;
    public $transactionsList = [];

    protected $pageName = 'revenues-list';

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
    public function reloadList()
    {
        $this->fetchData2();
    }

    public function fetchData2()
    {
        $date = Carbon::createFromFormat('F Y', $this->selectedMonthYear);

        $this->reset([
            'totalRevenues',
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
                ->orderBy('transaction_side', 'asc')
                ->orderBy('transaction_date', 'asc')
                ->orderBy('account_number', 'asc')
                ->get();

            foreach ($transaction as $key => $value) {
                if (ChartOfAccounts::find($value->account_number)->account_type == 'REVENUE') {
                    $this->transactionsList[$journalEntryNumber->journal_entry_number] = $transaction;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.transactions.revenues-list');
    }
}
