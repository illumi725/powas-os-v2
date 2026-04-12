<?php

namespace App\Http\Controllers\Readings;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReadingSheetController extends Controller
{
    public function view($powasID, $readingDate = null): View
    {
        $powas = Powas::find($powasID);
        return view('readings.reading-sheet', [
            'powasID' => $powasID,
            'powas' => $powas,
            'readingDate' => $readingDate,
        ]);
    }
}
