<?php

namespace App\Http\Controllers\POWAS;

use App\Http\Controllers\Controller;
use App\Models\PowasApplications;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PowasApplicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('powas.apply');
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
    public function show(PowasApplications $powasApplications)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PowasApplications $powasApplications)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PowasApplications $powasApplications)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PowasApplications $powasApplications)
    {
        //
    }
}
