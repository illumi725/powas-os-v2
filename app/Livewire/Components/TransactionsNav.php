<?php

namespace App\Livewire\Components;

use Livewire\Component;

class TransactionsNav extends Component
{
    public $powasID;
    public $powas;
    public $currentView;
    public $showData = 'block';
    public $shown = false;

    public function mount($powasID, $powas)
    {
        $this->powasID = $powasID;
        $this->powas = $powas;

        $this->currentView = 'journal-entries';
    }

    public function changeView($viewType)
    {
        $this->shown = true;
        $this->dispatch('show-loading', [
            'returnView' => $viewType,
        ]);
        $this->showData = 'hidden';
    }

    public function setView($viewType)
    {
        $this->currentView = $viewType;
        $this->showData = 'block';
        $this->shown = false;
    }

    public function render()
    {
        return view('livewire.components.transactions-nav');
    }
}
