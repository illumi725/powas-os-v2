<?php

namespace App\Livewire\Powas;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Imports\ApplicationsImport;
use App\Imports\MembersImport;
use App\Models\Billings;
use App\Models\ChartOfAccounts;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasOsLogs;
use App\Models\PowasSettings;
use App\Models\MeterChange;
use App\Helpers\MeterHelper;
use App\Models\Readings;
use App\Models\Transactions;
use App\Models\User;
use App\Rules\CheckExcelHeader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class MembersList extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search;
    public $pagination = 12;
    public $showingAddMemberModal = false;
    public $showingPOWASSelectorModal = false;
    public $showingExcelImportModal = false;
    public $showingImportDataModal = false;
    public $showingConfirmPrintModal = false;
    public $showingPOWASSelectorModalForManual = false;
    public $showingAddReadingModal = false;
    public $showingConfirmSaveModal = false;
    public $showingPenaltyDiscountModal = false;
    public $isMinimum = false;
    public $selectedPOWAS;
    public $discountType = 'amount';
    public $discount;
    public $penalty;
    public $discountLabel;
    public $toPrintBilling = [];
    public $regen;
    public $numberOfMembers;
    public $excelFile = null;
    public $importCollection = [];
    public $selectedMemberID;
    public $savedCount = 0;
    public $previousReading;
    public $presentReading;
    public $readingIDs;
    public $readingCounts;
    public $readingDate;
    public $previousReadingDate;
    public $dateNow;
    public $newDate;
    public $powasID;
    public $isBillPrint = true;
    public $isAutoPrint = false;
    public $powasSettings;
    public $transactionStatus = 'NO';
    public $billingMonth;
    public $readingCurrent;
    public $isInitialReading = false;

    public $validReadings = [];

    public $isExistsEquityCapitalAccount;
    public $isExistsMembershipFeeAccount;
    public $isExistsApplicationFeeAccount;

    public $errorList = [];

    public $comView = 'livewire.powas.members-cards-list';

    public $region = '',
        $province = '',
        $municipality = '',
        $powas = '';

    public $regionlist = [],
        $provincelist = [],
        $municipalitylist = [],
        $powaslist = [];

    public $barlist;

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
        $this->resetPage();

        $this->dispatch('alert', [
            'message' => 'All filters cleared!',
            'messageType' => 'info',
            'position' => 'top-right',
        ]);
    }

    public function confirmSave()
    {
        if (isset($this->previousReadingDate)) {
            $this->validate([
                'readingDate' => ['required', 'after:previousReadingDate'],
                'readingCounts' => ['required'],
                'previousReading' => ['required', 'lte:presentReading'],
                'presentReading' => ['required', 'gte:previousReading'],
            ]);
        } else {
            $this->validate([
                'readingDate' => ['required'],
                'readingCounts' => ['required', 'numeric', 'min:0'],
                'previousReading' => ['required', 'lte:presentReading'],
                'presentReading' => ['required', 'gte:previousReading'],
            ]);
        }

        $this->showingConfirmSaveModal = true;
    }

    public function saveReading($readingID, $memberID)
    {
        $importer = User::find(Auth::user()->user_id);
        $member = PowasMembers::find($memberID);

        $memberName = $member->applicationinfo->lastname . ', ' . $member->applicationinfo->firstname;

        $isExists = Readings::where('reading_id', $readingID)->exists();

        if ($isExists == false) {
            Readings::create([
                'reading_id' => $readingID,
                'member_id' => $memberID,
                'powas_id' => $this->powasID,
                'recorded_by' => Auth::user()->user_id,
                'reading' => $this->presentReading,
                'reading_date' => $this->readingDate,
                'reading_count' => $this->readingCounts,
            ]);

            $log_message = '<b><u>' . $importer->lastname . ', ' . $importer->firstname . '</u></b> created reading record for <b><i>' . strtoupper($memberName) . '</i></b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'reading', $this->powasID);

            $this->regen = 'false';
        } else {
            $readingToUpdate = Readings::where('reading_id', $readingID)->first();

            if ($readingToUpdate->reading != $this->presentReading) {
                $readingToUpdate->reading = $this->presentReading;
                $readingToUpdate->save();

                $log_message = '<b><u>' . $importer->lastname . ', ' . $importer->firstname . '</u></b> updated reading record for <b><i>' . strtoupper($memberName) . '</i></b> with reading ID <b>' . $readingID . '</b>';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'reading', $this->powasID);
            }

            if ($readingToUpdate->reading_date != $this->readingDate) {
                $readingToUpdate->reading_date = $this->readingDate;
                $readingToUpdate->save();

                $log_message = '<b><u>' . $importer->lastname . ', ' . $importer->firstname . '</u></b> updated reading date for <b><i>' . strtoupper($memberName) . '</i></b> with reading ID <b>' . $readingID . '</b>';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'reading', $this->powasID);
            }

            $this->regen = 'true';
        }

        $this->dispatch('alert', [
            'message' => 'Reading successfully saved!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->showingAddReadingModal = false;
        $this->showingConfirmSaveModal = false;
        $this->getBillingInfoToSave();

        $this->readingCurrent = Readings::where('powas_id', $this->powasID)
            ->where('member_id', $this->selectedMemberID)
            ->orderByDesc('reading_date')
            ->first();

        $currentReadingCount = Readings::where('powas_id', $this->powasID)
            ->where('member_id', $this->selectedMemberID)
            ->orderByDesc('reading_date')->get();

        if (($this->readingCurrent->reading_count > 1) || (count($currentReadingCount) > 1)) {
            $this->showingPenaltyDiscountModal = true;
        }
    }

    public function getTransactionStatus()
    {
        $member = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $this->selectedMemberID)->first();

        $presentReadingQuery = Readings::where('member_id', $member->member_id)
            ->orderBy('reading_date', 'desc')
            ->first();

        $currentBillingMonth = Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->format('Y-m-01');

        $daysElapsed = Carbon::now()->diffInDays(Carbon::parse($presentReadingQuery->reading_date));

        if ($daysElapsed >= 15) {
            $currentBillingMonth = Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->addMonth()->format('Y-m-01');
        }

        $transacted = 'NO';

        $billsReceivablesAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

        $billing = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('billings.member_id', $member->member_id)
            ->where('billings.billing_month', $currentBillingMonth)->first();

        if ($billing != null) {
            $isTransacted = Transactions::where('transaction_side', 'CREDIT')
                ->where('paid_to', $billing->billing_id)
                ->where('account_number', $billsReceivablesAccount->account_number)
                ->exists();

            if ($isTransacted == true) {
                $transacted = 'YES';
            }
        }

        return $transacted;
    }

    public function showAddReadingModal($memberID)
    {
        $this->reset([
            'selectedMemberID',
            'previousReadingDate',
            'isInitialReading',
            'previousReading',
            'presentReading',
            'readingIDs',
            'readingCounts',
            'transactionStatus',
        ]);

        $this->resetErrorBag();

        $this->selectedMemberID = $memberID;

        $member = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $memberID)
            ->first();

        $this->powasID = $member->powas_id;

        $this->powasSettings = PowasSettings::where('powas_id', $this->powasID)->first();

        if ($member->member_status != 'ACTIVE') {
            $this->dispatch('alert', [
                'message' => 'Cannot add reading record because member status is ' . $member->member_status . '.',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);

            return;
        }

        $readingRecord = Readings::where('member_id', $member->member_id)
            ->orderBy('reading_count', 'desc')
            ->first();

        if ($readingRecord != null) {
            $this->transactionStatus = $this->getTransactionStatus();

            $readingID = CustomNumberFactory::getRandomID();
            $this->readingDate = Carbon::now()->format('Y-m-d');

            if ($readingRecord->count() == 0) {
                $this->previousReading = '';
                $this->presentReading = '';
                $this->readingIDs = $readingID;
                $this->readingCounts = $readingRecord->reading_count + 1;
            } elseif ($readingRecord->reading_count == 0) {
                $this->previousReading = $readingRecord->reading;
                $this->presentReading = '';
                $this->readingIDs = $readingID;
                $this->readingCounts = $readingRecord->reading_count + 1;
            } else {
                $previousReadingRecord = Readings::where('member_id', $member->member_id)
                    ->orderBy('reading_count', 'desc')
                    ->offset(1)
                    ->first();
                $lastReadingDate = Carbon::parse($readingRecord->reading_date);
                $elapsedDays = $lastReadingDate->diffInDays(Carbon::now(), false);

                // if ($elapsedDays <= 20) {
                //     if ($readingRecord->count() == 1) {
                //         $this->previousReading = 0;
                //         $this->presentReading = $readingRecord->reading;
                //         $this->readingIDs = $readingRecord->reading_id;
                //         $this->readingCounts = $readingRecord->reading_count;
                //         $this->readingDate = $readingRecord->reading_date;
                //         $this->previousReadingDate = $readingRecord->reading_date;
                //         $this->savedCount++;
                //     } else {

                //         $this->previousReading = $previousReadingRecord->reading;
                //         $this->presentReading = $readingRecord->reading;
                //         $this->readingIDs = $readingRecord->reading_id;
                //         $this->readingCounts = $readingRecord->reading_count;
                //         $this->readingDate = $readingRecord->reading_date;
                //         $this->previousReadingDate = $previousReadingRecord->reading_date;
                //         $this->savedCount++;
                //         $this->billingMonth = Carbon::parse($readingRecord->reading_date)->subDays(15)->format('F Y');
                //     }
                // } else {
                $this->previousReading = $readingRecord->reading;
                $this->presentReading = '';
                $this->readingIDs = $readingID;
                $this->readingCounts = $readingRecord->reading_count + 1;
                $this->previousReadingDate = $readingRecord->reading_date;
                // }
            }
        } else {
            $this->isInitialReading = true;
            $this->readingDate = Carbon::now()->format('Y-m-d');
            $this->readingCounts = 0;
        }
        $this->showingAddReadingModal = true;
    }

    public function getReadingStatus($memberID)
    {
        $member = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $memberID)
            ->first();

        $this->powasID = $member->powas_id;

        $this->readingDate = Carbon::now()->format('Y-m-d');

        $readingRecord = Readings::where('member_id', $member->member_id)
            ->orderBy('reading_count', 'desc')
            ->first();

        if ($readingRecord == null) {
            return 'ADD';
        }

        if ($readingRecord->count() == 0) {
            return 'ADD';
        } elseif ($readingRecord->reading_count == 0) {
            return 'ADD';
        } else {
            $lastReadingDate = Carbon::parse($readingRecord->reading_date);
            $elapsedDays = $lastReadingDate->diffInDays(Carbon::now(), false);

            // if ($elapsedDays <= 20) {
            //     if ($readingRecord->count() == 1) {
            //         return 'EDIT';
            //     } else {
            //         return 'EDIT';
            //     }
            // } else {
            return 'ADD';
            // }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showAddMemberModal()
    {
        $this->showingAddMemberModal = true;

        $this->isExistsEquityCapitalAccount = ChartOfAccounts::where('account_type', 'EQUITY')->where('account_name', 'LIKE', '%' . 'CAPITAL' . '%')->count();
        $this->isExistsApplicationFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'APPLICATION' . '%')->count();
        $this->isExistsMembershipFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'MEMBERSHIP' . '%')->count();

        $powasCount = Powas::all()->count();

        if ($this->isExistsEquityCapitalAccount <= 0 || $this->isExistsApplicationFeeAccount <= 0 || $this->isExistsMembershipFeeAccount <= 0 || $powasCount <= 0) {
            if ($this->isExistsEquityCapitalAccount <= 0) {
                $this->errorList[] = '<b><u>Capital Account</u></b> of <i><b>EQUITY</b></i> Account Type is not yet present in the <b>Chart of Accounts</b>.';
            }
            if ($this->isExistsApplicationFeeAccount <= 0) {
                $this->errorList[] = '<b><u>Application Fee Account</u></b> of <i><b>REVENUE</b></i> Account Type is not yet present in the <b>Chart of Accounts</b>.';
            }
            if ($this->isExistsMembershipFeeAccount <= 0) {
                $this->errorList[] = '<b><u>Membership Fee Account</u></b> of <i><b>REVENUE</b></i> Account Type is not yet present in the <b>Chart of Accounts</b>.';
            }
            if ($powasCount <= 0) {
                $this->errorList[] = 'No POWAS information found in the system yet.';
            }

            return;
        }
    }

    public function showExcelImportModal()
    {
        $this->excelFile = null;
        $this->showingAddMemberModal = false;
        $this->showingExcelImportModal = true;
    }

    public function showImportData()
    {
        $headers = [
            'powas_id',
            'user_id',
            'lastname',
            'firstname',
            'middlename',
            'birthday',
            'birthplace',
            'gender',
            'contact_number',
            'civil_status',
            'address1',
            'barangay',
            'municipality',
            'province',
            'region',
            'present_address',
            'family_members',
            'application_status',
            'meter_number',
            'membership_date',
            'firstfifty',
            'land_owner',
            'member_status',
        ];

        $this->validate(['excelFile' => ['required', 'mimes:xlsx', new CheckExcelHeader($headers)]]);
        $this->showingImportDataModal = true;

        try {
            // $collection = (new MembersImport)->toArray($this->excelFile);
            $collection = Excel::toArray(new MembersImport,  $this->excelFile);

            $this->reset(['importCollection']);

            foreach ($collection as $key) {
                $data = $key;
                foreach ($data as $value) {
                    $this->importCollection[] = $value;
                }
            }
        } catch (\Exception $e) {
            $this->excelFile = null;

            $this->dispatch('alert', [
                'message' => 'An error occured while importing the file! Please check for blank cells or invalid data encoded!',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
        }
    }

    public function showPOWASSelectorModalForManual()
    {
        $currentUserID = Auth::user()->user_id;
        $sessionUser = User::find($currentUserID);
        $this->reset(['selectedPOWAS']);
        $this->showingAddMemberModal = false;

        if ($sessionUser->hasRole('admin')) {
            $this->showingPOWASSelectorModalForManual = true;
        } elseif ($sessionUser->hasRole('treasurer|secretary')) {
            $this->addMemberManually($sessionUser->powas_id);
        }
    }

    public function showPOWASSelectorModal()
    {
        $currentUserID = Auth::user()->user_id;
        $sessionUser = User::find($currentUserID);
        $this->reset(['selectedPOWAS']);
        $this->reset(['numberOfMembers']);
        $this->showingAddMemberModal = false;

        if ($sessionUser->hasRole('treasurer|secretary')) {
            $this->selectedPOWAS = $sessionUser->powas_id;
        }

        $this->showingPOWASSelectorModal = true;
    }

    public function createCSVTemplate($powasID)
    {
        $this->validate(['selectedPOWAS' => 'required', 'numberOfMembers' => 'required|numeric'], [], ['selectedPOWAS' => 'POWAS ID', 'numberOfMembers' => 'number of members']);
        $this->showingPOWASSelectorModal = false;
        return redirect()->route('members-csv-template', ['powasID' => $powasID, 'numberOfMembers' => $this->numberOfMembers]);
    }

    public function importExcelFile()
    {
        $this->validate(['excelFile' => 'required|mimes:xlsx']);

        // try {
        // $collection = (new MembersImport)->toArray($this->excelFile);
        $collection = Excel::toArray(new MembersImport,  $this->excelFile);

        $first50Count = 0;

        foreach ($collection as $key) {
            $data = $key;
            foreach ($data as $value) {
                $flag = $value['firstfifty'];
                $powas_id = $value['powas_id'];
                if ($flag == 'Y') {
                    $first50Count++;
                }
            }
        }

        $first50CountFromDB = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.firstfifty', 'Y')
            ->where('powas_applications.powas_id', $powas_id)
            ->orderBy('powas_applications.lastname', 'asc')
            ->count();

        if ($first50Count + $first50CountFromDB > 50) {
            $this->dispatch('alert', [
                'message' => 'First 50 members will exceed with this import! Please check thoroughly before importing!',
                'messageType' => 'warning',
                'position' => 'top-right',
            ]);
        } else {
            Excel::import(new MembersImport(), $this->excelFile);
            $this->excelFile = null;
            $this->dispatch('alert', [
                'message' => 'Excel file successfully imported!',
                'messageType' => 'success',
                'position' => 'top-right',
            ]);
        }
        // } catch (\Exception $e) {
        //     $this->excelFile = null;

        //     $this->dispatch('alert', [
        //         'message' => 'An error occured while importing the file! Please check for blank cells or invalid data encoded!',
        //         'messageType' => 'error',
        //         'position' => 'top-right',
        //     ]);
        // }

        $this->showingExcelImportModal = false;
    }

    public function addMemberManually($powasID)
    {
        return redirect()->route('members.add', ['powasID' => $powasID]);
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

        $this->discount = number_format(0, 2);
        $this->penalty = number_format(0, 2);
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
        $this->resetPage();

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
        $this->resetPage();

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

        $this->resetPage();

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

    public function getBillingInfoToSave()
    {
        $this->reset([
            'toPrintBilling',
        ]);
        $member = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $this->selectedMemberID)->first();

        $readingCount = Readings::where('member_id', $member->member_id)->count();

        if ($readingCount > 1) {
            $presentReadingQuery = Readings::where('member_id', $member->member_id)
                ->orderBy('reading_date', 'desc')
                ->first();
            $previousReadingQuery = Readings::where('member_id', $member->member_id)
                ->orderBy('reading_date', 'desc')
                ->offset(1)->first();

            $isExists = Billings::where('member_id', $member->member_id)
                ->where('billing_month', Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->format('Y-m-01'))->exists();

            if ($isExists == true) {
                $exists = 'YES';

                $billsReceivablesAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

                $billing = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
                    ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                    ->where('billings.member_id', $member->member_id)
                    ->where('billings.billing_month', Carbon::parse($presentReadingQuery->reading_date)->format('Y-m-01'))->first();

                $isTransacted = Transactions::where('transaction_side', 'CREDIT')
                    ->where('paid_to', $billing->billing_id)
                    ->where('account_number', $billsReceivablesAccount->account_number)
                    ->exists();

                $transacted = 'NO';
                if ($isTransacted == true) {
                    $transacted = 'YES';
                }

                if ($this->regen == 'true') {
                    $presR = Readings::where('reading_id', $billing->present_reading_id)->first();
                    $prevR = Readings::where('reading_id', $billing->previous_reading_id)->first();
                    $new_cm_used = MeterHelper::calculateCubicMeterUsed($member->member_id, $prevR, $presR);
                    $new_billing_amount = $new_cm_used * $this->powasSettings->water_rate;

                    $due_date_day = $this->powasSettings->due_date_day;
                    if ($this->powasSettings->due_date_day < 10) {
                        $due_date_day = '0' . $this->powasSettings->due_date_day;
                    }

                    $new_due_date = Carbon::parse($presentReadingQuery->reading_date)->addMonth()->format('Y-m-' . $due_date_day);
                    $new_due_date = Carbon::parse($new_due_date)->format('Y-m-d');

                    $revenuesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'REVENUE' . '%')->first();

                    if ($new_cm_used <= 5) {
                        $new_billing_amount = $this->powasSettings->minimum_payment;
                        $this->isMinimum = true;
                    } else {
                        $new_billing_amount = $new_cm_used * $this->powasSettings->water_rate;
                        $this->isMinimum = false;
                    }

                    if ($billing->cubic_meter_used != $new_cm_used) {
                        $oldValue = $billing->cubic_meter_used;
                        $billing->cubic_meter_used = $new_cm_used;
                        $billing->save();

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_cm_used, 2) . '</i></b> in the column <i><u>' . 'cubic_meter_used' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
                    }

                    if ($billing->billing_amount != $new_billing_amount) {
                        $oldValue = $billing->billing_amount;
                        $billing->billing_amount = $new_billing_amount;
                        $billing->save();

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_billing_amount, 2) . '</i></b> in the column <i><u>' . 'billing_amount' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

                        // For Bills Receivables
                        $billsReceveibleTransaction = Transactions::where('paid_to', $billing->billing_id)
                            ->where('account_number', $billsReceivablesAccount->account_number)->first();

                        $billsReceveibleTransaction->amount = $new_billing_amount;
                        $billsReceveibleTransaction->save();

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated bills receivable amount from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_billing_amount, 2) . '</i></b> in <i><u>' . 'TRANSACTIONS' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

                        // For Revenues
                        $revenuesTransaction = Transactions::where('paid_to', $billing->billing_id)
                            ->where('account_number', $revenuesAccount->account_number)->first();

                        $revenuesTransaction->amount = $new_billing_amount;
                        $revenuesTransaction->save();

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated revenue amount from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_billing_amount, 2) . '</i></b> in <i><u>' . 'TRANSACTIONS' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
                    }

                    if ($billing->due_date != $new_due_date) {
                        $oldValue = $billing->due_date;
                        $billing->due_date = Carbon::parse($new_due_date)->format('Y-m-d');
                        $billing->save();

                        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . $new_due_date . '</i></b> in the column <i><u>' . 'due_date' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
                    }
                }

                if ($billing->cubic_meter_used <= 5) {
                    $billing_amount = $this->powasSettings->minimum_payment;
                    $this->isMinimum = true;
                } else {
                    $billing_amount = $billing->cubic_meter_used * $this->powasSettings->water_rate;
                    $this->isMinimum = false;
                }

                $this->validReadings[$member->member_id] = [
                    'billing_id' => $billing->billing_id,
                    'member_name' => $member->lastname . ', ' . $member->firstname,
                    'present_reading' => Readings::where('reading_id', $billing->present_reading_id)->first()->reading,
                    'previous_reading' => Readings::where('reading_id', $billing->previous_reading_id)->first()->reading,
                    'present_reading_id' => $billing->present_reading_id,
                    'previous_reading_id' => $billing->present_reading_id,
                    'cubic_meter_used' => $billing->cubic_meter_used,
                    'billing_amount' => $billing->billing_amount,
                    'billing_month' => Carbon::parse($billing->billing_month)->format('F Y'),
                    'due_date' => Carbon::parse($billing->due_date)->format('Y-m-d'),
                    'cut_off_start' => Carbon::parse($billing->cut_off_start)->format('Y-m-d'),
                    'cut_off_end' => Carbon::parse($billing->cut_off_start)->format('Y-m-d'),
                    'bill_number' => $billing->bill_number,
                    'print_count' => $billing->print_count,
                    'is_minimum' => $this->isMinimum,
                    'is_exists' => $exists,
                    'isTransacted' => $transacted,
                ];

                $this->discount = number_format($billing->discount_amount, 2);
                $this->penalty = number_format($billing->penalty, 2);

                $this->discountType = 'amount';

                $this->toPrintBilling[] = $billing->billing_id;
            } else {
                $exists = 'NO';

                $billingID = CustomNumberFactory::getRandomID();

                $cubic_meter_used = MeterHelper::calculateCubicMeterUsed($member->member_id, $previousReadingQuery, $presentReadingQuery);

                if ($cubic_meter_used <= 5) {
                    $billing_amount = $this->powasSettings->minimum_payment;
                    $this->isMinimum = true;
                } else {
                    $billing_amount = $cubic_meter_used * $this->powasSettings->water_rate;
                    $this->isMinimum = false;
                }

                $due_date_day = $this->powasSettings->due_date_day;
                if ($this->powasSettings->due_date_day < 10) {
                    $due_date_day = '0' . $this->powasSettings->due_date_day;
                }

                $dueDate = Carbon::parse($presentReadingQuery->reading_date)->addMonth()->format('Y-m-' . $due_date_day);

                $this->validReadings[$member->member_id] = [
                    'billing_id' => $billingID,
                    'member_name' => $member->lastname . ', ' . $member->firstname,
                    'present_reading' => $presentReadingQuery->reading,
                    'previous_reading' => $previousReadingQuery->reading,
                    'present_reading_id' => $presentReadingQuery->reading_id,
                    'previous_reading_id' => $previousReadingQuery->reading_id,
                    'cubic_meter_used' => $cubic_meter_used,
                    'billing_amount' => $billing_amount,
                    'billing_month' => Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->format('F Y'),
                    'due_date' => Carbon::parse($dueDate)->format('Y-m-d'),
                    'cut_off_start' => Carbon::parse($previousReadingQuery->reading_date)->format('Y-m-d'),
                    'cut_off_end' => Carbon::parse($presentReadingQuery->reading_date)->format('Y-m-d'),
                    'bill_number' => $presentReadingQuery->reading_count,
                    'print_count' => 0,
                    'is_minimum' => $this->isMinimum,
                    'is_exists' => $exists,
                    'isTransacted' => 'NO',
                ];

                $this->discount = number_format(0, 2);
                $this->penalty = number_format(0, 2);

                if ($member->land_owner == 'Y') {
                    $this->discountType = 'percent';
                    $this->discount = 100;
                } else {
                    $this->discountType = 'amount';
                }

                $this->toPrintBilling[] = $billingID;
            }
        }
    }

    public function saveBilling($memberID)
    {
        // dd($this->validReadings);

        $this->validate([
            'discount' => 'required|numeric|min:0',
            'penalty' => 'required|numeric|min:0',
        ]);

        $billsReceivablesAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

        $revenuesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'REVENUE' . '%')->first();

        $selectedMember = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $memberID)->first();

        $isExists = Billings::where('member_id', $memberID)
            ->where('billing_month', Carbon::parse($this->validReadings[$memberID]['billing_month'])->format('Y-m-d'))->exists();

        if ($isExists == true) {
            $toUpdateBilling = Billings::where('member_id', $memberID)
                ->where('billing_month', Carbon::parse($this->validReadings[$memberID]['billing_month'])->format('Y-m-d'))->first();

            $oldValue = $toUpdateBilling->discount_amount;

            if ($oldValue != $this->discount) {
                $toUpdateBilling->discount_amount = $this->getDiscountValue($memberID);
                $toUpdateBilling->save();

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($this->discount, 2) . '</i></b> in the column <i><u>' . 'discount_amount' . '</u></i> at <b>' . $toUpdateBilling->billing_id .  '</b> with POWAS ID <b>' . $toUpdateBilling->powas_id . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
            }

            $oldValue = $toUpdateBilling->penalty;

            if ($oldValue != $this->penalty) {
                $toUpdateBilling->penalty = $this->penalty;
                $toUpdateBilling->save();

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($this->penalty, 2) . '</i></b> in the column <i><u>' . 'penalty' . '</u></i> at <b>' . $toUpdateBilling->billing_id .  '</b> with POWAS ID <b>' . $toUpdateBilling->powas_id . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
            }
        } else {
            Billings::create([
                'billing_id' => $this->validReadings[$memberID]['billing_id'],
                'powas_id' => $this->powasID,
                'member_id' => $memberID,
                'recorded_by' => Auth::user()->user_id,
                'previous_reading_id' => $this->validReadings[$memberID]['previous_reading_id'],
                'present_reading_id' => $this->validReadings[$memberID]['present_reading_id'],
                'cubic_meter_used' => $this->validReadings[$memberID]['cubic_meter_used'],
                'billing_amount' => $this->validReadings[$memberID]['billing_amount'],
                'discount_amount' => $this->getDiscountValue($memberID),
                'penalty' => $this->penalty,
                'billing_month' => Carbon::parse($this->validReadings[$memberID]['billing_month'])->format('Y-m-d'),
                'due_date' => $this->validReadings[$memberID]['due_date'],
                'cut_off_start' => $this->validReadings[$memberID]['cut_off_start'],
                'cut_off_end' => $this->validReadings[$memberID]['cut_off_end'],
                'bill_number' => $this->validReadings[$memberID]['bill_number'],
            ]);
            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created billing record for <b><i>' . strtoupper($this->validReadings[$memberID]['member_name']) . '</i></b> for the month of <b>' . $this->validReadings[$memberID]['billing_month'] . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

            $journalEntryNumber = CustomNumberFactory::journalEntryNumber($this->powasID, $this->validReadings[$memberID]['cut_off_end']);

            // For Bills Receivables
            $description = 'Bills Receivable generated for ' . strtoupper($this->validReadings[$memberID]['member_name']);

            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $billsReceivablesAccount->account_number,
                'description' => $description,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->validReadings[$memberID]['billing_amount'],
                'transaction_side' => $billsReceivablesAccount->normal_balance,
                'received_from' => strtoupper($this->validReadings[$memberID]['member_name']),
                'paid_to' => $this->validReadings[$memberID]['billing_id'],
                'member_id' => $memberID,
                'powas_id' => $this->powasID,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->validReadings[$memberID]['cut_off_end'],
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($billsReceivablesAccount->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($this->validReadings[$memberID]['billing_amount'], 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

            // For Revenues
            $description = 'Revenue from ' . strtoupper($this->validReadings[$memberID]['member_name']);

            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $revenuesAccount->account_number,
                'description' => $description,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->validReadings[$memberID]['billing_amount'],
                'transaction_side' => $revenuesAccount->normal_balance,
                'received_from' => strtoupper($this->validReadings[$memberID]['member_name']),
                'paid_to' => $this->validReadings[$memberID]['billing_id'],
                'member_id' => $memberID,
                'powas_id' => $this->powasID,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->validReadings[$memberID]['cut_off_end'],
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($revenuesAccount->account_name) . '</i></b> with description <b>"' . $description . ' ' . $selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->validReadings[$memberID]['billing_amount'], 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
        }

        $this->dispatch('alert', [
            'message' => 'Billing successfully saved!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->showingPenaltyDiscountModal = false;
        if ($this->isBillPrint == true && $this->isAutoPrint == false) {
            $this->showingConfirmPrintModal = true;
        }
    }

    public function getDiscountValue($memberID)
    {
        if ($this->discountType == 'percent') {
            $discountAmount = $this->validReadings[$memberID]['billing_amount'] * ($this->discount / 100);
        } elseif ($this->discountType == 'amount') {
            $discountAmount = $this->discount;
        }

        return round($discountAmount, 0, PHP_ROUND_HALF_UP);
    }

    public function render()
    {
        $powasSelections = Powas::orderBy('region', 'asc')
            ->orderBy('province', 'asc')
            ->orderBy('municipality', 'asc')
            ->orderBy('barangay', 'asc')
            ->orderBy('phase', 'asc')
            ->get();

        $user_id = Auth::user()->user_id;
        $currentuser = User::find($user_id);

        $query = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->join('powas', 'powas.powas_id', '=', 'powas_applications.powas_id');

        if (!$currentuser->hasRole('admin')) {
            $query->where('powas_applications.powas_id', $currentuser->powas_id);
        }

        if ($this->powas != '') {
            $query->where('powas.powas_id', $this->powas);
        }

        if ($this->search) {
            $searchTerm = '%' . strtoupper($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('powas_members.member_id', 'like', $searchTerm)
                    ->orWhere('powas_members.member_status', 'like', $searchTerm)
                    ->orWhere('powas_applications.powas_id', 'like', $searchTerm)
                    ->orWhere('powas_applications.lastname', 'like', $searchTerm)
                    ->orWhere('powas_applications.firstname', 'like', $searchTerm)
                    ->orWhere('powas_applications.middlename', 'like', $searchTerm)
                    ->orWhere('powas_applications.barangay', 'like', $searchTerm)
                    ->orWhere('powas_applications.municipality', 'like', $searchTerm)
                    ->orWhere('powas_applications.province', 'like', $searchTerm)
                    ->orWhere('powas_applications.region', 'like', $searchTerm);
            });
        }

        $members = $query->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->paginate($this->pagination);

        return view($this->comView, [
            'members' => $members,
            'powasSelections' => $powasSelections,
        ]);
    }
}
