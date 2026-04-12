<?php

namespace App\Livewire\Voucher;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PrintingExpensesAttachment extends Component
{
    public $date_of_expense;
    public $payee;
    public $rows = [];
    public $grandTotal = 0;

    public function mount()
    {
        $this->date_of_expense = now()->format('Y-m-d');
        $this->payee = '';
        $this->rows = [
            ['type' => '', 'quantity' => 0, 'unit_price' => 0, 'amount' => 0]
        ];
        $this->calculateTotal();
    }

    public function addRow()
    {
        $this->rows[] = ['type' => '', 'quantity' => 0, 'unit_price' => 0, 'amount' => 0];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotal();
    }

    public function updatedRows($value, $key)
    {
        // Recalculate amount for each row whenever rows change
        foreach ($this->rows as $index => $row) {
            $quantity = (float) ($row['quantity'] ?? 0);
            $unitPrice = (float) ($row['unit_price'] ?? 0);
            $this->rows[$index]['amount'] = $quantity * $unitPrice;
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->grandTotal = 0;
        foreach ($this->rows as $row) {
            $this->grandTotal += (float) ($row['amount'] ?? 0);
        }
    }

    public function render()
    {
        return view('livewire.voucher.printing-expenses-attachment');
    }
}
