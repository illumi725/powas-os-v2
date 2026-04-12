<?php

namespace App\Http\Controllers\POWAS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowMembersListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('powas.show-members-list');
    }
}
