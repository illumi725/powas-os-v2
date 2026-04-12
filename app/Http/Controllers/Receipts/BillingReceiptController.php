<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingReceiptController extends Controller
{
    public function index(Request $request)
    {
        $billingIDs = $request->query('billingIDs');
        $advancePrint = $request->query('advancePrint');

        $billingIDs = json_decode($billingIDs);

        return view('receipts.billing-receipt', [
            'billingIDs' => $billingIDs,
            'advancePrint' => $advancePrint,
        ]);
    }
}
