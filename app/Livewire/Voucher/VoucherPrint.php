<?php

namespace App\Livewire\Voucher;

use App\Models\Powas;
use App\Models\Transactions;
use App\Models\Vouchers;
use Livewire\Component;
use NumberFormatter;

class VoucherPrint extends Component
{
    public $powasID;
    public $powas;
    public $voucherID;
    public $voucherInfo;
    public $transactionInfo;
    public $inWords;
    public $nextVoucherID;
    public $prevVoucherID;

    public function mount($powasID, $powas, $voucherID)
    {
        $this->powasID = $powasID;
        $this->powas = $powas;
        $this->voucherID = $voucherID;

        $this->voucherInfo = Vouchers::with('voucherparticulars')->find($voucherID);

        $this->transactionInfo = Transactions::where('trxn_id', $this->voucherInfo->trxn_id)->first();

        $inWordsFormatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);

        $this->inWords = strtoupper($inWordsFormatter->format($this->voucherInfo->amount)) . ' PESOS ONLY';

        // Calculate Previous Voucher
        $prevVoucher = Vouchers::where('powas_id', $this->powasID)
            ->where(function ($query) {
                $query->where('voucher_date', '<', $this->voucherInfo->voucher_date)
                    ->orWhere(function ($q) {
                        $q->where('voucher_date', $this->voucherInfo->voucher_date)
                            ->where('voucher_number', '<', $this->voucherInfo->voucher_number);
                    });
            })
            ->orderBy('voucher_date', 'desc')
            ->orderBy('voucher_number', 'desc')
            ->first();
        $this->prevVoucherID = $prevVoucher ? $prevVoucher->voucher_id : null;

        // Calculate Next Voucher
        $nextVoucher = Vouchers::where('powas_id', $this->powasID)
            ->where(function ($query) {
                $query->where('voucher_date', '>', $this->voucherInfo->voucher_date)
                    ->orWhere(function ($q) {
                        $q->where('voucher_date', $this->voucherInfo->voucher_date)
                            ->where('voucher_number', '>', $this->voucherInfo->voucher_number);
                    });
            })
            ->orderBy('voucher_date', 'asc')
            ->orderBy('voucher_number', 'asc')
            ->first();
        $this->nextVoucherID = $nextVoucher ? $nextVoucher->voucher_id : null;
    }

    public function render()
    {
        return view('livewire.voucher.voucher-print');
    }
}
