<?php

namespace App\Http\Controllers\Applications;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ViewApplicationController extends Controller
{
    public function index($applicationid): View
    {
        return view('applications.application-view');
    }
}
