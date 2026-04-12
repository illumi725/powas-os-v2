<?php

namespace App\Livewire\Voucher;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AllowanceAttachment extends Component
{
    public $date_of_disbursement;
    public $rows = [];
    public $grandTotal = 0;

    public function mount()
    {
        $this->date_of_disbursement = now()->format('Y-m-d');
        $this->rows = [
            ['name' => '', 'designation' => '', 'amount' => 0]
        ];
        $this->calculateTotal();
    }

    public function addRow()
    {
        $this->rows[] = ['name' => '', 'designation' => '', 'amount' => 0];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotal();
    }

    public function updatedRows($value, $key)
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->grandTotal = 0;
        foreach ($this->rows as $row) {
            $this->grandTotal += (float) $row['amount'];
        }
    }

    public function render()
    {
        $roles = \Spatie\Permission\Models\Role::pluck('name')->toArray();
        
        // Add specific designations requested by the user
        $designations = array_unique(array_merge($roles, ['Meter Installer', 'Tank Cleaner']));
        sort($designations);

        return view('livewire.voucher.allowance-attachment', [
            'availableRoles' => $designations,
        ]);
    }
}
