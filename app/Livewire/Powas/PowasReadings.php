<?php

namespace App\Livewire\Powas;

use App\Factory\CustomNumberFactory;
use App\Imports\ImportReadingTemplate;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Readings;
use App\Models\User;
use App\Rules\CheckExcelHeader;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PowasReadings extends Component
{
    use WithPagination;
    public $powas;
    public $powasID;
    public $pagination = 10;
    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $showingExcelImportModal = false;
    public $showingImportDataModal = false;
    public $showingCountErrorModal = false;
    public $showingReadingDateSelector = false;
    public $selectedPOWAS;
    public $readingDate;
    public $powasSettings;
    public $excelFilePath = null;  // string path in storage/app/excel-uploads/
    public $importCollection = [];

    protected $pageName = 'readings';

    protected $rules = [
        'startDate' => 'required|date|before_or_equal:endDate',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    protected $messages = [
        'startDate.before_or_equal' => 'Start Date must be before or the same as the End Date!',
        'endDate.after_or_equal' => 'Start Date must be before or the same as the End Date!',
    ];

    public function mount($powasID)
    {
        $this->powas = Powas::find($powasID);
        $this->powasID = $powasID;
        $this->powasSettings = PowasSettings::where('powas_id', $powasID)->first();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatingSearch()
    {
        $this->resetPage('readings');
    }

    public function clearFilter()
    {
        $this->reset([
            'search',
            'startDate',
            'endDate',
            'pagination',
        ]);

        $this->resetErrorBag();

        $this->resetPage('readings');

        $this->dispatch('alert', [
            'message' => 'All filters cleared!',
            'messageType' => 'info',
            'position' => 'top-right',
        ]);
    }

    public function showExcelImportModal()
    {
        if ($this->getSaveCount() > 0) {
            $this->showingCountErrorModal = true;
        } else {
            $preset = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_applications.powas_id', $this->powasID)
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->get();

            if ($preset->count() == 0) {
                $this->dispatch('alert', [
                    'message' => 'Import is not possible because there is no members yet at ' . $this->powas->barangay . ' POWAS ' . $this->powas->phase . '!',
                    'messageType' => 'warning',
                    'position' => 'top-right',
                ]);
            } else {
                $this->excelFile = null;
                $this->showingExcelImportModal = true;
            }
        }
    }

    // Called by JS after a successful AJAX upload (bypasses Livewire WithFileUploads / tmpfile())
    public function setExcelFilePath(string $path)
    {
        $this->excelFilePath = $path;
    }

    public function showImportData()
    {
        if (!$this->excelFilePath || !file_exists(storage_path('app/' . $this->excelFilePath))) {
            $this->dispatch('alert', [
                'message' => 'Please select an Excel file first!',
                'messageType' => 'warning',
                'position' => 'top-right',
            ]);
            return;
        }

        $headers = [
            'powas_id',             // A
            'user_id',              // B
            'member_id',            // C
            'member_name',          // D
            'reading',              // E
            'reading_count',        // F
            'reading_date',         // G
        ];

        $absolutePath = storage_path('app/' . $this->excelFilePath);

        try {
            $collection = Excel::toArray(new ImportReadingTemplate, $absolutePath);

            $this->reset(['importCollection']);

            foreach ($collection as $key) {
                foreach ($key as $value) {
                    $this->importCollection[] = $value;
                }
            }

            $this->showingImportDataModal = true;
        } catch (\Exception $e) {
            $this->excelFilePath = null;

            $this->dispatch('alert', [
                'message' => 'An error occured while importing the file! Please check for blank cells or invalid data encoded!',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
        }
    }

    public function createReadingTemplate()
    {
        if ($this->getSaveCount() > 0) {
            $this->showingCountErrorModal = true;
        } else {
            $preset = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_applications.powas_id', $this->powasID)
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->get();

            if ($preset->count() == 0) {
                $this->dispatch('alert', [
                    'message' => 'Import is not possible because there is no members yet at ' . $this->powas->barangay . ' POWAS ' . $this->powas->phase . '!',
                    'messageType' => 'warning',
                    'position' => 'top-right',
                ]);
            } else {
                return redirect()->route('create-reading-template', ['powasID' => $this->powasID]);
            }
        }
    }

    public function importExcelFile()
    {
        if (!$this->excelFilePath || !file_exists(storage_path('app/' . $this->excelFilePath))) {
            $this->dispatch('alert', [
                'message' => 'Please select an Excel file first!',
                'messageType' => 'warning',
                'position' => 'top-right',
            ]);
            return;
        }

        $absolutePath = storage_path('app/' . $this->excelFilePath);

        try {
            Excel::import(new ImportReadingTemplate(), $absolutePath);

            // Clean up the uploaded file after successful import
            @unlink(storage_path('app/' . $this->excelFilePath));
            $this->excelFilePath = null;

            $this->dispatch('alert', [
                'message' => 'Excel file successfully imported!',
                'messageType' => 'success',
                'position' => 'top-right',
            ]);
            $this->dispatch('reloadBillings');
        } catch (\Exception $e) {
            $this->excelFilePath = null;

            $this->dispatch('alert', [
                'message' => 'An error occured while importing the file! Please check for blank cells or invalid data encoded!',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
        }

        $this->showingExcelImportModal = false;
    }

    public function printReadingSheet()
    {
        $reading_day = $this->powasSettings->reading_day;

        if ($reading_day < 10) {
            $reading_day = '0' . $this->powasSettings->reading_day;
        }

        $readingDates = Readings::select(DB::raw('DISTINCT(reading_date)'))
            ->where('reading_count', '>', 0)
            ->where('powas_id', $this->powasID)
            ->orderByDesc('reading_date')
            ->limit(24)
            ->get();

        $this->readingDate = Carbon::parse($readingDates[0]->reading_date)->format('Y-m-' . $reading_day);
        // $this->readingDate = Carbon::parse($readingDates[0]->reading_date)->addMonth()->format('Y-m-' . $reading_day);

        $this->showingReadingDateSelector = true;
    }

    public function getSaveCount()
    {
        $savedCount = 0;

        $membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_status', 'ACTIVE')
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        foreach ($membersList as $member) {
            $readingRecord = Readings::where('member_id', $member->member_id)
                ->orderBy('reading_count', 'desc')
                ->first();

            if ($readingRecord != null) {
                if ($readingRecord->count() > 1 || $readingRecord->reading_count > 1) {
                    $lastReadingDate = Carbon::parse($readingRecord->reading_date);
                    $elapsedDays = $lastReadingDate->diffInDays(Carbon::now(), false);

                    if ($elapsedDays <= 20) {
                        if ($readingRecord->count() == 1) {
                            $savedCount++;
                        } else {
                            $savedCount++;
                        }
                    }
                }
            }
        }

        return $savedCount;
    }

    public function render()
    {
        $users = User::all();
        $members = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        $readingDates = Readings::select(DB::raw('DISTINCT(reading_date)'))
            ->where('reading_count', '>', 0)
            ->where('powas_id', $this->powasID)
            ->orderByDesc('reading_date')
            ->limit(24)
            ->get();

        $query = Readings::join('powas_members', 'readings.member_id', '=', 'powas_members.member_id')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->join('users', 'readings.recorded_by', '=', 'users.user_id')
            ->where('readings.powas_id', $this->powasID);

        if ($this->startDate != '' && $this->endDate != '') {
            $query->whereBetween('readings.reading_date', [date($this->startDate), date($this->endDate)]);
        }

        if ($this->search) {
            $searchTerm = '%' . strtoupper($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('powas_applications.lastname', 'like', $searchTerm)
                    ->orWhere('powas_applications.firstname', 'like', $searchTerm)
                    ->orWhere('powas_applications.middlename', 'like', $searchTerm)
                    ->orWhere('powas_members.member_id', 'like', $searchTerm);
            });
        }

        $powasReadings = $query->orderBy('readings.reading_date', 'desc')
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->paginate($this->pagination, ['*'], 'readings');

        $usersList = [];
        $previousReadingList = [];
        $presentReadingList = [];

        foreach ($users as $key => $value) {
            $usersList[$value->user_id] = ($value->userinfo?->lastname ?? 'Unknown') . ', ' . ($value->userinfo?->firstname ?? '');
        }

        $checkBilling = Readings::where('powas_id', $this->powasID)->count();

        $billingMonth = '';

        if ($checkBilling > 0) {
            $billingMonth = Carbon::parse(Readings::orderBy('reading_date', 'DESC')->first()->reading_date)->subDays(15)->format('F Y');

            foreach ($members as $key => $value) {
                $prevReading = Readings::where('member_id', $value->member_id)
                    ->orderBy('reading_date', 'DESC')->offset(1)->first();

                $presReading = Readings::where('member_id', $value->member_id)
                    ->orderBy('reading_date', 'DESC')->first();


                if ($prevReading == null) {
                    $prevRead = '0.00';
                } else {
                    $prevRead = $prevReading['reading'];
                }

                if ($presReading == null) {
                    $presRead = '0.00';
                } else {
                    $presRead = $presReading['reading'];
                }


                $presentReadingList[$value->member_id] = $presRead;

                $previousReadingList[$value->member_id] = $prevRead;
            }
        }

        // dd($previousReadingList);

        return view('livewire.powas.powas-readings', [
            'powasReadings' => $powasReadings,
            'usersList' => $usersList,
            'previousReadingList' => $previousReadingList,
            'presentReadingList' => $presentReadingList,
            'savedCount' => $this->getSaveCount(),
            'billingMonth' => $billingMonth,
            'readingDates' => $readingDates,
        ]);
    }
}
