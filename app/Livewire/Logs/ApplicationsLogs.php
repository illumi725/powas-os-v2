<?php

namespace App\Livewire\Logs;

use App\Models\PowasOsLogs;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationsLogs extends Component
{
    use WithPagination;
    public $showingLogsModal = false;

    public $comView = 'livewire.powas.applications-cards-list';

    public function paginationView()
    {
        return 'pagination.custom-pagination';
    }

    public function showLogsModal()
    {
        $this->showingLogsModal = true;
    }

    public function render()
    {
        $changesLogs = PowasOsLogs::where('log_blade', 'applications')
            ->orderBy('created_at', 'desc')->paginate(10);
        return view('livewire.logs.applications-logs', [
            'changesLogs' => $changesLogs,
        ]);
    }
}
