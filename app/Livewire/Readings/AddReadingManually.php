<?php

namespace App\Livewire\Readings;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Readings;
use App\Models\MeterChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddReadingManually extends Component
{
    public $powasID;
    public $powas;
    public $powasSettings;
    public $search;
    public $savedCount = 0;
    public $membersList;
    public $previousReading = [];
    public $presentReading = [];
    public $readingIDs = [];
    public $readingCounts = [];
    public $readingDate = [];
    public $dateNow;
    public $showingReadingDatePicker = false;
    public $readingStats = [];
    public $readingDateInput;
    public $savingAll = false;
    public $isInitialReading = [];

    protected $rules = [
        'presentReading.*' => 'required|numeric|gte:previousReading.*',
        'readingDate.*' => 'required|date|before_or_equal:today',
        'readingCounts.*' => 'required|numeric|min:0',
    ];

    protected $validationAttributes = [
        'presentReading.*' => 'present reading',
        'readingDate.*' => 'reading date',
        'readingCounts.*' => 'required|numeric|min:0',
    ];

    public function showDatePicker()
    {
        $this->showingReadingDatePicker = true;
    }

    public function setReadingDate()
    {
        $this->validate([
            'readingDateInput' => ['required', 'date', 'before_or_equal:today']
        ]);

        foreach ($this->readingStats as $readStatKey => $readStatValue) {
            if ($readStatValue == 'NOT-EXISTS') {
                $this->readingDate[$readStatKey] = Carbon::parse($this->readingDateInput)->format('Y-m-d');
            }
        }

        $this->showingReadingDatePicker = false;
    }

    public function mount($powasID)
    {
        $this->powas = Powas::find($powasID);
        $this->powasID = $powasID;
    }

    public function saveAll()
    {
        $this->reset([
            'savingAll'
        ]);
        $this->validateOnly('readingDate.*');

        $this->savingAll = true;

        foreach ($this->readingIDs as $key => $value) {
            $this->saveReading($value, $key);
        }
    }

    public function saveReading($readingID, $memberID)
    {
        $this->validateOnly('presentReading.' . $memberID);

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
                'reading' => $this->presentReading[$memberID],
                'reading_date' => $this->readingDate[$memberID],
                'reading_count' => $this->readingCounts[$memberID],
            ]);

            $log_message = '<b><u>' . $importer->userinfo->lastname . ', ' . $importer->userinfo->firstname . '</u></b> created reading record for <b><i>' . strtoupper($memberName) . '</i></b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'reading', $this->powasID);
        } else {
            $readingToUpdate = Readings::where('reading_id', $readingID)->first();

            if ($readingToUpdate->reading != $this->presentReading[$memberID]) {
                $readingToUpdate->reading = $this->presentReading[$memberID];
                $readingToUpdate->save();

                $log_message = '<b><u>' . $importer->lastname . ', ' . $importer->firstname . '</u></b> updated reading record for <b><i>' . strtoupper($memberName) . '</i></b> with reading ID <b>' . $readingID . '</b>';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'reading', $this->powasID);
            }

            if ($readingToUpdate->reading_date != $this->readingDate[$memberID]) {
                $readingToUpdate->reading_date = $this->readingDate[$memberID];
                $readingToUpdate->save();

                $log_message = '<b><u>' . $importer->userinfo->lastname . ', ' . $importer->userinfo->firstname . '</u></b> updated reading date for <b><i>' . strtoupper($memberName) . '</i></b> with reading ID <b>' . $readingID . '</b>';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'reading', $this->powasID);
            }
        }

        $this->dispatch('saved_' . $this->readingIDs[$memberID]);
    }

    public function render()
    {
        $this->powasSettings = PowasSettings::where('powas_id', $this->powasID)->first();
        $this->savedCount = 0;

        if ($this->search == '' || $this->search == null) {
            $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_applications.powas_id', $this->powasID)
                ->where('powas_members.member_status', 'ACTIVE')
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->get();
        } else {
            $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_applications.powas_id', $this->powasID)
                ->where('powas_members.member_status', 'ACTIVE')
                ->where(function ($query) {
                    $query->where('powas_applications.lastname', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.firstname', 'like', '%' . strtoupper($this->search) . '%');
                })
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->get();
        }

        $this->reset([
            'previousReading',
            'presentReading',
            'readingIDs',
            'readingCounts',
        ]);

        foreach ($this->membersList as $member) {
            $readingRecord = Readings::where('member_id', $member->member_id)
                ->orderBy('reading_count', 'desc')
                ->first();
            $readingID = CustomNumberFactory::getRandomID();

            $reading_day = $this->powasSettings->reading_day;

            if ($this->powasSettings->reading_day < 10) {
                $reading_day = '0' . $this->powasSettings->reading_day;
            }

            if ($readingRecord == null) {
                // $this->readingDate[$member->member_id] = Carbon::now()->addMonth()->format('Y-m-' . $reading_day);
                $this->previousReading[$member->member_id] = 0;
                $this->presentReading[$member->member_id] = '';
                $this->readingIDs[$member->member_id] = $readingID;
                $this->readingCounts[$member->member_id] = 0;
                $this->isInitialReading[$member->member_id] = true;
                $this->readingStats[$member->member_id] = 'NOT-EXISTS';
            } else {
                // $this->readingDate[$member->member_id] = Carbon::parse(Carbon::parse($readingRecord->reading_date))->addMonth()->format('Y-m-' . $reading_day);
                if ($readingRecord->count() == 0) {
                    $this->previousReading[$member->member_id] = '';
                    $this->presentReading[$member->member_id] = '';
                    $this->readingIDs[$member->member_id] = $readingID;
                    $this->readingCounts[$member->member_id] = $readingRecord->reading_count + 1;
                    $this->readingDate[$member->member_id] = Carbon::parse(Carbon::parse($readingRecord->reading_date))->addMonth()->format('Y-m-' . $reading_day);
                    $this->readingStats[$member->member_id] = 'NOT-EXISTS';
                } elseif ($readingRecord->reading_count == 0) {
                    $this->previousReading[$member->member_id] = $readingRecord->reading;
                    $this->presentReading[$member->member_id] = '';
                    $this->readingIDs[$member->member_id] = $readingID;
                    $this->readingCounts[$member->member_id] = $readingRecord->reading_count + 1;
                    $this->readingDate[$member->member_id] = Carbon::parse(Carbon::parse($readingRecord->reading_date))->addMonth()->format('Y-m-' . $reading_day);
                    $this->readingStats[$member->member_id] = 'NOT-EXISTS';
                } else {
                    $previousReadingRecord = Readings::where('member_id', $member->member_id)
                        ->orderBy('reading_count', 'desc')
                        ->offset(1)
                        ->first();
                    $lastReadingDate = Carbon::parse($readingRecord->reading_date);
                    
                    // Check if reading exists for the current month
                    if ($lastReadingDate->format('Y-m') === Carbon::now()->format('Y-m')) {
                        // Edit Mode
                        $latestMeterChange = null;
                        if ($previousReadingRecord) {
                            $latestMeterChange = MeterChange::where('member_id', $member->member_id)
                                ->whereDate('change_date', '>=', Carbon::parse($previousReadingRecord->reading_date))
                                ->orderByDesc('change_date')
                                ->first();
                        } else {
                            $latestMeterChange = MeterChange::where('member_id', $member->member_id)
                                ->orderByDesc('change_date')
                                ->first();
                        }

                        if ($latestMeterChange) {
                             $this->previousReading[$member->member_id] = $latestMeterChange->new_meter_start_reading;
                        } elseif ($previousReadingRecord) {
                             $this->previousReading[$member->member_id] = $previousReadingRecord->reading;
                        } else {
                             // If no record before the current one (maybe first reading was this month)
                             $this->previousReading[$member->member_id] = 0; 
                        }

                        $this->presentReading[$member->member_id] = $readingRecord->reading;
                        $this->readingIDs[$member->member_id] = $readingRecord->reading_id;
                        $this->readingCounts[$member->member_id] = $readingRecord->reading_count;
                        $this->readingDate[$member->member_id] = $readingRecord->reading_date;
                        $this->readingStats[$member->member_id] = 'EXISTS';
                        $this->savedCount++;
                    } else {
                        // Add Mode
                        $latestMeterChange = MeterChange::where('member_id', $member->member_id)
                            ->whereDate('change_date', '>=', $lastReadingDate)
                            ->orderByDesc('change_date')
                            ->first();

                        if ($latestMeterChange) {
                            $this->previousReading[$member->member_id] = $latestMeterChange->new_meter_start_reading;
                        } else {
                            $this->previousReading[$member->member_id] = $readingRecord->reading;
                        }

                        $this->presentReading[$member->member_id] = '';
                        $this->readingIDs[$member->member_id] = $readingID;
                        $this->readingCounts[$member->member_id] = $readingRecord->reading_count + 1;
                        $this->readingDate[$member->member_id] = Carbon::parse(Carbon::parse($readingRecord->reading_date))->addMonth()->format('Y-m-' . $reading_day);
                        $this->readingStats[$member->member_id] = 'NOT-EXISTS';
                    }
                }
                $this->isInitialReading[$member->member_id] = false;
            }
        }
        return view('livewire.readings.add-reading-manually');
    }
}
