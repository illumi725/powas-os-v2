<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Fis extends Component
{
    public $powasID;
    public $powas;
    public $transactionMonth;
    public $monthYear;
    public $selectedMonthYear;
    public $newBeginningBalances = [];
    public $baseBalances;
    public $baseBalancesDate;
    public $chartOfAccount;
    public $debits = [];
    public $credits = [];
    public $accountTypes = [];

    public function mount($powasID, $powas, $transactionMonth)
    {
        $this->powasID = $powasID;
        $this->powas = $powas;
        $this->transactionMonth = $transactionMonth;
        $this->baseBalances = Storage::json('beginning_balances/' . $this->powasID . '.json');

        $this->baseBalancesDate = Carbon::parse(array_keys($this->baseBalances)[0])->format('Y-m-d');

        $this->monthYear = Transactions::selectRaw('DATE_FORMAT(transaction_date, "%M %Y") AS month_year, transaction_date')
            ->distinct()
            ->where('powas_id', $this->powasID)
            ->where('transaction_date', '>', $this->baseBalancesDate)
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

        $this->selectedMonthYear = $this->transactionMonth;

        $this->fetchData();
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
            $this->debits[$account->account_number] = $this->getDebit($account->account_number);
            $this->credits[$account->account_number] = $this->getCredit($account->account_number);
            $this->accountTypes[$account->account_number] = $this->getAccountType($account->account_number);
        }
    }

    public function getAccountType($accountNumber)
    {
        $coa = ChartOfAccounts::find($accountNumber);

        return $account = [
            'account_name' => $coa->account_name,
            'account_type' => $coa->account_type,
        ];
    }

    public function getDebit($accountNumber)
    {
        $currentMonth = Carbon::parse($this->selectedMonthYear)->format('Y-m-d');
        $sumOfDebits = Transactions::where('account_number', $accountNumber)
            ->where('powas_id', $this->powasID)
            ->where('transaction_side', 'DEBIT')
            ->whereBetween('transaction_date', [$currentMonth, Carbon::parse($currentMonth)->addMonth()->subDay()->format('Y-m-d')])
            ->sum('amount');

        return $sumOfDebits;
    }

    public function getCredit($accountNumber)
    {
        $currentMonth = Carbon::parse($this->selectedMonthYear)->format('Y-m-d');
        $sumOfCredits = Transactions::where('account_number', $accountNumber)
            ->where('powas_id', $this->powasID)
            ->where('transaction_side', 'CREDIT')
            ->whereBetween('transaction_date', [$currentMonth, Carbon::parse($currentMonth)->addMonth()->subDay()->format('Y-m-d')])
            ->sum('amount');

        return $sumOfCredits;
    }

    public function getBalance($accountNumber)
    {
        $balance = $this->baseBalances[$this->baseBalancesDate][$accountNumber] ?? 0;

        $currentMonth = Carbon::parse($this->selectedMonthYear)->subDay()->format('Y-m-d');

        if ($currentMonth < $this->baseBalancesDate) {
            $currentMonth = $this->baseBalancesDate;
        }

        $transactions = Transactions::where('account_number', $accountNumber)
            ->where('powas_id', $this->powasID)
            ->where('transaction_date', '>', $this->baseBalancesDate)
            ->where('transaction_date', '<=', $currentMonth)
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

    function convertString($inputString)
    {
        $lowercaseWords = ['in', 'on', 'and', 'of', 'etc.'];

        $convertedString = str_replace('-', ' ', strtolower($inputString));
        $convertedString = str_replace(':', ': ', strtolower($inputString));

        $convertedString = ucwords($convertedString);

        foreach ($lowercaseWords as $word) {
            $convertedString = str_replace(ucwords($word), $word, $convertedString);
        }

        return $convertedString;
    }

    public function fetchData()
    {
        $this->getNewBeginningBalances();
    }



    public function render()
    {
        return view('livewire.accounting.fis');
    }
}
