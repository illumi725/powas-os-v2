<?php

namespace App\Livewire\Readings;

use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Readings;
use Carbon\Carbon;
use Livewire\Component;

class ReadingSheet extends Component
{
    public $presentReadingDate;
    public $powasID;
    public $powasSettings;
    public $powas;
    public $membersList;
    public $billingMonth;

    public function mount($powasID, $readingDate = null)
    {
        $this->powasID = $powasID;
        $this->presentReadingDate = Carbon::parse($readingDate)->format('Y-m-d');
        $this->powas = Powas::find($powasID);
        $this->powasSettings = PowasSettings::where('powas_id', $powasID)->first();
        $this->billingMonth = Carbon::parse($readingDate)->subDays(15)->format('F Y');
    }

    public function render()
    {
        $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_status', 'ACTIVE')
            ->where('powas_members.membership_date', '<=', $this->presentReadingDate)
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        $readingInfos = [];

        $presReading = '';
        $prevReading = 0;
        $readDate = '';

        foreach ($this->membersList as $member) {
            $presentReading = Readings::where('member_id', $member->member_id)
                ->where('reading_date', $this->presentReadingDate)
                ->first();

            if ($presentReading == null) {
                $presReading = '';
                $previousReading = Readings::where('member_id', $member->member_id)
                    ->orderByDesc('reading_date')
                    ->first();
                if ($previousReading == null) {
                    $prevReading = 0;
                    $readDate = '';
                } else {
                    $prevReading = $previousReading->reading;
                    $readDate = '';
                }
            } else {
                $presReading = $presentReading->reading;

                $previousReading = Readings::where('member_id', $member->member_id)
                    ->where('reading_date', '<', $this->presentReadingDate)
                    ->orderByDesc('reading_date')
                    ->first();

                if ($previousReading == null) {
                    $prevReading = 0;
                    $readDate = $presentReading->reading_date;
                } else {
                    $prevReading = $previousReading->reading;
                    $readDate = $presentReading->reading_date;
                }
            }

            if ($previousReading) {
                $daysElapsed = Carbon::parse($this->presentReadingDate)->diffInDays($previousReading->reading_date);
            } else {
                $daysElapsed = 999;
            }

            // dd($daysElapsed);

            if ($daysElapsed >= 7) {
                $readingInfos[$member->member_id] = [
                    'member_name' => $member->lastname . ', ' . $member->firstname,
                    'meter_number' => $member->meter_number,
                    'previous_reading' => $prevReading,
                    'present_reading' => $presReading,
                    'reading_date' => $readDate,
                ];
            }
        }


        return view('livewire.readings.reading-sheet', [
            'readingInfos' => $readingInfos,
        ]);
    }
}
