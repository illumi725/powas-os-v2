<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccounts;
use App\Models\Powas;
use App\Models\PrintLogs;
use App\Models\Transactions;
use App\Models\Transanctions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtherReceiptController extends Controller
{
    public $printIDsList = [];

    public function view(Request $request): View
    {
        $trxnIDs = $request->query('trxnID');

        $trxnIDs = json_decode($trxnIDs);

        $this->printIDsList = $request->query('printID');

        $this->printIDsList = json_decode($this->printIDsList);

        $receiptNumber = $request->query('receiptNumber');

        $powasID = $request->query('powasID');

        $trxnList = [];

        foreach ($trxnIDs as $value) {
            $trxn = Transactions::find($value);
            $powas = Powas::find($trxn->powas_id);
            $recorded_by = User::find($trxn->recorded_by_id);
            $particulars = ChartOfAccounts::find($trxn->account_number);

            $trxnItem = [
                'receipt_no' => $trxn->trxn_id,
                'powas_name' => $powas->barangay . ' POWAS ' . $powas->phase,
                'powas_address' => $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province,
                'transact_by' => $recorded_by->userinfo->firstname,
                'transact_date' => $trxn->transaction_date . ' ' . Carbon::parse($trxn->created_at)->format('H:i:s'),
                'received_from' => $trxn->received_from,
                'particular' => $particulars->account_name,
                'description' => $trxn->description,
                'alias' => $particulars->alias,
                'amount' => $trxn->amount,
            ];

            $trxnList[] = $trxnItem;
        }

        return view('receipts.other-receipts', [
            'trxnList' => $trxnList,
            'printIDsList' => $this->printIDsList,
            'receiptNumber' => $receiptNumber,
            'powasID' => $powasID,
        ]);
    }
}
