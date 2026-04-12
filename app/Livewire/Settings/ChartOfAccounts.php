<?php

namespace App\Livewire\Settings;

use App\Events\ActionLogger;
use App\Models\ChartOfAccounts as ModelsChartOfAccounts;
use App\Models\PowasApplications;
use App\Models\PowasMembers;
use App\Models\PowasOsLogs;
use App\Models\User;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ChartOfAccounts extends Component
{
    use WithPagination;

    public $comView = 'livewire.settings.chart-of-accounts';

    public $confirmingActionTaken = false;

    public $search = '';
    public $pagination = 10;
    public $accountnumber;
    public $accountname;
    public $alias;
    public $description;
    public $accounttype = '';
    public $normalbalance = '';
    public $isCreatingNew = true;
    public $updatedInputs = [];
    public $actionTaken = '';
    public $affectedAccount = '';

    public $oldValues = [];

    public $tableToggle;

    protected $rules;

    protected $validationAttributes;

    public function __construct()
    {
        $this->validationAttributes = [
            'accountnumber' => 'account number',
            'accountname' => 'account name',
            'alias' => 'alias',
            'accounttype' => 'account type',
            'normalbalance' => 'normal balance',
        ];
    }

    public function confirmActionTaken($actionTaken)
    {
        // $this->validate();
        $this->actionTaken = $actionTaken;
        $this->confirmingActionTaken = true;
    }

    public function mount()
    {
        //
    }

    public function setNormalBalance()
    {
        if ($this->accounttype == "ASSET" || $this->accounttype == "EXPENSE") {
            $this->normalbalance = "DEBIT";
        } elseif ($this->accounttype == "LIABILITY" || $this->accounttype == "EQUITY" || $this->accounttype == "REVENUE") {
            $this->normalbalance = "CREDIT";
        }
    }

    public function cancel()
    {
        $this->resetErrorBag();
        $this->reset([
            'search',
            'accountnumber',
            'alias',
            'accountname',
            'description',
            'accounttype',
            'normalbalance',
            'isCreatingNew',
        ]);
    }

    public function delete(ModelsChartOfAccounts $chartofaccount)
    {
        $chartofaccount->delete();

        $this->dispatch('coaMessage', [
            'message' => 'Account successfully deleted!',
            'position' => 'top-right',
            'messageType' => 'success',
        ]);

        $this->resetErrorBag();
        $this->reset([
            'search',
            'accountnumber',
            'alias',
            'accountname',
            'description',
            'accounttype',
            'normalbalance',
            'isCreatingNew',
        ]);
        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> deleted <b><i>' . $chartofaccount->account_name . '</i></b> account.';
        ActionLogger::dispatch('delete', $log_message, Auth::user()->user_id, 'chart-of-accounts');

        $this->confirmingActionTaken = false;
    }

    public function updatedField()
    {
        $this->reset(['updatedInputs']);

        if ($this->isCreatingNew == false) {
            if ($this->oldValues['account_name'][0] != $this->accountname) {
                $this->updatedInputs['account_name'] = $this->accountname;
            }
            if ($this->oldValues['account_number'][0] != $this->accountnumber) {
                $this->updatedInputs['account_number'] = $this->accountnumber;
            }
            if ($this->oldValues['alias'][0] != $this->alias) {
                $this->updatedInputs['alias'] = $this->alias;
            }
            if ($this->oldValues['description'][0] != $this->description) {
                $this->updatedInputs['description'] = $this->description;
            }
            if ($this->oldValues['account_type'][0] != $this->accounttype) {
                $this->updatedInputs['account_type'] = $this->accounttype;
            }
            if ($this->oldValues['normal_balance'][0] != $this->normalbalance) {
                $this->updatedInputs['normal_balance'] = $this->normalbalance;
            }
        }
    }

    public function aliasPopulate()
    {
        $this->alias = $this->accountname;
    }

    public function saveAccount()
    {
        if ($this->isCreatingNew == true) {
            $this->validate([
                'accountnumber' => ['required', 'integer', Rule::unique('chart_of_accounts', 'account_number')],
                'accountname' => ['required', Rule::unique('chart_of_accounts', 'account_name')],
                'accounttype' => ['required'],
                'normalbalance' => ['required'],
            ]);

            if ($this->alias == "" || $this->alias == null) {
                $this->alias = $this->accountname;
            }

            ModelsChartOfAccounts::create([
                'account_number' => $this->accountnumber,
                'account_name' => strtoupper($this->accountname),
                'alias' => strtoupper($this->alias),
                'description' => $this->description,
                'account_type' => strtoupper($this->accounttype),
                'normal_balance' => strtoupper($this->normalbalance),
            ]);

            $this->dispatch('coaMessage', [
                'message' => 'New account successfully saved!',
                'position' => 'top-right',
                'messageType' => 'success',
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created <b><i>' . strtoupper($this->accountname) . '</i></b> account.';
            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'chart-of-accounts');
        } else {
            $this->validate([
                'accountnumber' => ['required', 'integer', Rule::unique('chart_of_accounts', 'account_number')->ignore($this->accountnumber, 'account_number')],
                'accountname' => ['required', Rule::unique('chart_of_accounts', 'account_name')->ignore($this->accountnumber, 'account_number')],
                'alias' => ['required', Rule::unique('chart_of_accounts', 'alias')->ignore($this->accountnumber, 'account_number')],
                'accounttype' => ['required'],
                'normalbalance' => ['required'],
            ]);

            $this->updatedField();

            if (count($this->updatedInputs) > 0) {
                $selectedAccount = ModelsChartOfAccounts::find($this->accountnumber);

                // dd($this->updatedInputs);

                foreach ($this->updatedInputs as $key => $value) {
                    $selectedAccount->$key = strtoupper($value);

                    $oldValue = $this->oldValues[$key][0];

                    $newValue = $value[0];

                    if (!is_numeric($oldValue)) {
                        $oldValue = '"' . $this->oldValues[$key][0] . '"';
                    }

                    if (!is_numeric($newValue)) {
                        $newValue = '"' . strtoupper($value) . '"';
                    }

                    $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . strtoupper($oldValue) . '</i></b> to <b><i>' . strtoupper($newValue) . '</i></b> in the column <i><u>' . $key . '</u></i> with account number <b>' . $this->oldValues[$key][1] . '</b>.';
                    ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'chart-of-accounts');
                }

                $selectedAccount->save();

                $this->dispatch('coaMessage', [
                    'message' => 'Account successfully updated!',
                    'position' => 'top-right',
                    'messageType' => 'success',
                ]);
            }
        }

        $this->resetErrorBag();
        $this->reset([
            'search',
            'accountnumber',
            'alias',
            'accountname',
            'description',
            'accounttype',
            'normalbalance',
            'isCreatingNew',
        ]);
    }

    public function render()
    {
        $chartsofaccounts = ModelsChartOfAccounts::orderBy('account_number', 'asc')->get();

        if (!$this->search) {
            $accountlist = ModelsChartOfAccounts::orderBy('account_number', 'asc')
                ->get();
        } else {
            $accountlist = ModelsChartOfAccounts::where('account_number', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('account_name', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('alias', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('description', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('account_type', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('normal_balance', 'like', '%' . strtoupper($this->search) . '%')
                ->get();
        }

        return view($this->comView, [
            'accountlist' => $accountlist,
            'chartofaccounts' => $chartsofaccounts,
        ]);
    }

    public function loadAccount(ModelsChartOfAccounts $account)
    {
        $this->accountnumber = $account->account_number;
        $this->accountname = $account->account_name;
        $this->alias = $account->alias;
        $this->description = $account->description;
        $this->accounttype = $account->account_type;
        $this->normalbalance = $account->normal_balance;
        $this->isCreatingNew = false;

        $this->reset([
            'oldValues',
        ]);

        $this->oldValues['account_number'] = [$account->account_number, $account->account_number];
        $this->oldValues['account_name'] = [$account->account_name, $account->account_number];
        $this->oldValues['alias'] = [$account->alias, $account->account_number];
        $this->oldValues['description'] = [$account->description, $account->account_number];
        $this->oldValues['account_type'] = [$account->account_type, $account->account_number];
        $this->oldValues['normal_balance'] = [$account->normal_balance, $account->account_number];
    }

    public function populateFromJSONFile()
    {
        $jsonFile = storage_path('app/chart_of_accounts.json');
        $imported = 0;

        // $user_id = Auth::user()->user_id;
        // $currentUser = User::find($user_id);

        // if ($currentUser->hasRole('admin')) {
        //     $powasMembers = DB::table('powas_members')
        //         ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
        //         ->get();
        // } else {
        //     $powasMembers = DB::table('powas_members')
        //         ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
        //         ->where('powas_applications.powas_id', Auth::user()->powas_id)
        //         ->get();
        // }

        // dd($powasMembers);


        if (file_exists($jsonFile)) {
            $jsonData = json_decode(file_get_contents($jsonFile), true);

            foreach ($jsonData as $key => $value) {
                $accountNumberExists = ModelsChartOfAccounts::where('account_name', $key)->exists();
                $accountNameExists = ModelsChartOfAccounts::where('account_name', $value['account_name'])->exists();

                if ($accountNumberExists <= 0 && $accountNameExists <= 0) {
                    ModelsChartOfAccounts::create([
                        'account_number' => $key,
                        'account_name' => $value['account_name'],
                        'alias' => $value['alias'],
                        'description' => $value['description'],
                        'account_type' => $value['account_type'],
                        'normal_balance' => $value['normal_balance'],
                    ]);

                    $imported++;


                    $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created <b><i>' . strtoupper($value['account_name']) . '</i></b> account.';
                    ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'chart-of-accounts');
                }
            }

            $this->dispatch('coaMessage', [
                'message' => $imported . ' accounts successfully imported!',
                'messageType' => 'success',
                'position' => 'top-right',
            ]);
        } else {
            return abort(404);
        }
    }
}
