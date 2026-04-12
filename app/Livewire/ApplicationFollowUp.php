<?php

namespace App\Livewire;

use App\Models\PowasApplications;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ApplicationFollowUp extends Component
{
    public $referencenumber;
    public $showingReferenceResultModal = false;
    public $applicationstatus;
    public $isExists = false;
    public $inputconfirmation;
    public $showingDeleteConfirmationModal = false;

    public function showDeleteConfirmationModal()
    {
        $this->resetErrorBag();
        $this->showingDeleteConfirmationModal = true;
    }

    protected $rules = [
        'referencenumber' => 'required|size:8',
    ];

    protected $validationAttributes = [
        'referencenumber' => 'reference number',
    ];

    public function render()
    {
        return view('livewire.application-follow-up');
    }

    public function showReferenceResultModal()
    {
        $this->validate();

        $this->isExists = PowasApplications::where('application_id', $this->referencenumber)->exists();

        if ($this->isExists == true) {
            $this->resetErrorBag();
            $this->showingReferenceResultModal = true;
            $this->applicationstatus = PowasApplications::where('application_id', $this->referencenumber)->get();

            $this->applicationstatus = $this->applicationstatus[0];
        } else {
            // $this->addError('referencenumber', 'Reference number cannot be found!');
            $this->dispatch('notfound', [
                'message' => 'Reference number \'' . $this->referencenumber . '\' cannot be found!',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
        }
    }

    public function deleteApplication()
    {
        if ($this->applicationstatus->application_id != $this->inputconfirmation) {
            $this->addError('inputconfirmation', 'Confirmation code does not match!');
        } else {
            $application = PowasApplications::find($this->referencenumber)->first();
            Storage::delete('public/ids/' . $application->id_path);
            $application->delete();

            $this->reset([
                'referencenumber',
            ]);
            $this->dispatch('notfound', [
                'message' => 'Application successfully deleted!',
                'messageType' => 'success',
                'position' => 'top-right',
            ]);
            $this->showingReferenceResultModal = false;
            $this->showingDeleteConfirmationModal = false;
        }
    }
}
