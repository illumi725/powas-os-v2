<?php

namespace App\Http\Controllers;

use App\Models\PowasMembers;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PowasMembersController extends Controller
{
    public function personalInfo($memberID): View
    {
        return view('members.manage-member-profile', [
            'memberID' => $memberID,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(PowasMembers $powasMembers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PowasMembers $powasMembers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PowasMembers $powasMembers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PowasMembers $powasMembers)
    {
        //
    }
}
