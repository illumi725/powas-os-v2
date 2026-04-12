<?php

namespace App\Livewire\Members;

use App\Events\ActionLogger;
use App\Models\PowasMembers;
use App\Rules\LegalAge;
use App\Rules\PhoneNumberFormat2;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditMember extends Component
{
    public $memberID;
    public $powasID;
    public $memberInfo = [];
    public $memberInfoOldValues = [];
    public $memberInfoToUpdate = [];
    public $birthplaces = [];
    public $sameas = true;

    // For ComboBoxes
    public $regions = [];
    public $provinces = [];
    public $municipalities = [];
    public $barangays = [];
    public $showingConfirmSave = false;

    protected $rules;

    protected $validationAttributes = [
        'memberInfo.lastname' => 'last name',
        'memberInfo.firstname' => 'first name',
        'memberInfo.birthday' => 'birthday',
        'memberInfo.birthplace' => 'birthplace',
        'memberInfo.gender' => 'gender',
        'memberInfo.contact_number' => 'contact_number',
        'memberInfo.civil_status' => 'civil_status',
        'memberInfo.address1' => 'address1',
        'memberInfo.barangay' => 'barangay',
        'memberInfo.municipality' => 'municipality',
        'memberInfo.province' => 'province',
        'memberInfo.region' => 'region',
        'memberInfo.present_address' => 'present_address',
        'memberInfo.family_members' => 'family_members',
    ];

    public function __construct()
    {
        $this->rules = [
            'memberInfo.lastname' => 'required',
            'memberInfo.firstname' => 'required',
            'memberInfo.birthday' => ['required', new LegalAge],
            'memberInfo.birthplace' => 'required',
            'memberInfo.gender' => 'required',
            'memberInfo.contact_number' => ['required', new PhoneNumberFormat2],
            'memberInfo.civil_status' => 'required',
            'memberInfo.address1' => 'required',
            'memberInfo.barangay' => 'required',
            'memberInfo.municipality' => 'required',
            'memberInfo.province' => 'required',
            'memberInfo.region' => 'required',
            'memberInfo.present_address' => 'required',
            'memberInfo.family_members' => 'required',
        ];
    }

    public function mount($memberID)
    {
        $this->memberID = $memberID;

        $this->loadMemberInfo($memberID);
        $this->loadBirthplaces();
        $this->loadRegions();
    }

    public function loadMemberInfo($memberID)
    {
        $memberData = PowasMembers::find($memberID);
        $this->powasID = $memberData->applicationinfo->powas_id;

        $this->memberInfo = $this->memberInfoOldValues = array_merge([
            'lastname' => $memberData->applicationinfo->lastname,
            'firstname' => $memberData->applicationinfo->firstname,
            'middlename' => $memberData->applicationinfo->middlename,
            'birthday' => $memberData->applicationinfo->birthday,
            'birthplace' => $memberData->applicationinfo->birthplace,
            'gender' => $memberData->applicationinfo->gender,
            'contact_number' => $memberData->applicationinfo->contact_number,
            'civil_status' => $memberData->applicationinfo->civil_status,
            'membership_date' => $memberData->membership_date,
            'address1' => $memberData->applicationinfo->address1,
            'barangay' => $memberData->applicationinfo->barangay,
            'municipality' => $memberData->applicationinfo->municipality,
            'province' => $memberData->applicationinfo->province,
            'region' => $memberData->applicationinfo->region,
            'present_address' => $memberData->applicationinfo->present_address,
            'family_members' => $memberData->applicationinfo->family_members,
        ]);
    }

    public function loadBarList()
    {
        $barlist = storage_path('app/bar_list.json');
        $jsonData = '';

        if (file_exists($barlist)) {
            $jsonData = json_decode(file_get_contents($barlist), true);
        } else {
            return abort(404);
        }

        return $jsonData;
    }

    public function loadBirthplaces()
    {
        $jsonData = $this->loadBarList();

        $barNames = [];

        foreach ($jsonData as $regionID) {
            foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                    $barNames[] = $municipalityID . ', ' . $provinceID;
                }
            }
        }

        $this->birthplaces = $barNames;
    }

    public function loadRegions()
    {
        $jsonData = $this->loadBarList();

        $regNames = [];

        foreach ($jsonData as $regionID => $regData) {
            $regNames[$regionID] = $regData['region_name'];
        }

        $this->regions = $regNames;
        $this->loadProvinces();
        $this->loadMunicipalities();
        $this->loadBarangays();
    }

    public function loadProvinces()
    {
        $jsonData = $this->loadBarList();
        $selectedRegion = $this->memberInfo['region'];

        $this->reset([
            'provinces',
            'municipalities',
            'barangays',
        ]);

        $provNames = [];

        foreach ($jsonData as $regionID) {
            if ($regionID['region_name'] == $selectedRegion) {
                foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                    $provNames[] = $provinceID;
                }
            }
        }

        $this->provinces = $provNames;
        $this->loadMunicipalities();
        $this->loadBarangays();
    }

    public function loadMunicipalities()
    {
        $jsonData = $this->loadBarList();
        $selectedRegion = $this->memberInfo['region'];
        $selectedProvince = $this->memberInfo['province'];

        $this->reset([
            'municipalities',
            'barangays',
        ]);

        $muniNames = [];

        foreach ($jsonData as $regionID) {
            if ($regionID['region_name'] == $selectedRegion) {
                foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                    if ($provinceID == $selectedProvince) {
                        foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                            $muniNames[] = $municipalityID;
                        }
                    }
                }
            }
        }

        $this->municipalities = $muniNames;
        $this->loadBarangays();
    }

    public function loadBarangays()
    {
        $jsonData = $this->loadBarList();
        $selectedRegion = $this->memberInfo['region'];
        $selectedProvince = $this->memberInfo['province'];
        $selectedMunicipality = $this->memberInfo['municipality'];

        $this->reset([
            'barangays',
        ]);

        $barNames = [];

        foreach ($jsonData as $regionID) {
            if ($regionID['region_name'] == $selectedRegion) {
                foreach ($regionID['province_list'] as $provinceID => $provinceName) {
                    if ($provinceID == $selectedProvince) {
                        foreach ($provinceName['municipality_list'] as $municipalityID => $municipalityName) {
                            if ($municipalityID == $selectedMunicipality) {
                                foreach ($municipalityName['barangay_list'] as $barangayID => $barangayName) {
                                    $barNames[] = $barangayName;
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->barangays = $barNames;
    }

    public function confirmSave()
    {
        $this->validate();
        $this->setToUpdateData();
        $this->showingConfirmSave = true;
    }

    public function saveInfo()
    {
        if (count($this->memberInfoToUpdate) > 0) {
            $currentMember = PowasMembers::find($this->memberID);
            foreach ($this->memberInfoToUpdate as $field => $value) {
                $currentMember->applicationinfo->$field = strtoupper($value);
                $currentMember->applicationinfo->save();

                if ($field == 'membership_date') {
                    $currentMember->membership_date = $value;
                    $currentMember->save();
                }

                $oldValue = $this->memberInfoOldValues[$field];
                $newValue = strtoupper($value);

                if (!is_numeric($oldValue)) {
                    $oldValue = '"' . $oldValue . '"';
                }

                if (!is_numeric($newValue)) {
                    $newValue = '"' . $newValue . '"';
                }

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . strtoupper($oldValue) . '</i></b> to <b><i>' . strtoupper($newValue) . '</i></b> in the column <i><u>' . $field . '</u></i> with POWAS ID <b>' . $this->powasID . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'members', $this->powasID);
            }
            $this->loadMemberInfo($this->memberID);
            $this->resetErrorBag();
            $this->dispatch('saved', [
                'message' => 'Member information successfully updated!',
                'messageType' => 'success',
                'position' => 'top-right',
            ]);
            $this->showingConfirmSave = false;
        }
    }

    public function setToUpdateData()
    {
        $this->reset(['memberInfoToUpdate']);

        foreach ($this->memberInfo as $field => $value) {
            if ($this->memberInfoOldValues[$field] != $this->memberInfo[$field]) {
                $this->memberInfoToUpdate[$field] = $this->memberInfo[$field];
            }
        }
    }

    public function loadPresentAddress()
    {
        if ($this->memberInfo['region'] != '' && $this->memberInfo['province'] != '' && $this->memberInfo['municipality'] != '' && $this->memberInfo['barangay'] != '' && $this->memberInfo['address1'] != '') {
            $this->memberInfo['present_address'] = $this->memberInfo['address1'] . ', ' . $this->memberInfo['barangay'] . ', ' . $this->memberInfo['municipality'] . ', ' . $this->memberInfo['province'];
        }
    }

    public function render()
    {
        return view('livewire.members.edit-member');
    }
}
