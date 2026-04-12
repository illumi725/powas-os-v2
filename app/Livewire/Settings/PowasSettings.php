<?php

namespace App\Livewire\Settings;

use App\Events\ActionLogger;
use App\Models\PowasMembers;
use App\Models\PowasSettings as ModelsPowasSettings;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PowasSettings extends Component
{
    public $comView = 'livewire.powas.powas-cards-list';
    public $powasSettings = [];
    public $oldValues = [];
    public $newValues = [];
    public $powasID;
    public $membersList = [];

    protected $rules = [
        'powasSettings.water_rate' => ['required', 'numeric'],
        'powasSettings.first_50_fee' => ['required', 'numeric'],
        'powasSettings.application_fee' => ['required', 'numeric'],
        'powasSettings.membership_fee' => ['required', 'numeric'],
        'powasSettings.minimum_payment' => ['required', 'numeric'],
        'powasSettings.members_micro_savings' => ['nullable', 'numeric'],
        'powasSettings.due_date_day' => ['required', 'numeric', 'min:1', 'max:28'],
        'powasSettings.reading_day' => ['required', 'numeric', 'min:1', 'max:28'],
        'powasSettings.collection_day' => ['required', 'numeric', 'min:1', 'max:28'],
        'powasSettings.days_before_disconnection' => ['required', 'numeric', 'min:1', 'max:28'],
        'powasSettings.penalty_per_day' => ['required', 'numeric'],
        'powasSettings.reconnection_fee' => ['required', 'numeric'],
        'powasSettings.land_owners_id' => ['nullable'],
    ];

    protected $validationAttributes = [
        'powasSettings.water_rate' => 'water rate',
        'powasSettings.first_50_fee' => 'first 50 fee',
        'powasSettings.application_fee' => 'application fee',
        'powasSettings.membership_fee' => 'membership fee',
        'powasSettings.minimum_payment' => 'minimum payment',
        'powasSettings.members_micro_savings' => 'member\'s micro-savings',
        'powasSettings.due_date_day' => 'due date day',
        'powasSettings.reading_day' => 'reading day',
        'powasSettings.collection_day' => 'collection day',
        'powasSettings.days_before_disconnection' => 'days before disconnection',
        'powasSettings.penalty_per_day' => 'penalty per day',
        'powasSettings.reconnection_fee' => 'reconnection fee',
        'powasSettings.land_owners_id' => 'land owner\'s account number',
    ];

    public function mount($powas_id)
    {
        $powasPref = ModelsPowasSettings::where('powas_id', $powas_id)->first();
        $this->powasID = $powas_id;


        $powasMembers = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $powas_id)
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        foreach ($powasMembers as $key => $value) {
            $this->membersList[$value->member_id] = $value->lastname . ', ' . $value->firstname . ' ' . $value->middlename;
        }

        $this->powasSettings = array_merge([
            'id' => $powasPref->id,
            'powas_id' => $powasPref->powas_id,
            'water_rate' => $powasPref->water_rate,
            'first_50_fee' => $powasPref->first_50_fee,
            'application_fee' => $powasPref->application_fee,
            'membership_fee' => $powasPref->membership_fee,
            'minimum_payment' => $powasPref->minimum_payment,
            'members_micro_savings' => $powasPref->members_micro_savings,
            'due_date_day' => $powasPref->due_date_day,
            'reading_day' => $powasPref->reading_day,
            'collection_day' => $powasPref->collection_day,
            'days_before_disconnection' => $powasPref->days_before_disconnection,
            'penalty_per_day' => $powasPref->penalty_per_day,
            'reconnection_fee' => $powasPref->reconnection_fee,
            'land_owners_id' => $powasPref->land_owners_id,
            'bill_paper_size' => $powasPref->bill_paper_size,
            'receipt_paper_size' => $powasPref->receipt_paper_size,
        ]);

        foreach ($this->powasSettings as $key => $value) {
            $this->oldValues[$key] = $value;
        }
    }

    public function updatePOWASSettings()
    {
        $toUpdate = ModelsPowasSettings::where('powas_id', $this->powasID)->first();

        $this->validate();

        $this->getNewValues();

        foreach ($this->newValues as $key => $value) {
            $toUpdate->$key = $value;
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

            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'powas-coop', $this->powasID);
        }

        $this->resetErrorBag();
        $this->dispatch('saved', [
            'message' => 'POWAS preferences successfully updated!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
    }

    public function render()
    {
        return view('livewire.settings.powas-settings');
    }

    public function getNewValues()
    {
        $this->reset(['newValues']);

        if ($this->oldValues['water_rate'] != $this->powasSettings['water_rate']) {
            $this->newValues['water_rate'] = $this->powasSettings['water_rate'];
        }
        if ($this->oldValues['first_50_fee'] != $this->powasSettings['first_50_fee']) {
            $this->newValues['first_50_fee'] = $this->powasSettings['first_50_fee'];
        }
        if ($this->oldValues['application_fee'] != $this->powasSettings['application_fee']) {
            $this->newValues['application_fee'] = $this->powasSettings['application_fee'];
        }
        if ($this->oldValues['membership_fee'] != $this->powasSettings['membership_fee']) {
            $this->newValues['membership_fee'] = $this->powasSettings['membership_fee'];
        }
        if ($this->oldValues['minimum_payment'] != $this->powasSettings['minimum_payment']) {
            $this->newValues['minimum_payment'] = $this->powasSettings['minimum_payment'];
        }
        if ($this->oldValues['members_micro_savings'] != $this->powasSettings['members_micro_savings']) {
            $this->newValues['members_micro_savings'] = $this->powasSettings['members_micro_savings'];
        }
        if ($this->oldValues['due_date_day'] != $this->powasSettings['due_date_day']) {
            $this->newValues['due_date_day'] = $this->powasSettings['due_date_day'];
        }
        if ($this->oldValues['reading_day'] != $this->powasSettings['reading_day']) {
            $this->newValues['reading_day'] = $this->powasSettings['reading_day'];
        }
        if ($this->oldValues['collection_day'] != $this->powasSettings['collection_day']) {
            $this->newValues['collection_day'] = $this->powasSettings['collection_day'];
        }
        if ($this->oldValues['days_before_disconnection'] != $this->powasSettings['days_before_disconnection']) {
            $this->newValues['days_before_disconnection'] = $this->powasSettings['days_before_disconnection'];
        }
        if ($this->oldValues['penalty_per_day'] != $this->powasSettings['penalty_per_day']) {
            $this->newValues['penalty_per_day'] = $this->powasSettings['penalty_per_day'];
        }
        if ($this->oldValues['reconnection_fee'] != $this->powasSettings['reconnection_fee']) {
            $this->newValues['reconnection_fee'] = $this->powasSettings['reconnection_fee'];
        }
        if ($this->oldValues['land_owners_id'] != $this->powasSettings['land_owners_id']) {
            $this->newValues['land_owners_id'] = $this->powasSettings['land_owners_id'];
        }
    }
}
