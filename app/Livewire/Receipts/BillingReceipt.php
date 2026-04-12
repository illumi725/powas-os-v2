<?php

namespace App\Livewire\Receipts;

use App\Models\Billings;
use App\Models\ChartOfAccounts;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class BillingReceipt extends Component
{
    public $billingIDs;
    public $powas;
    public $powasSettings;

    public $receiptToPrint = [];

    public $receipt_paper_size;
    public $printDuplicate = 'consumer';
    public $old_paper_size;
    public $transactionSets = [];
    public $hasMonthlyDue = false;
    public $isAdvancePrinting;

    public function savePageSettings()
    {
        $billingInfo = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('billings.billing_id', $this->billingIDs)->first();

        $powasSettings = PowasSettings::where('powas_id', $billingInfo->powas_id)->first();

        $powasSettings->receipt_paper_size = $this->receipt_paper_size;
        $powasSettings->save();

        $this->dispatch('alert', [
            'message' => 'Paper size successfully updated!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->old_paper_size = $powasSettings->receipt_paper_size;
    }

    public function mount($billingIDs, $advancePrint = null)
    {
        $this->billingIDs = $billingIDs;

        $this->isAdvancePrinting = $advancePrint;

        if ($advancePrint == null) {
            foreach ($billingIDs as $billingID) {
                $transaction = Transactions::where('paid_to', $billingID)
                    ->orderBy('journal_entry_number', 'asc')
                    ->orderBy('account_number', 'asc')
                    ->get();
                $thisBilling = Billings::find($billingID);

                $account_numbers = [];
                $cashReceived = [];
                $amountDue = [];
                $monthlyDue = [];
                $penalties = [];
                $reconnectionFee = [];
                $discounts = [];
                $excessPayments = [];
                $debitedExcessPayments = [];
                $totalAmountDue = [];

                $amountCounter = 0;

                foreach ($transaction as $value) {
                    $account_numbers[] = $value->account_number;
                    $chartOfAccounts = ChartOfAccounts::where('account_number', $value->account_number)->first();

                    if ($value->account_number == '105' && $value->transaction_side == 'CREDIT') {
                        $baseTransaction = $value;
                        $amountDue = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter + $value->amount;
                    }

                    if ($value->account_number == '101' && $value->transaction_side == 'DEBIT') { // && $value->order == '1') {
                        $cashReceived = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                    }

                    if ($value->account_number == '201' && $value->transaction_side == 'CREDIT') {
                        $monthlyDue = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter + $value->amount;
                        $this->hasMonthlyDue = true;
                    }

                    if ($value->account_number == '405' && $value->transaction_side == 'CREDIT') {
                        $penalties = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter + $value->amount;
                    }

                    if ($value->account_number == '404' && $value->transaction_side == 'CREDIT') {
                        $reconnectionFee = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter + $value->amount;
                    }

                    if ($value->account_number == '407' && $value->transaction_side == 'DEBIT') {
                        $discounts = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter - $value->amount;
                    }

                    if ($value->account_number == '208' && $value->transaction_side == 'CREDIT') {
                        $excessPayments = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter + $value->amount;
                    }

                    if ($value->account_number == '208' && $value->transaction_side == 'DEBIT') {
                        $debitedExcessPayments = ['alias' => $chartOfAccounts->alias, 'amount' => $value->amount];
                        $amountCounter = $amountCounter - $value->amount;
                    }
                }

                if ($this->hasMonthlyDue == false && count($monthlyDue) == 0) {
                    $monthlyDue = ['amount' => 0];
                }

                $member_account = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')->where('powas_members.member_id', $baseTransaction->member_id)->first();

                $this->transactionSets[$billingID] = [
                    'member_account_name' => $member_account->lastname . ', ' . $member_account->firstname,
                    'member_account_number' => $member_account->member_id,
                    'billing_month' => Carbon::parse(Billings::find($baseTransaction->paid_to)->billing_month)->format('F Y'),
                    'transact_by' => User::find($baseTransaction->recorded_by_id)->userinfo->firstname,
                    'transact_date' => $baseTransaction->transaction_date,
                    'cubic_meter_used' => $thisBilling->cubic_meter_used,
                    'due_date' => $thisBilling->due_date,
                    'cash_received' => $cashReceived,
                    'amount_due' => $amountDue,
                    'monthly_due' => $monthlyDue,
                    'penalties' => $penalties,
                    'reconnection_fee' => $reconnectionFee,
                    'discounts' => $discounts,
                    'total_amount_due' => $amountCounter,
                    'excess_payment' => $excessPayments,
                    'debited_excess_payment' => $debitedExcessPayments,
                ];
            }

            $this->powasSettings = PowasSettings::where('powas_id', $baseTransaction->powas_id)->first();
            $this->powas = Powas::find($baseTransaction->powas_id);

            $this->receipt_paper_size = $this->powasSettings->receipt_paper_size;
            $this->old_paper_size = $this->powasSettings->receipt_paper_size;
        } else {
            foreach ($billingIDs as $billingID) {
                $thisBilling = Billings::find($billingID);

                $member_account = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')->where('powas_members.member_id', $thisBilling->member_id)->first();


                $this->powasSettings = PowasSettings::where('powas_id', $thisBilling->powas_id)->first();
                $this->powas = Powas::find($thisBilling->powas_id);

                $monthlyDue = $this->powasSettings->members_micro_savings;

                $this->transactionSets[$billingID] = [
                    'member_account_name' => $member_account->lastname . ', ' . $member_account->firstname,
                    'member_account_number' => $member_account->member_id,
                    'billing_month' => Carbon::parse($thisBilling->billing_month)->format('F Y'),
                    'cubic_meter_used' => $thisBilling->cubic_meter_used,
                    'due_date' => $thisBilling->due_date,
                    'monthly_due' => ['alias' => 'Monthly Due', 'amount' => $monthlyDue],
                    'total_amount_due' => ($thisBilling->billing_amount + $thisBilling->penalty + $monthlyDue) - $thisBilling->discount_amount,
                ];
            }
            $this->receipt_paper_size = '105mm';
        }
    }

    public function render()
    {
        return view('livewire.receipts.billing-receipt');
    }
}
