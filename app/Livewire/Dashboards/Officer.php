<?php

namespace App\Livewire\Dashboards;

use App\Models\Billings;
use App\Models\Powas;
use App\Models\PowasApplications;
use App\Models\Readings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Officer extends Component
{
    public $powasCount;
    public $applicationCount;
    public $membersCount = 0;
    public $user;
    public $recordsCount;

    public function mount()
    {
        $this->user = Auth::user();
        $this->powasCount = Powas::all()->count();
        $this->applicationCount = PowasApplications::where('powas_id', $this->user->powas_id)
            ->where(function ($query) {
                $query->where('application_status', 'PENDING')
                    ->orWhere('application_status', 'VERIFIED');
            })
            ->count();

        $this->membersCount = DB::table('powas_members')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_id', $this->user->powas_id)
            ->count();

        $readingCount = Readings::where('powas_id', Auth::user()->powas_id)->count() + Billings::where('powas_id', Auth::user()->powas_id)->count();

        $this->recordsCount = $readingCount;
    }

    public function powasView()
    {
        return redirect()->route('powas.records', ['powasID' => $this->user->powas_id]);
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
        return view('livewire.dashboards.officer');
    }
}
