<?php

namespace App\Livewire\Powas;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\Powas;
use App\Models\PowasApplications;
use App\Models\PowasOsLogs;
use App\Models\PowasSettings;
use App\Rules\UniquePOWAS;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PowasCardsList extends Component
{
    use WithPagination;
    public $search;
    public $pagination = 10;
    public $showingDeleteConfirmationModal = false;
    public $inputconfirmation = '';
    public $selectedpowas;
    public $showingAddPOWASModal = false;

    public $powasExists = false;

    public $region = [];

    public $province = [];

    public $municipality = [];

    public $barangay = [];

    public $regionInput = '',
        $provinceInput = '',
        $municipalityInput = '',
        $barangayInput = '',
        $zoneInput = '',
        $phaseInput = '',
        $inaugurationInput = null,
        $statusInput = 'ACTIVE';

    protected $validationAttributes = [
        'inputconfirmation' => 'confirmation code',
        'regionInput' => 'region',
        'provinceInput' => 'province',
        'municipalityInput' => 'municipality',
        'barangayInput' => 'barangay',
        'zoneInput' => 'zone/village/sector',
        'phaseInput' => 'phase name',
        'statusInput' => 'status',
    ];

    protected $rules = [
        'inputconfirmation' => 'required',
    ];

    public function showAddPOWASModal()
    {
        $this->reset([
            'regionInput',
            'provinceInput',
            'municipalityInput',
            'barangayInput',
            'zoneInput',
            'phaseInput',
            'inaugurationInput',
            'statusInput',
        ]);
        $this->resetErrorBag();
        $this->showingAddPOWASModal = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
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

            $this->loadPhaseName();
        } else {
            return abort(404);
        }
    }

    public function loadprovince($edited = false)
    {
        $selectedRegion = $this->regionInput;

        if ($edited) {
            $this->provinceInput = '';
            $this->municipalityInput = '';
            $this->barangayInput = '';
        }

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

            $this->loadPhaseName();
        } else {
            return abort(404);
        }
    }

    public function loadmunicipality($edited = false)
    {
        $selectedRegion = $this->regionInput;
        $selectedProvince = $this->provinceInput;

        if ($edited) {
            $this->municipalityInput = '';
            $this->barangayInput = '';
        }

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

            $this->loadPhaseName();
        } else {
            return abort(404);
        }
    }

    public function loadbarangay($edited = false)
    {
        $selectedRegion = $this->regionInput;
        $selectedProvince = $this->provinceInput;
        $selectedMunicipality = $this->municipalityInput;

        if ($edited) {
            $this->barangayInput = '';
        }

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
        $powasCount = Powas::where('region', $this->regionInput)
            ->where('province', $this->provinceInput)
            ->where('municipality', $this->municipalityInput)
            ->where('barangay', $this->barangayInput)
            ->count();

        if (
            $this->regionInput != '' &&
            $this->provinceInput != '' &&
            $this->municipalityInput != '' &&
            $this->barangayInput != ''
        ) {
            $this->resetErrorBag('phaseInput');
            $this->phaseInput = 'PHASE ' . $this->arabicToRoman($powasCount + 1);
        } else {
            $this->phaseInput = '';
        }
    }

    public function addPOWAS()
    {
        $rules = [
            'regionInput' => 'required',
            'provinceInput' => 'required',
            'municipalityInput' => 'required',
            'barangayInput' => 'required',
            'zoneInput' => 'required',
            'phaseInput' => ['required', new UniquePOWAS(
                $this->regionInput,
                $this->provinceInput,
                $this->municipalityInput,
                $this->barangayInput,
                $this->phaseInput,
            )],
            'inaugurationInput' => 'nullable|date',
        ];

        $this->validate($rules);

        $powasID = CustomNumberFactory::powasID($this->provinceInput, $this->municipalityInput, $this->barangayInput);

        Powas::create([
            'powas_id' => $powasID,
            'region' => $this->regionInput,
            'province' => $this->provinceInput,
            'municipality' => $this->municipalityInput,
            'barangay' => $this->barangayInput,
            'zone' => strtoupper($this->zoneInput),
            'phase' => $this->phaseInput,
            'inauguration_date' => $this->inaugurationInput,
            'status' => $this->statusInput,
        ]);

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created <b><i>' . $this->barangayInput . ' POWAS ' . $this->phaseInput . '</i></b> with ID ' . $powasID . '.';
        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'powas-coop', $powasID);

        PowasSettings::create([
            'powas_id' => $powasID,
        ]);

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created <b><i>' . $this->barangayInput . ' POWAS ' . $this->phaseInput . ' Settings</i></b> with POWAS ID ' . $powasID . '.';
        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'powas-coop', $powasID);

        $this->reset([
            'regionInput',
            'provinceInput',
            'municipalityInput',
            'barangayInput',
            'zoneInput',
            'phaseInput',
            'inaugurationInput',
            'statusInput',
            'showingAddPOWASModal',
        ]);
        $this->resetErrorBag();

        $this->dispatch('alert', [
            'message' => 'New POWAS successfully added!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function gotoList()
    {
        $this->redirect('powas');
    }

    function arabicToRoman($number)
    {
        if ($number < 1 || $number > 3999) {
            return "Invalid input";
        }

        $values = array(
            1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1
        );

        $romanNumerals = array(
            'M', 'CM', 'D', 'CD', 'C', 'XC', 'L', 'XL', 'X', 'IX', 'V', 'IV', 'I'
        );

        $result = '';

        for ($i = 0; $i < count($values); $i++) {
            while ($number >= $values[$i]) {
                $result .= $romanNumerals[$i];
                $number -= $values[$i];
            }
        }

        return $result;
    }

    public function render()
    {
        if (!$this->search) {
            $powaslist = Powas::orderBy('region', 'asc')
                ->orderBy('province', 'asc')
                ->orderBy('municipality', 'asc')
                ->orderBy('barangay', 'asc')
                ->orderBy('phase', 'asc')
                ->paginate($this->pagination);
        } else {
            $powaslist = Powas::where('powas_id', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('region', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('province', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('municipality', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('barangay', 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere('phase', 'like', '%' . strtoupper($this->search) . '%')
                ->orderBy('region', 'asc')
                ->orderBy('province', 'asc')
                ->orderBy('municipality', 'asc')
                ->orderBy('barangay', 'asc')
                ->orderBy('phase', 'asc')
                ->paginate($this->pagination);
        }
        return view('livewire.powas.powas-cards-list', [
            'powaslist' => $powaslist,
        ]);
    }

    public function showDeleteConfirmationModal(Powas $powas)
    {
        $this->selectedpowas = $powas;

        $applicationCount = PowasApplications::where('application_status', 'PENDING')
            ->orWhere('application_status', 'VERIFIED')
            ->where('powas_id', $powas->powas_id)->count();

        $membersCount = PowasApplications::where('application_status', 'APPROVED')
            ->where('powas_id', $powas->powas_id)->count();

        if ($applicationCount > 0 || $membersCount > 0) {
            $this->dispatch('alert', [
                'message' => 'Unable to delete POWAS because there are already Applications and Members on it!',
                'position' => 'top-right',
                'messageType' => 'warning',
            ]);
        } else {
            $this->reset([
                'inputconfirmation'
            ]);
            $this->resetErrorBag();
            $this->showingDeleteConfirmationModal = true;
        }
    }

    public function delete(Powas $powas)
    {
        $this->validate();

        if ($powas->powas_id != $this->inputconfirmation) {
            $this->addError('inputconfirmation', 'Confirmation code does not match!');
        } else {
            $powas->delete();
            $this->reset([
                'showingDeleteConfirmationModal',
                'selectedpowas',
                'inputconfirmation',
            ]);
            $this->dispatch('alert', [
                'message' => 'POWAS successfully deleted!',
                'position' => 'top-right',
                'messageType' => 'success',
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> deleted <b><i>' . $powas->barangay . ' POWAS ' . $powas->phase . '</i></b>.';
            ActionLogger::dispatch('delete', $log_message, Auth::user()->user_id, 'powas-coop');
        }
    }
}
