<?php

namespace App\Livewire\Dashboards;

use App\Models\Powas;
use App\Models\PowasApplications;
use App\Models\PowasMembers;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Admin extends Component
{
    public $powasCount;
    public $applicationCount;
    public $membersCount;
    public $powasCoops = [];

    public $region = '',
        $province = '',
        $municipality = '',
        $powas = '';

    public $regionlist = [],
        $provincelist = [],
        $municipalitylist = [],
        $powaslist = [];

    public $barlist;

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

    public function powasView()
    {
        $this->redirect('powas');
    }

    public function applicationsView()
    {
        $this->redirect('applications');
    }

    public function membersView()
    {
        $this->redirect('members');
    }

    public function render()
    {
        $this->powasCount = Powas::all()->count();

        if ($this->powas == '') {
            $this->applicationCount = PowasApplications::where('application_status', 'PENDING')
                ->orWhere('application_status', 'VERIFIED')->count();
            $this->membersCount = DB::table('powas_members')
                ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->count();
        } else {
            $this->applicationCount = PowasApplications::where('powas_id', $this->powas)
                ->where(function ($query) {
                    $query->where('application_status', 'PENDING')
                        ->orWhere('application_status', 'VERIFIED');
                })
                ->count();
            $this->membersCount = DB::table('powas_members')
                ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_id', $this->powas)
                ->count();
        }

        return view('livewire.dashboards.admin');
    }
}
