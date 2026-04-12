<?php

namespace App\Http\Controllers\Billings;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionSheetController extends Controller
{
    public function view($powasID, $billingMonth = null): View
    {
        $powas = Powas::find($powasID);
        return view('billings.collection-sheet', [
            'powasID' => $powasID,
            'powas' => $powas,
            'billingMonth' => $billingMonth,
        ]);
    }
}
