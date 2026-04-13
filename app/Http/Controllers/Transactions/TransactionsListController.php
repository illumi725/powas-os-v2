<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Powas;
use App\Models\Vouchers;
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

    public function booksOfAccounts($powasID): View
    {
        $powas = Powas::find($powasID);

        return view('accounting.books', [
            'powasID' => $powasID,
            'powas'   => $powas,
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

    public function bulkPrintVouchers($powasID, $startDate, $endDate): View
    {
        $powas = Powas::find($powasID);

        $vouchers = Vouchers::with('voucherparticulars')
            ->where('powas_id', $powasID)
            ->whereBetween('voucher_date', [$startDate, $endDate])
            ->orderBy('voucher_date', 'asc')
            ->orderBy('voucher_number', 'asc')
            ->get();

        return view('voucher.voucher-bulk-print', [
            'powasID'   => $powasID,
            'powas'     => $powas,
            'vouchers'  => $vouchers,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }
}
