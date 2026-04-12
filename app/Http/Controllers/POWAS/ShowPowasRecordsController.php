<?php

namespace App\Http\Controllers\POWAS;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowPowasRecordsController extends Controller
{
    public function index($powasID): View
    {
        $powas = Powas::find($powasID);
        $memberCount = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $powasID)->count();
        $powasSettings = PowasSettings::where('powas_id', $powasID)->firstOrFail();

        $powasErrors = [];

        if ($memberCount == 0) {
            $powasErrors[] = 'No POWAS member in the database yet. Go to MEMBERS, then click ADD MEMBER button.';
        }

        if ($powasSettings->due_date_day == null || $powasSettings->due_date_day == '') {
            $powasErrors[] = 'Due date day not yet set. Go to POWAS COOP > ACTIONS > EDIT POWAS > Enter Due Date Day.';
        }

        if ($powasSettings->reading_day == null || $powasSettings->reading_day == '') {
            $powasErrors[] = 'Reading day not yet set. Go to POWAS COOP > ACTIONS > EDIT POWAS > Enter Reading Day.';
        }

        if ($powasSettings->collection_day == null || $powasSettings->collection_day == '') {
            $powasErrors[] = 'Collection day not yet set. Go to POWAS COOP > ACTIONS > EDIT POWAS > Enter Collection Day.';
        }

        if ($powasSettings->days_before_disconnection == null || $powasSettings->days_before_disconnection == '') {
            $powasErrors[] = 'Days before disconnection not yet set. Go to POWAS COOP > ACTIONS > EDIT POWAS > Enter Days Before Disconnection.';
        }

        if ($powasSettings->penalty_per_day == null || $powasSettings->penalty_per_day == '') {
            $powasErrors[] = 'Penalty per day not yet set. Go to POWAS COOP > ACTIONS > EDIT POWAS > Enter Penalty Per Day.';
        }

        if ($powasSettings->reconnection_fee == null || $powasSettings->reconnection_fee == '') {
            $powasErrors[] = 'Reconnection fee not yet set. Go to POWAS COOP > ACTIONS > EDIT POWAS > Enter Reconnection Fee.';
        }

        return view('powas.show-powas-records', [
            'powasID' => $powasID,
            'powas' => $powas,
            'powasErrors' => $powasErrors,
        ]);
    }
}
