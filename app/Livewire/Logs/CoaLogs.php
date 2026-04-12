<?php

namespace App\Livewire\Logs;

use App\Models\PowasOsLogs;
use Livewire\Component;
use Livewire\WithPagination;

class CoaLogs extends Component
{
    use WithPagination;
    public $comView = 'livewire.settings.chart-of-accounts';

    public $showingLogsModal = false;

    public function showLogsModal()
    {
        $this->showingLogsModal = true;
    }

    public function paginationView()
    {
        return 'pagination.custom-pagination';
    }
    public function render()
    {
        $changesLogs = PowasOsLogs::where('log_blade', 'chart-of-accounts')
            ->orderBy('created_at', 'desc')->paginate(10);
        return view('livewire.logs.coa-logs', [
            'changesLogs' => $changesLogs,
        ]);
    }
}
