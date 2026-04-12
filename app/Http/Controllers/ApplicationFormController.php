<?php

namespace App\Http\Controllers;

use App\Models\Powas;
use App\Models\PowasApplications;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class ApplicationFormController extends Controller
{
    public function view($applicationid)
    {
        $applicationinfo = PowasApplications::where('application_id', $applicationid)->first();

        if ($applicationinfo == null) {
            return abort(404, message: 'Reference number cannot be found!');
        } else {
            $role = Role::where('name', 'president')->first();

            $powasinfo = Powas::where('powas_id', $applicationinfo->powas_id)->first();

            $presidentinfo = $role->users()->where('powas_id', $powasinfo->powas_id)->first();

            $age = Carbon::parse($applicationinfo->birthday)->age;

            $html = view('livewire.exports.application-form', [
                'applicationinfo' => $applicationinfo,
                'powasinfo' => $powasinfo,
                'age' => $age,
                'presidentinfo' => $presidentinfo,
            ])->render();

            return Pdf::loadHTML($html)->stream($applicationinfo->application_id . ' - ' . $applicationinfo->lastname . ', ' . $applicationinfo->firstname . '.pdf');
        }
    }

    public function download($applicationid)
    {
        $applicationinfo = PowasApplications::where('application_id', $applicationid)->first();

        if ($applicationinfo == null) {
            return abort(404, message: 'Reference number cannot be found!');
        } else {
            $role = Role::where('name', 'president')->first();

            $powasinfo = Powas::where('powas_id', $applicationinfo->powas_id)->first();

            $presidentinfo = $role->users()->where('powas_id', $powasinfo->powas_id)->first();

            $age = Carbon::parse($applicationinfo->birthday)->age;

            $html = view('livewire.exports.application-form', [
                'applicationinfo' => $applicationinfo,
                'powasinfo' => $powasinfo,
                'age' => $age,
                'presidentinfo' => $presidentinfo,
            ])->render();

            return Pdf::loadHTML($html)->download($applicationinfo->application_id . ' - ' . $applicationinfo->lastname . ', ' . $applicationinfo->firstname . '.pdf');
        }
    }
}
