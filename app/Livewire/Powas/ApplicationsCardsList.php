<?php

namespace App\Livewire\Powas;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\ChartOfAccounts;
use App\Models\IssuedReceipts;
use App\Models\Powas;
use App\Models\PowasApplications;
use App\Models\PowasMembers;
use App\Models\PowasOsLogs;
use App\Models\PowasSettings;
use App\Models\Readings;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationsCardsList extends Component
{
    use WithPagination;
    public $search;
    public $pagination = 10;
    public $selectedapplication;
    public $showingApplicationDetailsModal = false;
    public $confirmingActionTaken = false;
    public $printing = false;
    public $powasinfo;
    public $powasSettings;
    public $age;
    public $days;
    public $actiontaken = '';
    public $rejectreason = '';
    public $verifiedby;
    public $first50;
    public $payment;
    public $trxnID = [];
    public $printIDs = [];
    public $applicationFee;
    public $membershipFee;
    public $receiptNumber;
    public $description;
    public $first50Count;

    public $comView = 'livewire.powas.applications-cards-list';

    public $isExistsEquityCapitalAccount;
    public $isExistsMembershipFeeAccount;
    public $isExistsApplicationFeeAccount;

    public $region = '',
        $province = '',
        $municipality = '',
        $powas = '';

    public $regionlist = [],
        $provincelist = [],
        $municipalitylist = [],
        $powaslist = [];

    public $barlist;

    protected $rules = [
        'actiontaken' => 'required',
    ];

    protected $validationAttributes = [
        'actiontaken' => 'action',
        'rejectreason' => 'reject reason'
    ];

    protected $messages = [
        'actiontaken.required' => 'Please select an action.',
    ];

    public function clearfilter()
    {
        $this->reset([
            'region',
            'province',
            'municipality',
            'powas',
            'provincelist',
            'municipalitylist',
            'powaslist',
            'search',
            'pagination',
        ]);
        $this->resetErrorBag('powas');

        $this->dispatch('alert', [
            'message' => 'All filters cleared!',
            'messageType' => 'info',
            'position' => 'top-right',
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->barlist = storage_path('app/bar_list.json');

        $this->resetErrorBag('powas');

        if (file_exists($this->barlist)) {
            $jsonData = json_decode(file_get_contents($this->barlist), true);

            foreach ($jsonData as $regionID => $regionData) {
                $this->regionlist[$regionID] = $regionData['region_name'];
            }

            $this->loadprovince();
            $this->loadmunicipality();
            $this->loadpowas();
        } else {
            return abort(404);
        }
    }

    public function loadprovince()
    {
        $selectedRegion = $this->region;

        $this->reset([
            'province',
            'municipality',
            'powas',
            'provincelist',
            'municipalitylist',
            'powaslist',
        ]);

        $this->resetErrorBag('powas');

        if (file_exists($this->barlist)) {
            $jsonData = json_decode(file_get_contents($this->barlist), true);

            foreach ($jsonData as $regionID) {
                if ($regionID['region_name'] == $selectedRegion) {
                    foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                        $this->provincelist[] = $provinceID;
                    }
                }
            }

            $this->loadmunicipality();
        } else {
            return abort(404);
        }
    }

    public function loadmunicipality()
    {
        $selectedRegion = $this->region;
        $selectedProvince = $this->province;

        $this->reset([
            'municipality',
            'powas',
            'municipalitylist',
            'powaslist',
        ]);

        $this->resetErrorBag('powas');

        if (file_exists($this->barlist)) {
            $jsonData = json_decode(file_get_contents($this->barlist), true);

            foreach ($jsonData as $regionID) {
                if ($regionID['region_name'] == $selectedRegion) {
                    foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                        if ($provinceID == $selectedProvince) {
                            foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                                $this->municipalitylist[] = $municipalityID;
                            }
                        }
                    }
                }
            }
        } else {
            return abort(404);
        }
    }

    public function loadpowas()
    {
        $this->reset([
            'powas',
            'powaslist',
        ]);

        $powasList = Powas::where('region', $this->region)
            ->where('province', $this->province)
            ->where('municipality', $this->municipality)
            ->orderBy('phase', 'asc')
            ->get();

        if (
            $this->region != '' &&
            $this->province != '' &&
            $this->municipality != ''
        ) {
            foreach ($powasList as $key => $value) {
                $this->powaslist[$value->powas_id] = $value;
            }

            $this->resetErrorBag('powas');
            if (count($this->powaslist) == 0) {
                $this->powas = '';
                $this->addError('powas', 'There is currently no POWAS at ' . $this->municipality . ', ' . $this->province . '!');
            }
        } else {
            $this->resetErrorBag('powas');
            $this->powas = '';
        }
    }

    public function showApplicationDetailsModal(PowasApplications $application)
    {
        $this->selectedapplication = $application;
        $this->powasinfo = Powas::where('powas_id', $application->powas_id)->first();
        $this->powasSettings = PowasSettings::where('powas_id', $this->powasinfo->powas_id)->first();
        $this->age = Carbon::parse($application->birthday)->age;
        $applicationDate = Carbon::parse($application->application_date);
        $this->days = $applicationDate->diffInDays(Carbon::now(), false);
        $this->payment = $this->powasSettings->membership_fee + $this->powasSettings->application_fee;

        $this->isExistsEquityCapitalAccount = ChartOfAccounts::where('account_type', 'EQUITY')->where('account_name', 'LIKE', '%' . 'CAPITAL' . '%')->count();
        $this->isExistsApplicationFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'APPLICATION' . '%')->count();
        $this->isExistsMembershipFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'MEMBERSHIP' . '%')->count();

        $this->first50Count = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.firstfifty', 'Y')
            ->where('powas_applications.powas_id', $application->powas_id)
            ->orderBy('powas_applications.lastname', 'asc')
            ->count();

        if (User::find($application->by_user_id) != null) {
            $this->verifiedby = User::where('user_id', $application->by_user_id)->first();
        }

        $this->resetErrorBag();
        $this->showingApplicationDetailsModal = true;
        $this->reset('actiontaken');
    }

    public function confirmActionTaken()
    {
        $this->validate();
        if ($this->actiontaken == 'REJECT') {
            $this->validate([
                'rejectreason' => 'required',
            ]);
        }
        if ($this->actiontaken == 'APPROVE') {
            $this->validate([
                'payment' => 'required|integer',
            ]);

            if ($this->first50 == 1) {
                if ($this->payment != $this->powasSettings->first_50_fee) {
                    $this->addError('payment', 'First 50 Payment must be ' . $this->powasSettings->first_50_fee + 0 . '!');
                    return;
                } else {
                }
            } else {
                $this->applicationFee = $this->payment - $this->powasSettings->membership_fee;
                $this->membershipFee = $this->payment - $this->powasSettings->application_fee;

                $appPlusMemBase = $this->powasSettings->application_fee + $this->powasSettings->membership_fee;
                $inputAppMem = $this->applicationFee + $this->membershipFee;

                if ($inputAppMem != $appPlusMemBase) {
                    $this->addError('payment', 'First 50 Payment must be ' . $appPlusMemBase . '!');
                    return;
                }
            }
        }
        $this->reset([
            'confirmingActionTaken',
        ]);

        $this->confirmingActionTaken = true;
    }

    public function takeaction($applicationid)
    {
        $this->validate();

        $dateNow = now();
        $cashOnHandAccountNumber = ChartOfAccounts::where('account_type', 'ASSET')->where('account_number', '101')->first();

        $toModify = PowasApplications::find($applicationid);
        $oldValue = '"' . $toModify->application_status . '"';

        if ($toModify->add_mode == 'default') {
            $dateToSave = date_format($dateNow, 'Y-m-d');
        } else {
            $dateToSave = $toModify->application_date;
        }

        switch ($this->actiontaken) {
            case 'VERIFY':
                $toModify->update([
                    'application_status' => strtoupper('VERIFIED'),
                    'by_user_id' => Auth::user()->user_id,
                ]);

                $newValue = '"VERIFIED"';

                $this->resetErrorBag();
                $this->dispatch('alert', [
                    'message' => 'POWAS application \'' . $applicationid . '\' successfully verified!',
                    'messageType' => 'success',
                    'position' => 'top-right',
                ]);
                break;

            case 'APPROVE':
                $this->validate([
                    'payment' => 'required|numeric',
                ]);

                if ($this->first50 == 1) {
                    if ($this->isExistsEquityCapitalAccount <= 0) {
                        $this->dispatch('alert', [
                            'message' => 'Capital Account of EQUITY Account Type is not yet present in the Chart of Accounts!',
                            'messageType' => 'error',
                            'position' => 'top-right'
                        ]);

                        return;
                    } else {
                        $toModify->update([
                            'application_status' => strtoupper('APPROVED'),
                            'by_user_id' => Auth::user()->user_id,
                        ]);

                        $newValue = '"APPROVED"';

                        $memberID = $toModify->powas_id . '-' . rand(1000, 9999);

                        $equityCapitalAccountNumber = ChartOfAccounts::where('account_type', 'EQUITY')->where('account_name', 'LIKE', '%' . 'CAPITAL' . '%')->first();

                        PowasMembers::create([
                            'member_id' => $memberID,
                            'application_id' => $applicationid,
                            'membership_date' => $dateToSave,
                            'firstfifty' => 'Y',
                        ]);

                        $newlyCreated = PowasMembers::find($memberID);

                        $newMember = $newlyCreated->applicationinfo->lastname . ', ' . $newlyCreated->applicationinfo->firstname;

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created member account for <b><i>' . strtoupper($newMember) . '</i></b> with account number <b>' . $memberID . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'members', $this->selectedapplication->powas_id);

                        $this->reset([
                            'trxnID',
                            'printIDs',
                        ]);

                        $journalEntryNumber = CustomNumberFactory::journalEntryNumber($this->selectedapplication->powas_id, $dateToSave);

                        // Transaction for Equity Account
                        $trxnNewID = CustomNumberFactory::getRandomID();

                        $this->description = 'Equity Capital from ' . $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename;

                        Transactions::create([
                            'trxn_id' => $trxnNewID,
                            'account_number' => $equityCapitalAccountNumber->account_number,
                            'description' => $this->description,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $this->payment,
                            'transaction_side' => $equityCapitalAccountNumber->normal_balance,
                            'received_from' => $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename,
                            'member_id' => $memberID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $this->trxnID[] = $trxnNewID;

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($equityCapitalAccountNumber->account_name) . '</i></b> with description <b>"' . $this->description . ' ' . $this->selectedapplication->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->payment, 2) . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->selectedapplication->powas_id);

                        // Transaction for Cash Account
                        $trxnNewID = CustomNumberFactory::getRandomID();

                        $this->description = 'Cash received from ' . $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename . ' for Equity Capital';

                        Transactions::create([
                            'trxn_id' => $trxnNewID,
                            'account_number' => $cashOnHandAccountNumber->account_number,
                            'description' => $this->description,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $this->payment,
                            'transaction_side' => $cashOnHandAccountNumber->normal_balance,
                            'received_from' => $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename,
                            'member_id' => $memberID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccountNumber->account_name) . '</i></b> with description <b>"' . $this->description . ' ' . $this->selectedapplication->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->payment, 2) . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->selectedapplication->powas_id);

                        $readingID = CustomNumberFactory::getRandomID();

                        Readings::create([
                            'reading_id' => $readingID,
                            'member_id' => $memberID,
                            'powas_id' => $toModify->powas_id,
                            'recorded_by' => Auth::user()->user_id,
                            'reading' => 0.00,
                            'reading_date' =>  $dateToSave,
                            'reading_count' =>  0,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created reading record for <b><i>' . strtoupper($newMember) . '</i></b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'reading', $this->selectedapplication->powas_id);

                        $printNewID = CustomNumberFactory::getRandomID();

                        $this->receiptNumber = CustomNumberFactory::receipt($this->selectedapplication->powas_id, $dateToSave);

                        IssuedReceipts::create([
                            'print_id' => $printNewID,
                            'receipt_number' => $this->receiptNumber,
                            'trxn_id' => $trxnNewID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $this->printIDs[] = $printNewID;

                        $this->printing = true;
                    }
                } else {
                    if ($this->isExistsApplicationFeeAccount <= 0 || $this->isExistsMembershipFeeAccount <= 0) {
                        if ($this->isExistsApplicationFeeAccount <= 0) {
                            $this->dispatch('alert', [
                                'message' => 'Application Fee Account of REVENUE Account Type is not yet present in the Chart of Accounts!',
                                'messageType' => 'error',
                                'position' => 'top-right'
                            ]);
                        } elseif ($this->isExistsMembershipFeeAccount <= 0) {
                            $this->dispatch('alert', [
                                'message' => 'Membership Fee Account of REVENUE Account Type is not yet present in the Chart of Accounts!',
                                'messageType' => 'error',
                                'position' => 'top-right'
                            ]);
                        }

                        return;
                    } else {
                        $toModify->update([
                            'application_status' => strtoupper('APPROVED'),
                            'by_user_id' => Auth::user()->user_id,
                        ]);

                        $newValue = '"APPROVED"';

                        $memberID = $toModify->powas_id . '-' . rand(1000, 9999);

                        $applicationFeeAccountNumber = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'APPLICATION FEE' . '%')->first();

                        $membershipFeeAccountNumber = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'MEMBERSHIP FEE' . '%')->first();

                        PowasMembers::create([
                            'member_id' => $memberID,
                            'application_id' => $applicationid,
                            'membership_date' => $dateToSave,
                        ]);

                        $newlyCreated = PowasMembers::find($memberID);

                        $newMember = $newlyCreated->applicationinfo->lastname . ', ' . $newlyCreated->applicationinfo->firstname;

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created member account for <b><i>' . strtoupper($newMember) . '</i></b> with account number <b>' . $memberID . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'members', $this->selectedapplication->powas_id);

                        $this->reset([
                            'trxnID',
                            'printIDs',
                        ]);

                        // Reading
                        $readingID = CustomNumberFactory::getRandomID();

                        Readings::create([
                            'reading_id' => $readingID,
                            'member_id' => $memberID,
                            'powas_id' => $toModify->powas_id,
                            'recorded_by' => Auth::user()->user_id,
                            'reading' => 0.00,
                            'reading_date' =>  $dateToSave,
                            'reading_count' =>  0,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created reading record for <b><i>' . strtoupper($newMember) . '</i></b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'reading', $this->selectedapplication->powas_id);

                        $journalEntryNumber = CustomNumberFactory::journalEntryNumber($this->selectedapplication->powas_id, $dateToSave);

                        // Transaction for Application Fee
                        $trxnNewID = CustomNumberFactory::getRandomID();

                        $this->description = 'Application Fee received from ' . $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename;

                        Transactions::create([
                            'trxn_id' => $trxnNewID,
                            'account_number' => $applicationFeeAccountNumber->account_number,
                            'description' => $this->description,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $this->applicationFee,
                            'transaction_side' => $applicationFeeAccountNumber->normal_balance,
                            'received_from' => $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename,
                            'member_id' => $memberID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($applicationFeeAccountNumber->account_name) . '</i></b> with description <b>"' . $this->description . ' ' . $this->selectedapplication->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->applicationFee, 2) . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->selectedapplication->powas_id);

                        $this->trxnID[] = $trxnNewID;

                        // Transaction for Cash Account
                        $trxnNewID = CustomNumberFactory::getRandomID();

                        $this->description = 'Cash received from ' . $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename . ' for Application Fee';

                        Transactions::create([
                            'trxn_id' => $trxnNewID,
                            'account_number' => $cashOnHandAccountNumber->account_number,
                            'description' => $this->description,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $this->applicationFee,
                            'transaction_side' => $cashOnHandAccountNumber->normal_balance,
                            'received_from' => $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename,
                            'member_id' => $memberID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccountNumber->account_name) . '</i></b> with description <b>"' . $this->description . ' ' . $this->selectedapplication->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->applicationFee, 2) . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->selectedapplication->powas_id);

                        $printNewID = CustomNumberFactory::getRandomID();

                        $this->receiptNumber = CustomNumberFactory::receipt($this->selectedapplication->powas_id, $dateToSave);

                        IssuedReceipts::create([
                            'print_id' => $printNewID,
                            'receipt_number' => $this->receiptNumber,
                            'trxn_id' => $trxnNewID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $this->printIDs[] = $printNewID;

                        // Transaction for Membership Fee
                        $trxnNewID = CustomNumberFactory::getRandomID();

                        $this->description = 'Membership Fee from ' . $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename;

                        Transactions::create([
                            'trxn_id' => $trxnNewID,
                            'account_number' => $membershipFeeAccountNumber->account_number,
                            'description' => $this->description,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $this->membershipFee,
                            'transaction_side' => $membershipFeeAccountNumber->normal_balance,
                            'received_from' => $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename,
                            'member_id' => $memberID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($membershipFeeAccountNumber->account_name) . '</i></b> with description <b>"' . $this->description . ' ' . $this->selectedapplication->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->membershipFee, 2) . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->selectedapplication->powas_id);

                        $this->trxnID[] = $trxnNewID;

                        // Transaction for Cash Account
                        $trxnNewID = CustomNumberFactory::getRandomID();

                        $this->description = 'Cash received from ' . $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename . ' for Membership Fee';

                        Transactions::create([
                            'trxn_id' => $trxnNewID,
                            'account_number' => $cashOnHandAccountNumber->account_number,
                            'description' => $this->description,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $this->membershipFee,
                            'transaction_side' => $cashOnHandAccountNumber->normal_balance,
                            'received_from' => $this->selectedapplication->lastname . ', ' . $this->selectedapplication->firstname . ' ' . $this->selectedapplication->middlename,
                            'member_id' => $memberID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccountNumber->account_name) . '</i></b> with description <b>"' . $this->description . ' ' . $this->selectedapplication->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->membershipFee, 2) . '</b>.';

                        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->selectedapplication->powas_id);

                        $printNewID = CustomNumberFactory::getRandomID();

                        IssuedReceipts::create([
                            'print_id' => $printNewID,
                            'receipt_number' => $this->receiptNumber,
                            'trxn_id' => $trxnNewID,
                            'powas_id' => $this->selectedapplication->powas_id,
                            'transaction_date' => $dateToSave,
                        ]);

                        $this->printIDs[] = $printNewID;

                        $this->printing = true;
                    }
                }

                $this->resetErrorBag();

                $this->reset([
                    'first50',
                ]);

                $this->dispatch('alert', [
                    'message' => 'POWAS application \'' . $applicationid . '\' successfully approved!',
                    'messageType' => 'success',
                    'position' => 'top-right',
                ]);
                break;

            case 'REJECT':
                $oldReject = '"' . $toModify->reject_reason . '"';

                $toModify->update([
                    'application_status' => strtoupper('REJECTED'),
                    'reject_reason' => $this->rejectreason,
                    'by_user_id' => Auth::user()->user_id,
                ]);

                $newReject = '"' . $this->rejectreason . '"';

                $newValue = '"REJECTED"';

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . strtoupper($oldReject) . '</i></b> to <b><i>' . strtoupper($newReject) . '</i></b> in the column <i><u>reject_reason</u></i> with application reference number <b>' . $applicationid . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'applications', $this->selectedapplication->powas_id);

                $this->resetErrorBag();
                $this->dispatch('alert', [
                    'message' => 'POWAS application \'' . $applicationid . '\' successfully rejected!',
                    'messageType' => 'success',
                    'position' => 'top-right',
                ]);
                break;
        }

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . strtoupper($oldValue) . '</i></b> to <b><i>' . strtoupper($newValue) . '</i></b> in the column <i><u>application_status</u></i> with application reference number <b>' . $applicationid . '</b>.';

        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'applications', $this->selectedapplication->powas_id);

        $this->showingApplicationDetailsModal = false;

        $this->confirmingActionTaken = false;

        // $this->redirect('applications');
    }

    public function setPayment()
    {
        if ($this->first50 == 1) {
            $this->payment = $this->powasSettings->first_50_fee + 0;
        } else {
            $this->payment = $this->powasSettings->membership_fee + $this->powasSettings->application_fee;
        }
    }

    public function render()
    {
        $user_id = Auth::user()->user_id;
        $currentuser = User::find($user_id);

        if (!$this->search) {
            if ($this->powas == '') {
                if ($currentuser->hasRole('admin')) {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->where('powas_applications.application_status', 'PENDING')
                        ->orWhere('powas_applications.application_status', 'VERIFIED')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->paginate(12);
                } else {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->where('powas_applications.application_status', 'PENDING')
                        ->where('powas_applications.powas_id', $currentuser->powas_id)
                        ->orWhere('powas_applications.application_status', 'VERIFIED')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->paginate(12);
                }
            } else {
                if ($currentuser->hasRole('admin')) {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->where('powas.powas_id', $this->powas)
                        ->where('powas_applications.application_status', 'PENDING')
                        ->orWhere('powas_applications.application_status', 'VERIFIED')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->paginate(12);
                } else {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->where('powas.powas_id', $this->powas)
                        ->where('powas_applications.powas_id', $currentuser->powas_id)
                        ->where('powas_applications.application_status', 'PENDING')
                        ->orWhere('powas_applications.application_status', 'VERIFIED')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->paginate(12);
                }
            }
        } else {
            if ($this->powas == '') {
                if ($currentuser->hasRole('admin')) {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->where('powas_applications.application_id', 'like', '%' . strtoupper($this->search) . '%')
                        ->where('powas_applications.application_status', 'PENDING')
                        ->orWhere('powas_applications.application_status', 'VERIFIED')
                        ->orWhere('powas_applications.application_status', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.powas_id', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.lastname', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.firstname', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.middlename', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.barangay', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.municipality', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.province', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.region', 'like', '%' . strtoupper($this->search) . '%')
                        ->paginate(12);
                } else {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->where('powas_applications.powas_id', $currentuser->powas_id)
                        ->where(function ($query) {
                            $query->where('powas_applications.application_id', 'like', '%' . strtoupper($this->search) . '%')
                                ->where('powas_applications.application_status', 'PENDING')
                                ->orWhere('powas_applications.application_status', 'VERIFIED')
                                ->orWhere('powas_applications.application_status', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.powas_id', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.lastname', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.firstname', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.middlename', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.barangay', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.municipality', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.province', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.region', 'like', '%' . strtoupper($this->search) . '%');
                        })
                        ->paginate(12);
                }
            } else {
                if ($currentuser->hasRole('admin')) {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->where('powas.powas_id', $this->powas)
                        ->where(function ($query) {
                            $query->where('powas_applications.application_id', 'like', '%' . strtoupper($this->search) . '%')
                                ->where('powas_applications.application_status', 'PENDING')
                                ->orWhere('powas_applications.application_status', 'VERIFIED')
                                ->orWhere('powas_applications.application_status', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.powas_id', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.lastname', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.firstname', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.middlename', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.barangay', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.municipality', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.province', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.region', 'like', '%' . strtoupper($this->search) . '%');
                        })
                        ->paginate(12);
                } else {
                    $applications = PowasApplications::join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id')
                        ->orderBy('powas_applications.lastname', 'asc')
                        ->where('powas.powas_id', $this->powas)
                        ->where('powas_applications.powas_id', $currentuser->powas_id)
                        ->where(function ($query) {
                            $query->where('powas_applications.application_id', 'like', '%' . strtoupper($this->search) . '%')
                                ->where('powas_applications.application_status', 'PENDING')
                                ->orWhere('powas_applications.application_status', 'VERIFIED')
                                ->orWhere('powas_applications.application_status', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.powas_id', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.lastname', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.firstname', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.middlename', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.barangay', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.municipality', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.province', 'like', '%' . strtoupper($this->search) . '%')
                                ->orWhere('powas_applications.region', 'like', '%' . strtoupper($this->search) . '%');
                        })
                        ->paginate(12);
                }
            }
        }
        return view($this->comView, [
            'applications' => $applications,
        ]);
    }
}
