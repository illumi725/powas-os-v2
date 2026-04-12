<?php

namespace App\Livewire\Receipts;

use App\Models\IssuedReceipts;
use Livewire\Component;

class OtherReceipts extends Component
{
    public $trxnList;
    public $printIDsList;
    public $receiptNumber;
    public $powasID;
    public $thisReceipt;
    public $receipt_paper_size = '80mm';

    public function mount($trxnList, $printIDsList, $receiptNumber, $powasID)
    {
        $this->trxnList = $trxnList;
        $this->printIDsList = $printIDsList;
        $this->receiptNumber = $receiptNumber;
        $this->powasID = $powasID;
        $this->thisReceipt = IssuedReceipts::where('receipt_number', $receiptNumber)->first();
    }

    public function render()
    {
        return view('livewire.receipts.other-receipts');
    }

    public function updatePrintLog()
    {
        $printLog = IssuedReceipts::where('receipt_number', $this->receiptNumber)
            ->where('powas_id', $this->powasID)
            ->get();

        foreach ($printLog as $log) {
            $printCount = $log->print_count;
            $printCount++;
            $log->is_printed = 'YES';
            $log->print_count = $printCount;
            $log->save();
        }
    }
}
