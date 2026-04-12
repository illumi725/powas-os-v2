<?php

namespace App\Livewire\Powas;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\Billings;
use App\Models\ChartOfAccounts;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Readings;
use App\Models\Transactions;
use App\Models\MeterChange;
use App\Helpers\MeterHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GeneratedBilling extends Component
{
    public $powasID;
    public $powas;
    public $search;
    public $membersList;
    public $powasSettings;
    public $isMinimum = false;
    public $discountType = [];
    public $discount = [];
    public $penalty = [];
    public $discountLabel = [];
    public $toPrintBilling = [];
    public $regen;
    public $selectedToPrint = [];
    public $savedCount = 0;

    // Global Discount Properties
    public $showingGlobalDiscountModal = false;
    public $globalDiscountType = 'amount'; // 'amount' or 'percent'
    public $globalDiscountValue = 0;

    public $validReadings = [];

    protected $rules = [
        'discount.*' => 'required|numeric|min:0',
        'penalty.*' => 'required|numeric|min:0',
    ];

    protected $validationAttributes = [
        'discount.*' => 'discount',
        'penalty.*' => 'penalty',
    ];

    public function updatedSelectedToPrint($value)
    {
        $this->selectedToPrint = array_filter($this->selectedToPrint);
    }

    public function mount($powasID, $regen)
    {
        $this->powasID = $powasID;
        $this->regen = $regen;
    }

    public function saveAll()
    {
        foreach ($this->validReadings as $item => $value) {
            $this->saveBilling($item);
        }
    }

    public function saveBilling($memberID)
    {
        $this->validate([
            'discount.' . $memberID => 'required|numeric|min:0',
            'penalty.' . $memberID => 'required|numeric|min:0',
        ]);

        $billsReceivablesAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

        $revenuesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'REVENUE' . '%')->first();

        $selectedMember = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $memberID)->first();

        $isExists = Billings::where('member_id', $memberID)
            ->where('billing_month', Carbon::parse($this->validReadings[$memberID]['billing_month'])->format('Y-m-d'))->exists();

        if ($isExists == true) {
            $toUpdateBilling = Billings::where('member_id', $memberID)
                ->where('billing_month', Carbon::parse($this->validReadings[$memberID]['billing_month'])->format('Y-m-d'))->first();

            $oldValue = $toUpdateBilling->discount_amount;

            if ($oldValue != $this->discount[$memberID]) {
                $toUpdateBilling->discount_amount = $this->getDiscountValue($memberID);
                $toUpdateBilling->save();

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($this->discount[$memberID], 2) . '</i></b> in the column <i><u>' . 'discount_amount' . '</u></i> at <b>' . $toUpdateBilling->billing_id .  '</b> with POWAS ID <b>' . $toUpdateBilling->powas_id . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
            }

            $oldValue = $toUpdateBilling->penalty;

            if ($oldValue != $this->penalty[$memberID]) {
                $toUpdateBilling->penalty = $this->penalty[$memberID];
                $toUpdateBilling->save();

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($this->penalty[$memberID], 2) . '</i></b> in the column <i><u>' . 'penalty' . '</u></i> at <b>' . $toUpdateBilling->billing_id .  '</b> with POWAS ID <b>' . $toUpdateBilling->powas_id . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
            }
        } else {
            Billings::create([
                'billing_id' => $this->validReadings[$memberID]['billing_id'],
                'powas_id' => $this->powasID,
                'member_id' => $memberID,
                'recorded_by' => Auth::user()->user_id,
                'previous_reading_id' => $this->validReadings[$memberID]['previous_reading_id'],
                'present_reading_id' => $this->validReadings[$memberID]['present_reading_id'],
                'cubic_meter_used' => $this->validReadings[$memberID]['cubic_meter_used'],
                'billing_amount' => $this->validReadings[$memberID]['billing_amount'],
                'discount_amount' => $this->getDiscountValue($memberID),
                'penalty' => $this->penalty[$memberID],
                'billing_month' => Carbon::parse($this->validReadings[$memberID]['billing_month'])->format('Y-m-d'),
                'due_date' => $this->validReadings[$memberID]['due_date'],
                'cut_off_start' => $this->validReadings[$memberID]['cut_off_start'],
                'cut_off_end' => $this->validReadings[$memberID]['cut_off_end'],
                'bill_number' => $this->validReadings[$memberID]['bill_number'],
            ]);
            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created billing record for <b><i>' . strtoupper($this->validReadings[$memberID]['member_name']) . '</i></b> for the month of <b>' . $this->validReadings[$memberID]['billing_month'] . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

            $journalEntryNumber = CustomNumberFactory::journalEntryNumber($this->powasID, $this->validReadings[$memberID]['cut_off_end']);

            // For Bills Receivables
            $description = 'Bills Receivable generated for ' . strtoupper($this->validReadings[$memberID]['member_name']);

            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $billsReceivablesAccount->account_number,
                'description' => $description,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->validReadings[$memberID]['billing_amount'],
                'transaction_side' => $billsReceivablesAccount->normal_balance,
                'received_from' => strtoupper($this->validReadings[$memberID]['member_name']),
                'paid_to' => $this->validReadings[$memberID]['billing_id'],
                'member_id' => $memberID,
                'powas_id' => $this->powasID,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->validReadings[$memberID]['cut_off_end'],
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($billsReceivablesAccount->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($this->validReadings[$memberID]['billing_amount'], 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

            // For Revenues
            $description = 'Revenue from ' . strtoupper($this->validReadings[$memberID]['member_name']);

            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $revenuesAccount->account_number,
                'description' => $description,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->validReadings[$memberID]['billing_amount'],
                'transaction_side' => $revenuesAccount->normal_balance,
                'received_from' => strtoupper($this->validReadings[$memberID]['member_name']),
                'paid_to' => $this->validReadings[$memberID]['billing_id'],
                'member_id' => $memberID,
                'powas_id' => $this->powasID,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->validReadings[$memberID]['cut_off_end'],
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($revenuesAccount->account_name) . '</i></b> with description <b>"' . $description . ' ' . $selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->validReadings[$memberID]['billing_amount'], 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
        }

        $this->dispatch('saved_' . $memberID);
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

        if ($this->search == '' || $this->search == null) {
            $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_applications.powas_id', $this->powasID)
                ->where('powas_members.member_status', 'ACTIVE')
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->get();
        } else {
            $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('powas_applications.powas_id', $this->powasID)
                ->where('powas_members.member_status', 'ACTIVE')
                ->where(function ($query) {
                    $query->where('powas_applications.lastname', 'like', '%' . strtoupper($this->search) . '%')
                        ->orWhere('powas_applications.firstname', 'like', '%' . strtoupper($this->search) . '%');
                })
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->get();
        }

        $this->reset([
            'validReadings',
        ]);

        $this->reset([
            'toPrintBilling',
        ]);

        foreach ($this->membersList as $member) {
            $readingCount = Readings::where('member_id', $member->member_id)->count();
            if ($readingCount > 1) {
                $presentReadingQuery = Readings::where('member_id', $member->member_id)
                    ->orderBy('reading_date', 'desc')
                    ->first();
                $previousReadingQuery = Readings::where('member_id', $member->member_id)
                    ->orderBy('reading_date', 'desc')
                    ->offset(1)->first();

                $isExists = Billings::where('member_id', $member->member_id)
                    ->where('billing_month', Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->format('Y-m-01'))->exists();

                if ($isExists == true) {
                    $exists = 'YES';

                    $billsReceivablesAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

                    $billing = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
                        ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                        ->where('billings.member_id', $member->member_id)
                        ->where('billings.billing_month', Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->format('Y-m-01'))->first();

                    $isTransacted = Transactions::where('transaction_side', 'CREDIT')
                        ->where('paid_to', $billing->billing_id)
                        ->where('account_number', $billsReceivablesAccount->account_number)
                        ->exists();

                    $transacted = 'NO';
                    if ($isTransacted == true) {
                        $transacted = 'YES';
                    }

                    if ($this->regen == 'true') {
                        $presR = Readings::where('reading_id', $billing->present_reading_id)->first();
                        $prevR = Readings::where('reading_id', $billing->previous_reading_id)->first();
                        $new_cm_used = MeterHelper::calculateCubicMeterUsed($member->member_id, $prevR, $presR);
                        $new_billing_amount = $new_cm_used * $this->powasSettings->water_rate;

                        $due_date_day = $this->powasSettings->due_date_day;
                        if ($this->powasSettings->due_date_day < 10) {
                            $due_date_day = '0' . $this->powasSettings->due_date_day;
                        }

                        $new_due_date = Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->addMonth()->format('Y-m-' . $due_date_day);
                        $new_due_date = Carbon::parse($new_due_date)->format('Y-m-d');

                        $revenuesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'REVENUE' . '%')->first();

                        // $selectedMember = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                        //     ->where('powas_members.member_id', $member->member_id)->first();

                        if ($new_cm_used <= 5) {
                            $new_billing_amount = $this->powasSettings->minimum_payment;
                            $this->isMinimum = true;
                        } else {
                            $new_billing_amount = $new_cm_used * $this->powasSettings->water_rate;
                            $this->isMinimum = false;
                        }

                        if ($billing->cubic_meter_used != $new_cm_used) {
                            $oldValue = $billing->cubic_meter_used;
                            $billing->cubic_meter_used = $new_cm_used;
                            $billing->save();

                            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_cm_used, 2) . '</i></b> in the column <i><u>' . 'cubic_meter_used' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
                        }

                        if ($billing->billing_amount != $new_billing_amount) {
                            $oldValue = $billing->billing_amount;
                            $billing->billing_amount = $new_billing_amount;
                            $billing->save();

                            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_billing_amount, 2) . '</i></b> in the column <i><u>' . 'billing_amount' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

                            // For Bills Receivables
                            $billsReceveibleTransaction = Transactions::where('paid_to', $billing->billing_id)
                                ->where('account_number', $billsReceivablesAccount->account_number)->first();

                            $billsReceveibleTransaction->amount = $new_billing_amount;
                            $billsReceveibleTransaction->save();

                            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated bills receivable amount from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_billing_amount, 2) . '</i></b> in <i><u>' . 'TRANSACTIONS' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);

                            // For Revenues
                            $revenuesTransaction = Transactions::where('paid_to', $billing->billing_id)
                                ->where('account_number', $revenuesAccount->account_number)->first();

                            $revenuesTransaction->amount = $new_billing_amount;
                            $revenuesTransaction->save();

                            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated revenue amount from <b><i>' . $oldValue . '</i></b> to <b><i>' . number_format($new_billing_amount, 2) . '</i></b> in <i><u>' . 'TRANSACTIONS' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
                        }

                        if ($billing->due_date != $new_due_date) {
                            $oldValue = $billing->due_date;
                            $billing->due_date = Carbon::parse($new_due_date)->format('Y-m-d');
                            $billing->save();

                            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated from <b><i>' . $oldValue . '</i></b> to <b><i>' . $new_due_date . '</i></b> in the column <i><u>' . 'due_date' . '</u></i> at <b>' . $billing->billing_id .  '</b> with POWAS ID <b>' . $billing->powas_id . '</b>.';

                            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billing', $this->powasID);
                        }
                    }

                    if ($billing->cubic_meter_used <= 5) {
                        $billing_amount = $this->powasSettings->minimum_payment;
                        $this->isMinimum = true;
                    } else {
                        $billing_amount = $billing->cubic_meter_used * $this->powasSettings->water_rate;
                        $this->isMinimum = false;
                    }

                    $this->validReadings[$member->member_id] = [
                        'billing_id' => $billing->billing_id,
                        'member_name' => $member->lastname . ', ' . $member->firstname,
                        'present_reading' => Readings::where('reading_id', $billing->present_reading_id)->first()->reading,
                        'previous_reading' => Readings::where('reading_id', $billing->previous_reading_id)->first()->reading,
                        'present_reading_id' => $billing->present_reading_id,
                        'previous_reading_id' => $billing->present_reading_id,
                        'cubic_meter_used' => $billing->cubic_meter_used,
                        'billing_amount' => $billing->billing_amount,
                        'billing_month' => Carbon::parse($billing->billing_month)->format('F Y'),
                        'due_date' => Carbon::parse($billing->due_date)->format('Y-m-d'),
                        'cut_off_start' => Carbon::parse($billing->cut_off_start)->format('Y-m-d'),
                        'cut_off_end' => Carbon::parse($billing->cut_off_start)->format('Y-m-d'),
                        'bill_number' => $billing->bill_number,
                        'print_count' => $billing->print_count,
                        'is_minimum' => $this->isMinimum,
                        'is_exists' => $exists,
                        'isTransacted' => $transacted,
                    ];

                    if (!isset($this->discount[$member->member_id])) {
                        $this->discount[$member->member_id] = number_format($billing->discount_amount, 2, '.', '');
                    }
                    if (!isset($this->penalty[$member->member_id])) {
                        $this->penalty[$member->member_id] = number_format($billing->penalty, 2, '.', '');
                    }

                    if (!isset($this->discountType[$member->member_id])) {
                        $this->discountType[$member->member_id] = 'amount';
                    }

                    $this->toPrintBilling[] = $billing->billing_id;
                    $this->savedCount++;
                } else {
                    $exists = 'NO';

                    $billingID = CustomNumberFactory::getRandomID();

                    $cubic_meter_used = MeterHelper::calculateCubicMeterUsed($member->member_id, $previousReadingQuery, $presentReadingQuery);

                    if ($cubic_meter_used <= 5) {
                        $billing_amount = $this->powasSettings->minimum_payment;
                        $this->isMinimum = true;
                    } else {
                        $billing_amount = $cubic_meter_used * $this->powasSettings->water_rate;
                        $this->isMinimum = false;
                    }

                    $due_date_day = $this->powasSettings->due_date_day;
                    if ($this->powasSettings->due_date_day < 10) {
                        $due_date_day = '0' . $this->powasSettings->due_date_day;
                    }

                    $dueDate = Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->addMonth()->format('Y-m-' . $due_date_day);

                    $this->validReadings[$member->member_id] = [
                        'billing_id' => $billingID,
                        'member_name' => $member->lastname . ', ' . $member->firstname,
                        'present_reading' => $presentReadingQuery->reading,
                        'previous_reading' => $previousReadingQuery->reading,
                        'present_reading_id' => $presentReadingQuery->reading_id,
                        'previous_reading_id' => $previousReadingQuery->reading_id,
                        'cubic_meter_used' => $cubic_meter_used,
                        'billing_amount' => $billing_amount,
                        'billing_month' => Carbon::parse($presentReadingQuery->reading_date)->subDays(15)->format('F Y'),
                        'due_date' => Carbon::parse($dueDate)->format('Y-m-d'),
                        'cut_off_start' => Carbon::parse($previousReadingQuery->reading_date)->format('Y-m-d'),
                        'cut_off_end' => Carbon::parse($presentReadingQuery->reading_date)->format('Y-m-d'),
                        'bill_number' => $presentReadingQuery->reading_count,
                        'print_count' => 0,
                        'is_minimum' => $this->isMinimum,
                        'is_exists' => $exists,
                        'isTransacted' => 'NO',
                    ];

                    if (!isset($this->discount[$member->member_id])) {
                        $this->discount[$member->member_id] = number_format(0, 2);
                    }
                    if (!isset($this->penalty[$member->member_id])) {
                        $this->penalty[$member->member_id] = number_format(0, 2);
                    }

                    if (!isset($this->discountType[$member->member_id])) {
                        if ($member->land_owner == 'Y') {
                            $this->discountType[$member->member_id] = 'percent';
                            $this->discount[$member->member_id] = 100;
                        } else {
                            $this->discountType[$member->member_id] = 'amount';
                        }
                    }

                    $this->toPrintBilling[] = $billingID;
                }
            }
        }

        return view('livewire.powas.generated-billing', [
            'validReadingsList' => $this->validReadings,
        ]);
    }

    public function openGlobalDiscountModal()
    {
        $this->showingGlobalDiscountModal = true;
        $this->globalDiscountType = 'amount';
        $this->globalDiscountValue = 0;
    }

    public function applyGlobalDiscount()
    {
        $this->validate([
            'globalDiscountValue' => 'required|numeric|min:0',
        ]);

        foreach ($this->validReadings as $memberID => $data) {
            // Skip landowners if needed, but per plan applying to all.
            // Users can manually change back landowners if they wish.
            
            if ($this->globalDiscountType == 'percent') {
                $val = $this->globalDiscountValue;
                if ($val > 100) $val = 100;
                $this->discountType[$memberID] = 'percent';
                $this->discount[$memberID] = $val;
            } else {
                $this->discountType[$memberID] = 'amount';
                $this->discount[$memberID] = $this->globalDiscountValue;
            }
        }

        $this->showingGlobalDiscountModal = false;

        $this->dispatch('alert', [
            'message' => 'Global discount applied to all pending bills!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
    }
}
