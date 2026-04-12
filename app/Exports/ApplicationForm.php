<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApplicationForm implements FromView
{
    public function view(): View
    {
        return view('livewire.exports.application-form');
    }
}
