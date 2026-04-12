<?php

namespace App\Http\Controllers\POWAS;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\View\View;

class BillingGenerateController extends Controller
{
    public function index($powasID, $regen): View
    {
        $powas = Powas::find($powasID);
        return view('powas.billing-generate', [
            'powasID' => $powasID,
            'powas' => $powas,
            'regen' => $regen,
        ]);
    }
}
