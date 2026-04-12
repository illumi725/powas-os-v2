<?php

namespace App\Http\Controllers;

use App\Models\Powas;
use App\Models\Readings;
use App\Exports\CreateReadingTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($powasID): View
    {

        $powas = Powas::find($powasID);
        return view('readings.add-reading-manually', [
            'powasID' => $powasID,
            'powas' => $powas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Readings $readings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Readings $readings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Readings $readings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Readings $readings)
    {
        //
    }
    public function createReadingTemplate($powasID)
    {
        return (new CreateReadingTemplate(auth()->id(), $powasID))->download('reading-template.xlsx');
    }
}
