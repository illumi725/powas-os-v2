<?php

namespace App\Http\Controllers\CSV;

use App\Exports\CreateReadingTemplate;
use App\Exports\MembersCSVTemplate;
use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;

class CsvController extends Controller
{
    public function membersCSVTemplate($powasID, $numberOfMembers)
    {
        $user = Auth::user();
        $powas = Powas::find($powasID);

        return (new MembersCSVTemplate($user->user_id, $powasID, $numberOfMembers))
            ->download($powas->barangay . ' ' . $powas->phase . ' Members Template ' . date_format(now(), 'Ymd_His') . '.xlsx', Excel::XLSX);
    }

    public function readingImportTemplate($powasID)
    {
        $user = Auth::user();
        $powas = Powas::find($powasID);

        return (new CreateReadingTemplate($user->user_id, $powasID))
            ->download($powas->barangay . ' ' . $powas->phase . ' Reading Template ' . date_format(now(), 'Ymd_His') . '.xlsx', Excel::XLSX);
    }
}
