<?php

namespace App\Livewire\Transactions;

use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BankList extends Component
{
    use WithPagination;
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
    public $newBeginningBalances = [];
    public $baseBalances;
    public $baseBalancesDate;
    public $chartOfAccount;
    public $debits = [];
    public $credits = [];
    public $accountTypes = [];

    protected $pageName = 'revenues-list';

    public function mount($powasID, $powas)
    {
        $this->powas = $powas;
        $this->powasID = $powasID;

        $this->baseBalances = Storage::json('beginning_balances/' . $powasID . '.json');

        $this->baseBalancesDate = Carbon::parse(array_keys($this->baseBalances)[0])->format('Y-m-d');

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

    public function getNewBeginningBalances()
    {
        $this->reset([
            'newBeginningBalances',
            'debits',
            'credits',
        ]);

        $this->chartOfAccount = ChartOfAccounts::all();

        foreach ($this->chartOfAccount as $account_number => $account) {
            $this->newBeginningBalances[$account->account_number] = $this->getBalance($account->account_number);
        }
    }

    public function fetchData2()
    {
        $date = Carbon::createFromFormat('F Y', $this->selectedMonthYear);

        $this->reset([
            'totalDebit',
            'totalCredit',
            'transactionsList',
        ]);

        $journalEntryNumbers = Transactions::whereYear('transaction_date', $date->year)
            ->whereMonth('transaction_date', $date->month)
            ->where('powas_id', $this->powasID)
            ->where('account_number', '102')
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
                if ($value->account_number == '102') {
                    $this->transactionsList[$journalEntryNumber->journal_entry_number] = $transaction;
                }
            }
        }
    }

    public function getBalance($accountNumber)
    {
        $balance = $this->baseBalances[$this->baseBalancesDate][$accountNumber];

        $currentMonth = Carbon::parse($this->selectedMonthYear)->subDay()->format('Y-m-d');

        if ($currentMonth < $this->baseBalancesDate) {
            $currentMonth = $this->baseBalancesDate;
        }

        $transactions = Transactions::where('account_number', $accountNumber)
            ->where('powas_id', $this->powasID)
            ->whereBetween('transaction_date', [$this->baseBalancesDate, $currentMonth])
            ->get();

        foreach ($transactions as $key => $value) {
            $account = ChartOfAccounts::where('account_number', $value->account_number)->first();

            if ($account->account_type == 'ASSET' || $account->account_type == 'EXPENSE') {
                if ($value->transaction_side == 'DEBIT') {
                    $balance = $balance + $value->amount;
                } elseif ($value->transaction_side == 'CREDIT') {
                    $balance = $balance - $value->amount;
                }
            } elseif ($account->account_type == 'LIABILITY' || $account->account_type == 'EQUITY' || $account->account_type == 'REVENUE') {
                if ($value->transaction_side == 'DEBIT') {
                    $balance = $balance - $value->amount;
                } elseif ($value->transaction_side == 'CREDIT') {
                    $balance = $balance + $value->amount;
                }
            }
        }

        return $balance;
    }

    public function render()
    {
        return view('livewire.transactions.bank-list');
    }
}
