<?php

namespace App\Imports;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\PowasMembers;
use App\Models\Readings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportReadingTemplate implements ToModel, WithEvents, WithHeadingRow
{
    use Importable, RegistersEventListeners;
    public $importLogs = [];
    public static $duplicates = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $readingDate = Carbon::parse(Date::excelToDateTimeObject($row['reading_date']));
        $readingID = CustomNumberFactory::getRandomID();

        $importer = User::find($row['user_id']);
        $member = PowasMembers::find($row['member_id']);

        $memberName = $member->applicationinfo->lastname . ', ' . $member->applicationinfo->firstname;

        $toCheck = [
            'member_id' => $row['member_id'],
            'powas_id' => $row['powas_id'],
            'recorded_by' => $row['user_id'],
            'reading' => $row['reading'],
            'reading_date' => $readingDate,
            'reading_count' => $row['reading_count'],
        ];

        $isExists = Readings::where($toCheck)->exists();

        if ($isExists == false) {

            $log_message = '<b><u>' . $importer->userinfo->lastname . ', ' . $importer->userinfo->firstname . '</u></b> created reading record for <b><i>' . strtoupper($memberName) . '</i></b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'members', $row['powas_id']);

            return new Readings([
                'reading_id' => $readingID,
                'member_id' => $row['member_id'],
                'powas_id' => $row['powas_id'],
                'recorded_by' => $row['user_id'],
                'reading' => $row['reading'],
                'reading_date' => $readingDate,
                'reading_count' => $row['reading_count'],
            ]);
        }
    }
}
