<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionsListController extends Controller
{
    public function index($powasID): View
    {
        $powas = Powas::find($powasID);

        return view('transactions.transactions-list', [
            'powasID' => $powasID,
            'powas' => $powas,
        ]);
    }

    public function accounting($powasID, $transactionMonth): View
    {
        $powas = Powas::find($powasID);

        return view('accounting.fis', [
            'powasID' => $powasID,
            'powas' => $powas,
            'transactionMonth' => $transactionMonth,
        ]);
    }

    public function printVoucher($powasID, $voucherID = null): View
    {
        $powas = Powas::find($powasID);

        return view('voucher.voucher-print', [
            'powasID' => $powasID,
            'powas' => $powas,
            'voucherID' => $voucherID,
        ]);
    }
}
