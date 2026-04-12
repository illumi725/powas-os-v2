<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddMemberController extends Controller
{
    public function index($powasID): View
    {
        $selectedPOWAS = Powas::where('powas_id', $powasID)->firstOrFail();

        return view('members.add-member', ['selectedPOWAS' => $selectedPOWAS]);
    }
}
