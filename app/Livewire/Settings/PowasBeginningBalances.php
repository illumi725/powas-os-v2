<?php

namespace App\Livewire\Settings;

use App\Events\ActionLogger;
use App\Models\ChartOfAccounts;
use App\Models\Powas;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PowasBeginningBalances extends Component
{
    public $powasID;
    public $powas;
    public $balances = [];
    public $chartOfAccounts;
    public $balanceDate;
    public $beginningBalances;
    public $jsonFile;
    public $lockFileExists = false;
    public $showingConfirmSaveModal = false;

    protected $rules = [
        'balances.*' => 'required',
    ];

    public function mount($powas_id)
    {
        $this->powasID = $powas_id;
        $this->powas = Powas::find($powas_id);

        $this->chartOfAccounts = ChartOfAccounts::all();

        $this->balanceDate = Carbon::now()->format('Y-m-d');

        $folderName = 'beginning_balances';
        $fileName = $this->powasID . '.json';
        $this->jsonFile = $folderName . '/' . $fileName;

        $this->lockFileExists = Storage::exists($this->jsonFile . '.lock');

        if ($this->lockFileExists == false) {
            $this->initJSON();
        } else {
            $this->loadJSONFile();
        }
    }

    public function initJSON()
    {
        $values = [];

        foreach ($this->chartOfAccounts as $account_number => $account) {
            $values[$account->account_number] = $this->getBalance($account->account_number);

            // $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created beginning balance for <b><i>' . $account->account_name . '</i></b> amounting to <b>' . number_format($values[$account->account_number], 2) . '</b>.';

            // ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'powas-coop', $this->powasID);
        }

        $initData[Carbon::parse($this->balanceDate)->format('Y-m-d')] = $values;

        $jsonData = json_encode($initData, JSON_PRETTY_PRINT);

        Storage::put($this->jsonFile, $jsonData);

        $this->loadJSONFile();
    }

    public function getBalance($accountNumber)
    {
        $balance = 0;

        $transactions = Transactions::where('account_number', $accountNumber)
            ->where('powas_id', $this->powasID)
            ->whereDate('transaction_date', '<=', $this->balanceDate)
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

    public function loadJSONFile()
    {
        $this->beginningBalances = Storage::json($this->jsonFile);

        $jsonData = $this->beginningBalances;
        $this->balanceDate = Carbon::parse(array_keys($jsonData)[0])->format('Y-m-d');

        if (isset($this->beginningBalances[$this->balanceDate])) {
            foreach ($jsonData[$this->balanceDate] as $key => $value) {
                $this->balances[$key] = number_format($value, 2, '.', '');
            }
        }
    }

    public function confirmSave()
    {
        $this->validate();
        $this->showingConfirmSaveModal = true;
    }

    public function saveAndLock()
    {
        $values = [];

        $oldValues = [];

        foreach ($this->beginningBalances as $key => $value) {
            foreach ($value as $index => $val) {
                $oldValues[$index] = $val;
            }
        }

        foreach ($this->chartOfAccounts as $account_number => $account) {
            $values[$account->account_number] = number_format($this->balances[$account->account_number], 0, '.', '');

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated beginning balance for <b><i>' . $account->account_name . '</i></b> from <b>' . number_format($oldValues[$account->account_number], 2) . '</b> to <b>' . number_format($values[$account->account_number], 2) . '</b>.';

            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'powas-coop', $this->powasID);
        }

        $initData[Carbon::parse($this->balanceDate)->format('Y-m-d')] = $values;

        $jsonData = json_encode($initData, JSON_PRETTY_PRINT);

        Storage::put($this->jsonFile, $jsonData);

        $this->dispatch('saved', [
            'message' => 'Beginning balances successfully saved!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->showingConfirmSaveModal = false;

        Storage::put($this->jsonFile . '.lock', 'Please do not delete this file!');

        $this->lockFileExists = Storage::exists($this->jsonFile . '.lock');

        $this->loadJSONFile();
    }

    public function render()
    {
        return view('livewire.settings.powas-beginning-balances');
    }
}
