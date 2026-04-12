<?php

namespace App\Livewire\Billings;

use App\Models\Billings;
use App\Models\Powas;
use App\Models\PowasSettings;
use App\Models\Readings;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class BillPrinter extends Component
{
    public $billingIDs;
    public $paper_size;
    public $billings = [];
    public $old_paper_size;
    public $jsonData;
    public $thermal_paper = [];
    public $thermal_paper_default_length = 30000;
    public $divHeight = 0;
    public $printCount = [];

    public function savePageSettings()
    {
        $billingInfo = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('billings.billing_id', $this->billingIDs[0])->first();

        $powasSettings = PowasSettings::where('powas_id', $billingInfo->powas_id)->first();

        $powasSettings->bill_paper_size = $this->paper_size;
        $powasSettings->save();

        $this->dispatch('alert', [
            'message' => 'Paper size successfully updated!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);

        $this->old_paper_size = $powasSettings->bill_paper_size;
    }

    public function resetThermal()
    {
        if (!is_array($this->thermal_paper)) {
            $this->thermal_paper = [];
        }

        $this->thermal_paper['length'] = $this->thermal_paper_default_length;
        Storage::put('thermal_paper.json', json_encode($this->thermal_paper));
    }

    public function getPaperHeight($divHeight)
    {
        $this->divHeight = ($divHeight / 2.02) / 2;
    }

    public function updatePrintCount()
    {
        foreach ($this->billingIDs as $value) {
            $billToUpdate = Billings::find($value);
            $billToUpdate->print_count = $billToUpdate->print_count + 1;
            $billToUpdate->save();
            $this->printCount[$billToUpdate->billing_id] = $billToUpdate->print_count;
        }

        if ($this->paper_size == '80mm') {
            $newPaperSize = $this->thermal_paper['length'] - $this->divHeight;

            $this->thermal_paper['length'] = $newPaperSize;
            Storage::put('thermal_paper.json', json_encode($this->thermal_paper));
        }
    }

    public function mount($billingIDs)
    {
        $this->billingIDs = $billingIDs;

        if (Storage::exists('thermal_paper.json')) {
            $this->jsonData = Storage::get('thermal_paper.json');
            $this->thermal_paper = json_decode($this->jsonData, true);
        } else {
            Storage::put('thermal_paper.json', json_encode(['length' => $this->thermal_paper_default_length]));
            $this->jsonData = Storage::get('thermal_paper.json');
            $this->thermal_paper = json_decode($this->jsonData, true);
        }

        foreach ($billingIDs as $value) {
            $billingInfo = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
                ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('billings.billing_id', $value)->first();

            $powas = Powas::find($billingInfo->powas_id);
            $powasSettings = PowasSettings::where('powas_id', $billingInfo->powas_id)->first();

            $previous_balances = Billings::where('member_id', $billingInfo->member_id)
                ->where('bill_status', 'UNPAID')
                ->whereDate('billing_month', '<', $billingInfo->billing_month)
                ->orderBy('billing_month', 'desc')->get();

            $previous_balance = 0;
            $additionalFee = 0;

            foreach ($previous_balances as $key) {
                $previous_balance = $previous_balance + $key->billing_amount;
            }

            if (count($previous_balances) > 0) {
                $additionalFee = $powasSettings->reconnection_fee;
            }

            $presentReading = Readings::where('reading_id', $billingInfo->present_reading_id)->first()->reading;
            $previousReading = Readings::where('reading_id', $billingInfo->previous_reading_id)->first()->reading;

            $excessPayment = 0;

            $isExistsPreviousBill = Billings::where('member_id', $billingInfo->member_id)
                ->where('billings.billing_month', Carbon::parse($billingInfo->billing_month)->subMonth(1)->format('Y-m-01'))->exists();

            if ($isExistsPreviousBill == true) {
                $previousBillID = Billings::where('member_id', $billingInfo->member_id)
                    ->where('billings.billing_month', Carbon::parse($billingInfo->billing_month)->subMonth(1)->format('Y-m-01'))->first()->billing_id;

                $isExistsExcessPayment = Transactions::where('paid_to', $previousBillID)
                    ->where('account_number', '208')
                    ->where('transaction_side', 'CREDIT')
                    ->exists();

                if ($isExistsExcessPayment == true) {
                    $excessPayment = Transactions::where('paid_to', $previousBillID)
                        ->where('account_number', '208')
                        ->where('transaction_side', 'CREDIT')
                        ->first()->amount;
                }
            }

            $totalAmountDue = ($previous_balance + $billingInfo->billing_amount + $powasSettings->members_micro_savings + $billingInfo->penalty + $additionalFee) - $billingInfo->discount_amount - $excessPayment;

            $this->billings[$value] = [
                'billing_id' => $billingInfo->billing_id,
                'timestamp' => $billingInfo->created_at,
                'powas_name' => $powas->barangay . ' POWAS ' . $powas->phase,
                'powas_address' => $powas->zone . ', ' . $powas->barangay . ', ' . $powas->municipality . ', ' . $powas->province,
                'account_name' => $billingInfo->lastname . ', ' . $billingInfo->firstname,
                'account_number' => $billingInfo->member_id,
                'bill_number' => $billingInfo->bill_number,
                'print_count' => $billingInfo->print_count,
                'billing_month' => Carbon::parse($billingInfo->billing_month)->format('F Y'),
                'due_date' => Carbon::parse($billingInfo->due_date)->format('F d, Y'),
                'disconnection_date' => Carbon::parse($billingInfo->due_date)->addDays($powasSettings->days_before_disconnection)->format('F d, Y'),
                'present_reading' => $presentReading,
                'previous_reading' => $previousReading,
                'cubic_meter_used' => $billingInfo->cubic_meter_used,
                'billing_period' => Carbon::parse($billingInfo->cut_off_start)->addDay()->format('M d, Y') . ' - <br>' . Carbon::parse($billingInfo->cut_off_end)->format('M d, Y'),
                'billing_amount' => number_format($billingInfo->billing_amount, 2),
                'previous_balance' => number_format($previous_balance, 2),
                'excess_payment' => number_format($excessPayment, 2),
                'discount_amount' => number_format($billingInfo->discount_amount, 2),
                'penalty' => number_format($billingInfo->penalty, 2),
                'reconnection_fee' => $additionalFee,
                'members_micro_savings' => number_format($powasSettings->members_micro_savings, 2),
                'total_amount_due' => number_format($totalAmountDue, 2),
                'is_minimum' => $billingInfo->cubic_meter_used <= 5,
            ];

            $this->printCount[$billingInfo->billing_id] = $billingInfo->print_count;
        }

        $this->paper_size = $powasSettings->bill_paper_size;
        $this->old_paper_size = $powasSettings->bill_paper_size;
    }

    public function render()
    {
        return view('livewire.billings.bill-printer');
    }
}
