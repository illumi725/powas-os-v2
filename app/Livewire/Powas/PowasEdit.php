<?php

namespace App\Livewire\Powas;

use App\Events\ActionLogger;
use App\Models\Powas;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PowasEdit extends Component
{
    public $comView = 'livewire.powas.powas-cards-list';
    public $oldValues = [];
    public $toUpdateInputs = [];

    public $powas;
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

    public $regionInput = '',
        $provinceInput = '',
        $municipalityInput = '',
        $barangayInput = '',
        $zoneInput = '',
        $phaseInput = '',
        $inaugurationInput = null,
        $statusInput = 'ACTIVE';

    protected $rules = [
        'regionInput' => 'required',
        'provinceInput' => 'required',
        'municipalityInput' => 'required',
        'barangayInput' => 'required',
        'zoneInput' => 'required',
        'phaseInput' => 'required',
        'inaugurationInput' => 'nullable|date',
    ];

    protected $validationAttributes = [
        'regionInput' => 'region',
        'provinceInput' => 'province',
        'municipalityInput' => 'municipality',
        'barangayInput' => 'barangay',
        'zoneInput' => 'zone/village/sector',
        'phaseInput' => 'phase name',
        'statusInput' => 'status',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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

            // $this->loadPhaseName();
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

            // $this->loadPhaseName();
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

            // $this->loadPhaseName();
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
            $this->phaseInput = 'PHASE ' . $this->arabicToRoman($powasCount + 1);
        } else {
            $this->phaseInput = '';
        }
    }

    public function update($powas_id)
    {
        $toUpdate = Powas::find($powas_id);
        $this->validate();

        $this->populateToUpdate();

        // $toUpdate->update([
        //     'region' => $this->regionInput,
        //     'province' => $this->provinceInput,
        //     'municipality' => $this->municipalityInput,
        //     'barangay' => $this->barangayInput,
        //     'zone' => strtoupper($this->zoneInput),
        //     'phase' => $this->phaseInput,
        //     'inauguration_date' => $this->inaugurationInput,
        //     'status' => $this->statusInput,
        // ]);

        foreach ($this->toUpdateInputs as $key => $value) {
            $toUpdate->$key = strtoupper($value);
            $toUpdate->save();

            $oldValue = $this->oldValues[$key];
            $newValue = $value;

            if (!is_numeric($oldValue)) {
                $oldValue = '"' . $this->oldValues[$key] . '"';
            }

            if (!is_numeric($newValue)) {
                $newValue = '"' . $value . '"';
            }

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . strtoupper($oldValue) . '</i></b> to <b><i>' . strtoupper($newValue) . '</i></b> in the column <i><u>' . $key . '</u></i> with POWAS ID <b>' . $toUpdate->powas_id . '</b>.';

            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'powas-coop', $powas_id);
        }

        $this->resetErrorBag();
        $this->dispatch('saved', [
            'message' => 'POWAS information successfully updated!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
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

    public function mount($powas_id)
    {
        // dd($powas_id);
        $this->powas = Powas::find($powas_id);
        $this->regionInput = $this->powas->region;
        $this->provinceInput = $this->powas->province;
        $this->municipalityInput = $this->powas->municipality;
        $this->barangayInput = $this->powas->barangay;
        $this->zoneInput = $this->powas->zone;
        $this->phaseInput = $this->powas->phase;
        $this->inaugurationInput = $this->powas->inauguration_date;
        $this->statusInput = $this->powas->status;

        $this->oldValues['region'] = $this->regionInput;
        $this->oldValues['province'] = $this->provinceInput;
        $this->oldValues['municipality'] = $this->municipalityInput;
        $this->oldValues['barangay'] = $this->barangayInput;
        $this->oldValues['zone'] = $this->zoneInput;
        $this->oldValues['phase'] = $this->phaseInput;
        $this->oldValues['inauguration_date'] = $this->inaugurationInput;
        $this->oldValues['status'] = $this->statusInput;

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

            // $this->loadPhaseName();
        } else {
            return abort(404);
        }
    }

    public function render()
    {
        return view('livewire.powas.powas-edit');
    }

    public function populateToUpdate()
    {
        $this->reset(['toUpdateInputs']);

        if ($this->oldValues['region'] != $this->regionInput) {
            $this->toUpdateInputs['region'] = $this->regionInput;
        }

        if ($this->oldValues['province'] != $this->provinceInput) {
            $this->toUpdateInputs['province'] = $this->provinceInput;
        }

        if ($this->oldValues['municipality'] != $this->municipalityInput) {
            $this->toUpdateInputs['municipality'] = $this->municipalityInput;
        }

        if ($this->oldValues['barangay'] != $this->barangayInput) {
            $this->toUpdateInputs['barangay'] = $this->barangayInput;
        }

        if ($this->oldValues['zone'] != $this->zoneInput) {
            $this->toUpdateInputs['zone'] = $this->zoneInput;
        }

        if ($this->oldValues['phase'] != $this->phaseInput) {
            $this->toUpdateInputs['phase'] = $this->phaseInput;
        }

        if ($this->oldValues['inauguration_date'] != $this->inaugurationInput) {
            $this->toUpdateInputs['inauguration_date'] = $this->inaugurationInput;
        }

        if ($this->oldValues['status'] != $this->statusInput) {
            $this->toUpdateInputs['status'] = $this->statusInput;
        }
    }
}
