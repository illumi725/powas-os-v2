<?php

namespace App\Http\Controllers\POWAS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowPowasListController extends Controller
{
    public function index(): View
    {
        return view('powas.show-powas-list');
    }
}
