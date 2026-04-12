<?php

namespace App\Livewire\Powas;

use App\Factory\CustomNumberFactory;
use App\Models\Powas;
use App\Models\PowasApplications;
use App\Rules\LegalAge;
use App\Rules\PhoneNumberFormat2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Laravel\Jetstream\Jetstream;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Apply extends Component
{
    use WithFileUploads;

    public $showingMessageModal = false;
    public $message;
    public $messageType;
    public $position;
    public $isSave = false;
    public $id_path;
    public $refnum;
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
    public $presentaddress = '';
    public $terms;

    public $regionInput = '',
        $provinceInput = '',
        $municipalityInput = '',
        $barangayInput = '',
        $phaseInput = '';

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
        'phaseInput' => 'phase name',
        'id_path' => 'identidication card',
        'terms' => 'terms and condition',
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
            'id_path' => 'required|image|max:2048',
            'phaseInput' => 'required',
        ];

        if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
            $this->rules['terms'] = ['accepted', 'required'];
        }
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

    public function mount()
    {
        // $this->saveIDCard();

        $barlist = storage_path('app/bar_list.json');

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

            $this->loadPhaseName();
        } else {
            return abort(404);
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
                $this->phaseInput = '';
                $this->addError('phaseInput', 'There is currently no POWAS at ' . $this->barangayInput . ', ' . $this->municipalityInput . ', ' . $this->provinceInput);
            }
            $this->presentaddress = $this->address1 . ', ' . $this->barangayInput . ', ' . $this->municipalityInput . ', ' . $this->provinceInput;
        } else {
            $this->resetErrorBag('phaseInput');
            $this->phaseInput = '';
            $this->presentaddress = '';
        }
    }

    public function modalActions($saved)
    {
        if ($saved == true) {
            $this->redirect('/');
        }
    }

    public function saveApplication()
    {
        // $this->saveIDCard();

        // dd(asset('/powas-os/public/uploads/ids'));

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
            $errorMessage = 'Your record is already exists at ' . $applicationInfo->barangay . ', ' . $applicationInfo->municipality . ', ' . $applicationInfo->province . ' - ' . $applicationInfo->region . ' with reference number ' . $applicationInfo->application_id . '!';
            $errorMessageType = 'warning';
            $errorPosition = 'top-right';

            return $this->dispatch('app_exists', [
                'message' => $errorMessage,
                'position' => $errorPosition,
                'messageType' => $errorMessageType,
            ]);
        } else {
            $idName = CustomNumberFactory::getRandomID() . "." . $this->id_path->extension();
            $this->id_path->storeAs('ids', $idName, 'public');
            $referenceNumber = rand(10000000, 99999999);
            $this->refnum = $referenceNumber;
            PowasApplications::create([
                'application_id' => $referenceNumber,
                'powas_id' => strtoupper($this->phaseInput),
                'lastname' => strtoupper($this->lastname),
                'firstname' => strtoupper($this->firstname),
                'middlename' => strtoupper($this->middlename),
                'birthday' => $this->birthday,
                'birthplace' => strtoupper($this->birthplace),
                'gender' => strtoupper($this->gender),
                'contact_number' => $this->contactnumber,
                'application_date' => Date::now(),
                'civil_status' => strtoupper($this->civilstatus),
                'address1' => strtoupper($this->address1),
                'barangay' => strtoupper($this->barangayInput),
                'municipality' => strtoupper($this->municipalityInput),
                'province' => strtoupper($this->provinceInput),
                'region' => strtoupper($this->regionInput),
                'present_address' => strtoupper($this->presentaddress),
                'family_members' => $this->numberoffamilymembers,
                'id_path' => $idName,
            ]);
            $this->isSave = true;
            $this->message = 'You have successfully submitted your POWAS application with reference number ' . $referenceNumber . '! You may now proceed to the designated treasurer to pay for application and membership fees. Take note of your reference number to be used for following up your application. Thank you!';
            $this->messageType = 'Success!';
            $this->showingMessageModal = true;
        }
    }

    // public function saveIDCard()
    // {
    //     $accessToken = app('getGoogleToken');
    //     $mimeType = $this->id_path->getMimeType();
    //     $imageFile = file_get_contents($this->id_path->getRealPath());
    //     $fileName = Str::slug($this->id_path->getClientOriginalName());
    //     // dd($name);

    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $accessToken,
    //         'Content-Type' => 'Application/json'
    //     ])->post('https://www.googleapis.com/upload/drive/v3/files', [
    //         'data' => $fileName,
    //         'name' => $fileName,
    //         'mimeType' => $mimeType,
    //         'uploadType' => 'resumable',
    //         'parents' => [\Config('services.google.idcards_folder')],
    //     ]);

    //     if ($response->successful()) {
    //         dd($response);

    //         $this->dispatch('app_exists', [
    //             'message' => 'ID successfully uploaded!',
    //             'position' => 'top-right',
    //             'messageType' => 'success',
    //         ]);
    //     } else {
    //         dd($response);

    //         $this->dispatch('app_exists', [
    //             'message' => $response,
    //             'position' => 'top-right',
    //             'messageType' => 'error',
    //         ]);
    //     }
    // }

    public function viewApplicationPDF()
    {
        $this->redirect('/application-form/view/' . $this->refnum);
    }

    public function render()
    {
        return view('livewire.powas.apply');
    }
}
