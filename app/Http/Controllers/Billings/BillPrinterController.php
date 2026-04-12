<?php

namespace App\Http\Controllers\Billings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillPrinterController extends Controller
{
    public function view(Request $request)
    {
        $billingIDs = $request->query('toPrint');

        $billingIDs = json_decode($billingIDs);

        return view('billings.bill-print', [
            'billingIDs' => $billingIDs,
        ]);
    }
}
