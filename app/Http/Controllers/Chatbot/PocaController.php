<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PocaController extends Controller
{
    public function index(): View
    {
        return view('chatbot.poca');
    }
}
