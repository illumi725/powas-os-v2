<?php

namespace App\Http\Controllers\POWAS;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagePowasController extends Controller
{
    public function index($powas_id): View
    {
        return view('powas.manage-powas', ['powas_id' => $powas_id]);
    }
}
