<?php

namespace App\Livewire\Billings;

use App\Models\Billings;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Readings;
use Carbon\Carbon;
use Livewire\Component;

class CollectionSheet extends Component
{
    public $powasID;
    public $powas;
    public $powasSettings;
    public $membersList;
    public $isMinimum = false;
    public $discountType = [];
    public $discount = [];
    public $penalty = [];
    public $discountLabel = [];
    public $toPrintBilling = [];
    public $savedCount = 0;
    public $totalAmountDues = [];
    public $billingMonth;

    public $validReadings = [];

    public function mount($powasID, $billingMonth = null)
    {
        $this->powasID = $powasID;
        $this->billingMonth = $billingMonth;
    }

    public function getDiscountValue($memberID)
    {
        if ($this->discountType[$memberID] == 'percent') {
            $discountAmount = $this->validReadings[$memberID]['billing_amount'] * ($this->discount[$memberID] / 100);
        } elseif ($this->discountType[$memberID] == 'amount') {
            $discountAmount = $this->discount[$memberID];
        }

        return round($discountAmount, 0, PHP_ROUND_HALF_UP);
    }

    public function render()
    {
        $this->powas = Powas::find($this->powasID);
        $this->powasSettings = PowasSettings::where('powas_id', $this->powasID)->first();
        $this->savedCount = 0;

        $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_status', 'ACTIVE')
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        $billingSummary = [];
        $totalBillings = 0;
        $totalCubicMeterUsed = 0;
        $totalNotMinimumBilling = 0;
        $totalNotMinimumCount = 0;
        $totalMinimumBilling = 0;
        $totalMinimumCount = 0;
        $totalBalanceFromPrevious = 0;
        $totalDiscounts = 0;
        $totalPenalties = 0;
        $totalMonthlyDue = 0;
        $totalBillingAmount = 0;
        $totalUncollectedMicroSavings = 0;

        foreach ($this->membersList as $member) {
            $readingCount = Readings::where('member_id', $member->member_id)->count();
            if ($readingCount > 1) {
                $presentReadingQuery = Readings::where('member_id', $member->member_id)
                    ->orderBy('reading_date', 'desc')
                    ->first();

                if ($this->billingMonth == null) {
                    $this->billingMonth = Carbon::parse($presentReadingQuery->reading_date)->format('Y-m-01');
                }

                $billing = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
                    ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                    ->where('billings.member_id', $member->member_id)
                    ->where('billings.billing_month', $this->billingMonth)->first();

                if ($billing != null) {

                    $previous_balances = Billings::where('member_id', $billing->member_id)
                        ->where('bill_status', 'UNPAID')
                        ->whereDate('billing_month', '<', $billing->billing_month)
                        ->orderBy('billing_month', 'desc')->get();

                    $previous_balance = 0;
                    $uncollectedMicroSavings = 0;
                    $additionalFee = 0;

                    foreach ($previous_balances as $key) {
                        $previous_balance = $previous_balance + $key->billing_amount;
                        $uncollectedMicroSavings = $uncollectedMicroSavings + $this->powasSettings->members_micro_savings;
                    }

                    if (count($previous_balances) > 0) {
                        $additionalFee = $this->powasSettings->reconnection_fee;
                    }

                    $totalBillings++;
                    $totalCubicMeterUsed = $totalCubicMeterUsed + $billing->cubic_meter_used;

                    if ($billing->cubic_meter_used <= 5) {
                        $totalMinimumCount++;
                        $totalMinimumBilling = $totalMinimumBilling + $billing->billing_amount;
                    } else {
                        $totalNotMinimumCount++;
                        $totalNotMinimumBilling = $totalNotMinimumBilling + $billing->billing_amount;
                    }

                    $totalBalanceFromPrevious = $totalBalanceFromPrevious + $previous_balance + $additionalFee;
                    $totalDiscounts = $totalDiscounts + $billing->discount_amount;
                    $totalPenalties = $totalPenalties + $billing->penalty;
                    $totalMonthlyDue = $totalMonthlyDue + $this->powasSettings->members_micro_savings;
                    $totalBillingAmount = $totalBillingAmount + $billing->billing_amount;
                    $totalUncollectedMicroSavings = $totalUncollectedMicroSavings + $uncollectedMicroSavings;

                    $this->validReadings[$member->member_id] = [
                        'billing_id' => $billing->billing_id,
                        'member_name' => $member->lastname . ', ' . $member->firstname,
                        'present_reading' => Readings::where('reading_id', $billing->present_reading_id)->first()->reading,
                        'previous_reading' => Readings::where('reading_id', $billing->previous_reading_id)->first()->reading,
                        'present_reading_id' => $billing->present_reading_id,
                        'previous_reading_id' => $billing->present_reading_id,
                        'cubic_meter_used' => number_format($billing->cubic_meter_used, 2),
                        'billing_amount' => number_format($billing->billing_amount, 2),
                        'billing_month' => Carbon::parse($billing->billing_month)->format('F Y'),
                        'due_date' => Carbon::parse($billing->due_date)->format('Y-m-d'),
                        'cut_off_start' => Carbon::parse($billing->cut_off_start)->format('Y-m-d'),
                        'cut_off_end' => Carbon::parse($billing->cut_off_start)->format('Y-m-d'),
                        'bill_number' => $billing->bill_number,
                        'print_count' => $billing->print_count,
                        'bill_status' => $billing->bill_status,
                    ];

                    $this->discount[$member->member_id] = number_format($billing->discount_amount, 2);
                    $this->penalty[$member->member_id] = number_format($billing->penalty, 2);
                    $this->totalAmountDues[$billing->billing_id] = ($previous_balance + $billing->billing_amount + $billing->penalty + $this->powasSettings->members_micro_savings) - $billing->discount_amount;

                    $this->toPrintBilling[] = $billing->billing_id;
                    $this->savedCount++;

                    $this->discountType[$member->member_id] = 'amount';
                }
            }
        }

        $billingSummary['total_billings'] = $totalBillings;
        $billingSummary['total_cubic_meter_used'] = $totalCubicMeterUsed;
        $billingSummary['total_billing_amount'] = $totalBillingAmount;
        $billingSummary['total_non_minimum_billing'] = $totalNotMinimumBilling;
        $billingSummary['total_non_minimum_count'] = $totalNotMinimumCount;
        $billingSummary['total_minimum_billing'] = $totalMinimumBilling;
        $billingSummary['total_minimum_count'] = $totalMinimumCount;
        $billingSummary['total_balances_from_previous'] = $totalBalanceFromPrevious;
        $billingSummary['total_discounts'] = $totalDiscounts;
        $billingSummary['total_penalties'] = $totalPenalties;
        $billingSummary['total_monthly_dues'] = $totalMonthlyDue + $totalUncollectedMicroSavings;

        return view('livewire.billings.collection-sheet', [
            'validReadingsList' => $this->validReadings,
            'billingSummary' => $billingSummary,
        ]);
    }
}
