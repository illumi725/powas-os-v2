<?php

namespace App\Livewire\Members;

use App\Models\ChartOfAccounts;
use App\Models\Powas;
use App\Models\PowasApplications;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Rules\LegalAge;
use App\Rules\PhoneNumberFormat2;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddMemberManually extends Component
{
    use WithFileUploads;

    public $showingMessageModal = false;
    public $message;
    public $messageType;
    public $position;
    public $isSave = false;
    public $id_path;
    public $refnum;
    public $thisPOWAS;
    /**
     * The component's barlist.
     *
     * @var array
     */
    public $region = [];

    /**
     * The component's barlist.
     *
     * @var array
     */
    public $province = [];

    /**
     * The component's barlist.
     *
     * @var array
     */
    public $municipality = [];

    /**
     * The component's barlist.
     *
     * @var array
     */
    public $barangay = [];

    /**
     * The component's barlist.
     *
     * @var array
     */
    public $phase = [];

    public $birthplacelist = [];

    public $lastname = '';
    public $firstname = '';
    public $middlename = '';
    public $birthday = '';
    public $birthplace = '';
    public $gender = '';
    public $contactnumber = '';
    public $civilstatus = '';
    public $numberoffamilymembers = '';
    public $address1 = '';
    public $sameas = true;
    public $membershipDate;
    public $first50;
    public $payment;
    public $trxnID = [];
    public $printIDs = [];
    public $applicationFee;
    public $membershipFee;
    public $receiptNumber;
    public $description;
    public $first50Count;
    public $powasSettings;
    public $presentaddress;

    public $isExistsEquityCapitalAccount;
    public $isExistsMembershipFeeAccount;
    public $isExistsApplicationFeeAccount;

    public $regionInput = '',
        $provinceInput = '',
        $municipalityInput = '',
        $barangayInput = '';

    protected $rules;

    protected $validationAttributes = [
        'lastname' => 'last name',
        'firstname' => 'first name',
        'birthday' => 'birthday',
        'birthplace' => 'birthplace',
        'gender' => 'gender',
        'contactnumber' => 'contact number',
        'civilstatus' => 'civil status',
        'numberoffamilymembers' => 'number of family members',
        'address1' => 'zone/village/sector',
        'regionInput' => 'region',
        'provinceInput' => 'province',
        'municipalityInput' => 'municipality',
        'barangayInput' => 'barangay',
        'membershipdDate' => 'membership date',
        'id_path' => 'identidication card',
    ];

    public function __construct()
    {
        $this->rules = [
            'lastname' => 'required',
            'firstname' => 'required',
            'birthday' => ['required', new LegalAge],
            'birthplace' => 'required',
            'gender' => 'required',
            'contactnumber' => ['required', new PhoneNumberFormat2],
            'civilstatus' => 'required',
            'numberoffamilymembers' => 'required',
            'address1' => 'required',
            'regionInput' => 'required',
            'provinceInput' => 'required',
            'municipalityInput' => 'required',
            'barangayInput' => 'required',
            'membershipDate' => 'required',
            'id_path' => 'required|image|max:2048',
        ];
    }

    public function setPayment()
    {
        $this->powasSettings = PowasSettings::where('powas_id', $this->thisPOWAS->powas_id)->first();

        if ($this->first50 == 1) {
            $this->payment = $this->powasSettings->first_50_fee + 0;
        } else {
            $this->payment = $this->powasSettings->membership_fee + $this->powasSettings->application_fee;
        }
    }

    public function loadPhaseName()
    {
        $powasPhases = Powas::where('region', $this->regionInput)
            ->where('province', $this->provinceInput)
            ->where('municipality', $this->municipalityInput)
            ->where('barangay', $this->barangayInput)
            ->orderBy('phase', 'asc')
            ->get();

        if (
            $this->regionInput != '' &&
            $this->provinceInput != '' &&
            $this->municipalityInput != '' &&
            $this->barangayInput != ''
        ) {
            $phaseNames = [];
            foreach ($powasPhases as $phaseID => $phaseValue) {
                $phaseNames[] = $phaseValue;
            }
            $this->phase = $phaseNames;
            $this->resetErrorBag('phaseInput');
            if (count($this->phase) == 0) {
                $this->addError('phaseInput', 'There is currently no POWAS at ' . $this->barangayInput . ', ' . $this->municipalityInput . ', ' . $this->provinceInput);
            }
            $this->presentaddress = $this->address1 . ', ' . $this->barangayInput . ', ' . $this->municipalityInput . ', ' . $this->provinceInput;
        } else {
            $this->resetErrorBag('phaseInput');
            $this->presentaddress = '';
        }
    }

    public function mount(Powas $selectedPOWAS)
    {
        $this->thisPOWAS = $selectedPOWAS;
        $this->payment = 2700;

        $barlist = storage_path('app/bar_list.json');
        $this->membershipDate = Carbon::now()->format('Y-m-d');

        $this->isExistsEquityCapitalAccount = ChartOfAccounts::where('account_type', 'EQUITY')->where('account_name', 'LIKE', '%' . 'CAPITAL' . '%')->count();
        $this->isExistsApplicationFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'APPLICATION' . '%')->count();
        $this->isExistsMembershipFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'MEMBERSHIP' . '%')->count();

        $this->first50Count = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.firstfifty', 'Y')
            ->where('powas_applications.powas_id', $selectedPOWAS->powas_id)
            ->orderBy('powas_applications.lastname', 'asc')
            ->count();

        if (file_exists($barlist)) {
            $jsonData = json_decode(file_get_contents($barlist), true);

            $regionName = [];

            foreach ($jsonData as $regionID => $regionData) {
                $regionName[$regionID] = $regionData['region_name'];
            }

            $this->region = $regionName;
            $this->loadprovince();
            $this->loadmunicipality();
            $this->loadbarangay();
            $this->loadBirthplace();
        } else {
            return abort(404);
        }
    }

    public function render()
    {
        return view('livewire.members.add-member-manually');
    }

    public function loadBirthplace()
    {
        $barlist = storage_path('app/bar_list.json');

        if (file_exists($barlist)) {
            $jsonData = json_decode(file_get_contents($barlist), true);

            $barangayNames = [];

            foreach ($jsonData as $regionID) {
                foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                    foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                        $barangayNames[] = $municipalityID . ', ' . $provinceID;
                    }
                }
            }
            $this->birthplacelist = $barangayNames;
        } else {
            return abort(404);
        }
    }

    public function loadprovince()
    {
        $selectedRegion = $this->regionInput;

        $this->provinceInput = '';
        $this->municipalityInput = '';
        $this->barangayInput = '';

        $barlist = storage_path('app/bar_list.json');

        if (file_exists($barlist)) {
            $jsonData = json_decode(file_get_contents($barlist), true);

            $provinceNames = [];

            foreach ($jsonData as $regionID) {
                if ($regionID['region_name'] == $selectedRegion) {
                    foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                        $provinceNames[] = $provinceID;
                    }
                }
            }

            $this->province = $provinceNames;
            $this->loadmunicipality();
            $this->loadbarangay();
        } else {
            return abort(404);
        }
    }

    public function loadmunicipality()
    {
        $selectedRegion = $this->regionInput;
        $selectedProvince = $this->provinceInput;

        $this->municipalityInput = '';
        $this->barangayInput = '';

        $barlist = storage_path('app/bar_list.json');

        if (file_exists($barlist)) {
            $jsonData = json_decode(file_get_contents($barlist), true);

            $municipalityNames = [];

            foreach ($jsonData as $regionID) {
                if ($regionID['region_name'] == $selectedRegion) {
                    foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                        if ($provinceID == $selectedProvince) {
                            foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                                $municipalityNames[] = $municipalityID;
                            }
                        }
                    }
                }
            }

            $this->municipality = $municipalityNames;
            $this->loadbarangay();
        } else {
            return abort(404);
        }
    }

    public function loadbarangay()
    {
        $selectedRegion = $this->regionInput;
        $selectedProvince = $this->provinceInput;
        $selectedMunicipality = $this->municipalityInput;

        $this->barangayInput = '';

        $barlist = storage_path('app/bar_list.json');

        if (file_exists($barlist)) {
            $jsonData = json_decode(file_get_contents($barlist), true);

            $barangayNames = [];

            foreach ($jsonData as $regionID) {
                if ($regionID['region_name'] == $selectedRegion) {
                    foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                        if ($provinceID == $selectedProvince) {
                            foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                                if ($municipalityID == $selectedMunicipality) {
                                    foreach ($municipalityName['barangay_list'] as $barangayID => $barangayName) {
                                        $barangayNames[] = $barangayName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->barangay = $barangayNames;

            // $this->loadPhaseName();
        } else {
            return abort(404);
        }
    }

    public function modalActions($saved)
    {
        if ($saved == true) {
            return redirect()->route('members');
        }

        // $this->showingMessageModal = false;
    }

    public function saveMember()
    {
        $this->validate();

        $toCheck = [
            'lastname' => strtoupper($this->lastname),
            'firstname' => strtoupper($this->firstname),
            'barangay' => strtoupper($this->barangayInput),
            'municipality' => strtoupper($this->municipalityInput),
            'province' => strtoupper($this->provinceInput),
            'region' => strtoupper($this->regionInput),
        ];

        $isExisting = PowasApplications::isExisting($toCheck);

        if ($isExisting == true) {
            $applicationInfo = PowasApplications::getExistingApplication($toCheck);
            $errorMessage = 'Applicant record is already exists at ' . $applicationInfo->barangay . ', ' . $applicationInfo->municipality . ', ' . $applicationInfo->province . ' - ' . $applicationInfo->region . ' with reference number ' . $applicationInfo->application_id . '!';
            $errorMessageType = 'warning';
            $errorPosition = 'top-right';

            return $this->dispatch('message', [
                'message' => $errorMessage,
                'position' => $errorPosition,
                'messageType' => $errorMessageType,
            ]);
        } else {
            $idName = strtoupper($this->lastname) . ", " . strtoupper($this->firstname) . "." . $this->id_path->extension();
            $this->id_path->storeAs('ids', $idName, 'public');
            $referenceNumber = rand(10000000, 99999999);
            $this->refnum = $referenceNumber;

            PowasApplications::create([
                'application_id' => $referenceNumber,
                'powas_id' => $this->thisPOWAS->powas_id,
                'lastname' => strtoupper($this->lastname),
                'firstname' => strtoupper($this->firstname),
                'middlename' => strtoupper($this->middlename),
                'birthday' => $this->birthday,
                'birthplace' => strtoupper($this->birthplace),
                'gender' => strtoupper($this->gender),
                'contact_number' => $this->contactnumber,
                'application_date' => $this->membershipDate,
                'civil_status' => strtoupper($this->civilstatus),
                'address1' => strtoupper($this->address1),
                'barangay' => strtoupper($this->barangayInput),
                'municipality' => strtoupper($this->municipalityInput),
                'province' => strtoupper($this->provinceInput),
                'region' => strtoupper($this->regionInput),
                'present_address' => strtoupper($this->presentaddress),
                'family_members' => $this->numberoffamilymembers,
                'add_mode' => 'manual',
                'id_path' => $idName,
            ]);
            $this->isSave = true;
            $this->message = 'You have successfully added POWAS application with reference number ' . $referenceNumber . '! Please go to Dashboard > POWAS Applications for approval and payment receipt!';
            $this->messageType = 'Success!';
            $this->showingMessageModal = true;
        }
    }
}
