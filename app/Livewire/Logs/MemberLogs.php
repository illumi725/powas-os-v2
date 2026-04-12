<?php

namespace App\Livewire\Logs;

use App\Models\PowasOsLogs;
use Livewire\Component;
use Livewire\WithPagination;

class MemberLogs extends Component
{
    use WithPagination;
    public $comView = 'livewire.powas.members-cards-list';

    public $showingLogsModal = false;

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
        $changesLogs = PowasOsLogs::where('log_blade', 'members')
            ->orderBy('created_at', 'desc')->paginate(10);
        return view('livewire.logs.member-logs', [
            'changesLogs' => $changesLogs,
        ]);
    }
}
