<?php

namespace App\Livewire\Readings;

use App\Models\MeterChange;
use App\Models\PowasMembers;
use App\Models\Readings;
use App\Events\ActionLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class ChangeMeter extends Component
{
    public $showingModal = false;
    public $memberID;
    public $member;
    public $oldMeterNumber = '';
    public $newMeterNumber = '';
    public $oldMeterFinalReading = null;
    public $isOldMeterBroken = false;
    public $newMeterStartReading = 0;
    public $changeDate;
    public $reason = '';

    protected $rules = [
        'newMeterNumber' => 'required|string|max:255',
        'newMeterStartReading' => 'required|numeric|min:0',
        'changeDate' => 'required|date|before_or_equal:today',
        'reason' => 'nullable|string|max:255',
    ];

    public function updatedIsOldMeterBroken($value)
    {
        if ($value) {
            $this->oldMeterFinalReading = null;
            $this->resetErrorBag('oldMeterFinalReading');
        }
    }

    #[On('openChangeMeterModal')]
    public function openModal($memberID)
    {
        $this->memberID = $memberID;
        $this->member = PowasMembers::find($memberID);
        
        $this->oldMeterNumber = $this->member->meter_number;
        $this->newMeterNumber = '';
        $this->isOldMeterBroken = false;
        
        // Fetch the last reading to set as a placeholder or minimum for the final reading
        $lastReading = Readings::where('member_id', $memberID)
            ->orderByDesc('reading_date')
            ->first();
            
        $this->oldMeterFinalReading = $lastReading ? $lastReading->reading : 0;
        $this->newMeterStartReading = 0;
        $this->changeDate = Carbon::now()->format('Y-m-d');
        $this->reason = '';
        
        $this->resetErrorBag();
        $this->showingModal = true;
    }

    public function save()
    {
        $this->validate();
        
        if (!$this->isOldMeterBroken) {
            $this->validate([
                'oldMeterFinalReading' => 'required|numeric|min:0',
            ]);
        }

        MeterChange::create([
            'member_id' => $this->memberID,
            'old_meter_number' => $this->oldMeterNumber,
            'new_meter_number' => $this->newMeterNumber,
            'old_meter_final_reading' => $this->isOldMeterBroken ? null : $this->oldMeterFinalReading,
            'new_meter_start_reading' => $this->newMeterStartReading,
            'change_date' => $this->changeDate,
            'reason' => $this->reason,
            'recorded_by' => Auth::user()->user_id,
        ]);

        $this->member->meter_number = $this->newMeterNumber;
        $this->member->save();

        $memberName = $this->member->applicationinfo->lastname . ', ' . $this->member->applicationinfo->firstname;
        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> changed meter for <b><i>' . strtoupper($memberName) . '</i></b> from <b>' . ($this->oldMeterNumber ?: 'None') . '</b> to <b>' . $this->newMeterNumber . '</b>.';
        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'meter_change', $this->member->applicationinfo->powas_id);

        $this->showingModal = false;
        
        $this->dispatch('alert', [
            'message' => 'Meter successfully changed!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
        
        // Reload component
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.readings.change-meter');
    }
}
