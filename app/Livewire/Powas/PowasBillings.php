<?php

namespace App\Livewire\Powas;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Livewire\Transactions\TransactionsList;
use App\Models\Billings;
use App\Models\BillsPayments;
use App\Models\ChartOfAccounts;
use App\Models\MicroSavings;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Readings;
use App\Models\ReconnectionFees;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PowasBillings extends Component
{
    use WithPagination;

    public $pagination = 10;
    public $powasID;
    public $powas;
    public $showingBillingMonthSelector = false;
    public $membersList;
    public $powasSettings;
    public $billingMonth;
    public $isMinimum = false;
    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $powasSettingsChanges = [];
    public $billingMonths;
    public $billingMonthSelections;
    public $showingAddPaymentModal = false;
    public $showingConfirmSaveModal = false;
    public $showingConfirmPrintModal = false;
    public $printAllReceipts = false;
    public $selectedBill;
    public $selectedMember;
    public $paymentDate;
    public $afterDuePenalty = 0;
    public $reconnectionFee = 0;
    public $billCutOffEnd = '';
    public $filterBillingMonth = 'All';

    public $existingBillingCount = 0;
    public $notExistingBillingCount = 0;
    public $daysPassedAfterDueDate = 0;
    public $withReconnectionFee = false;
    public $amountToPay = 0;
    public $paymentAmount = 0;
    public $excessPaymentFromDB = 0;
    public $withExcessPayments = false;

    public $validReadings = [];

    public $isReconnectionFeeExists = false;
    public $isExcessPaymentsExists = false;
    public $isMicroSavingsExists = false;
    public $isReceivablesExists = false;
    public $isPenaltiesExists = false;
    public $saveError = [];

    public $toPrintReceipts = [];

    public $selectedBillings = [];
    public $selectAll = false;
    public $bulkPaymentDate;
    public $showingBulkPaymentModal = false;
    public $bulkSummary = [
        'count' => 0,
        'bill_amount' => 0,
        'penalty' => 0,
        'reconnection_fee' => 0,
        'micro_savings' => 0,
        'discount' => 0,
        'excess' => 0,
        'total_pay' => 0,
    ];

    // Discount Feature Properties
    public $showingDiscountModal = false;
    public $discountBillId;
    public $discountSettingType = 'amount'; // 'amount' or 'percentage'
    public $discountSettingValue = 0;
    public $discountCalculatedAmount = 0;
    public $discountTargetBill;

    protected $pageName = 'billings';
    protected $listeners = ['reloadBillings' => '$refresh'];

    protected $rules = [
        'startDate' => 'required|date|before_or_equal:endDate',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    // ... (existing messages) ...

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
                ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                ->where('billings.powas_id', $this->powasID);

            if ($this->filterBillingMonth != 'All') {
                $query->where('billings.billing_month', $this->filterBillingMonth);
            }

            if ($this->search) {
                $searchTerm = '%' . strtoupper($this->search) . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('powas_applications.lastname', 'like', $searchTerm)
                        ->orWhere('powas_applications.firstname', 'like', $searchTerm)
                        ->orWhere('powas_applications.middlename', 'like', $searchTerm)
                        ->orWhere('billings.billing_id', 'like', $searchTerm)
                        ->orWhere('billings.bill_status', 'like', $searchTerm)
                        ->orWhere('powas_members.member_id', 'like', $searchTerm);
                });
            }

            $powasBillings = $query->orderBy('billings.billing_month', 'desc')
                ->orderBy('billings.bill_status', 'desc')
                ->orderBy('powas_applications.lastname', 'asc')
                ->orderBy('powas_applications.firstname', 'asc')
                ->orderBy('powas_applications.middlename', 'asc')
                ->paginate($this->pagination, ['*'], 'billings');

            $this->selectedBillings = collect($powasBillings->items())
                ->where('bill_status', '!=', 'PAID') // Only select UNPAID rows
                ->pluck('billing_id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedBillings = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage('billings');
        $this->selectAll = false;
        $this->selectedBillings = [];
    }

    public function updatedFilterBillingMonth()
    {
        $this->resetPage('billings');
        $this->selectAll = false;
        $this->selectedBillings = [];
    }

    public function updatedPagination()
    {
        $this->resetPage('billings');
        $this->selectAll = false;
        $this->selectedBillings = [];
    }

    public function showBulkPaymentModal()
    {
        if (count($this->selectedBillings) == 0) {
            $this->dispatch('alert', [
                'message' => 'Please select at least one bill to pay.',
                'messageType' => 'warning',
                'position' => 'top-right',
            ]);
            return;
        }

        $this->bulkPaymentDate = Carbon::parse(now())->format('Y-m-d');
        $this->calculateBulkEstimates();
        $this->showingBulkPaymentModal = true;
    }

    public function updatedBulkPaymentDate()
    {
        $this->calculateBulkEstimates();
    }

    public function calculateBulkEstimates()
    {
        $this->bulkSummary = [
            'count' => 0,
            'bill_amount' => 0,
            'penalty' => 0,
            'reconnection_fee' => 0,
            'micro_savings' => 0,
            'discount' => 0,
            'excess' => 0,
            'total_pay' => 0,
        ];

        if (empty($this->selectedBillings)) return;

        $bills = Billings::whereIn('billing_id', $this->selectedBillings)->where('bill_status', '!=', 'PAID')->get();

        foreach ($bills as $bill) {
            // 1. Calculate Fees
            $daysPassed = Carbon::parse($bill->due_date)->diffInDays(Carbon::parse($this->bulkPaymentDate), false);

            $calculatedPenalty = 0;
            if ($this->powasSettings->penalty_per_day > 0) {
                if ($daysPassed > 0 && $daysPassed < $this->powasSettings->days_before_disconnection) {
                    $calculatedPenalty = $this->powasSettings->penalty_per_day * $daysPassed;
                } elseif ($daysPassed >= $this->powasSettings->days_before_disconnection) {
                    $calculatedPenalty = ($this->powasSettings->days_before_disconnection - 1) * $this->powasSettings->penalty_per_day;
                }
            }

            $calculatedReconnectionFee = 0;
            if ($this->powasSettings->reconnection_fee > 0) {
                if ($daysPassed >= $this->powasSettings->days_before_disconnection) {
                    $calculatedReconnectionFee = $this->powasSettings->reconnection_fee;
                }
            }

            $microSavingsAmt = 0;
            if ($this->powasSettings->members_micro_savings > 0) {
                $microSavingsAmt = $this->powasSettings->members_micro_savings;
            }

            // Check Excess Payments
            $excessAmt = 0;
            $previousBill = Billings::where('member_id', $bill->member_id)
                ->where('billing_month', Carbon::parse($bill->billing_month)->subMonth()->format('Y-m-01'))
                ->first();

            if ($previousBill) {
                $excessTrans = Transactions::where('paid_to', $previousBill->billing_id)
                    ->where('account_number', '208') // Excess Payments
                    ->where('transaction_side', 'CREDIT')
                    ->first();
                if ($excessTrans) {
                    $excessAmt = $excessTrans->amount;
                }
            }

            $this->bulkSummary['bill_amount'] += $bill->billing_amount;
            $this->bulkSummary['penalty'] += ($calculatedPenalty + $bill->penalty);
            $this->bulkSummary['reconnection_fee'] += $calculatedReconnectionFee;
            $this->bulkSummary['micro_savings'] += $microSavingsAmt;
            $this->bulkSummary['discount'] += $bill->discount_amount;
            $this->bulkSummary['excess'] += $excessAmt;
        }

        $this->bulkSummary['count'] = $bills->count();
        $this->bulkSummary['total_pay'] =
            ($this->bulkSummary['bill_amount'] + $this->bulkSummary['penalty'] + $this->bulkSummary['micro_savings'] + $this->bulkSummary['reconnection_fee'])
            - ($this->bulkSummary['discount'] + $this->bulkSummary['excess']);
    }

    public function saveBulkPayments()
    {
        $this->validate([
            'bulkPaymentDate' => ['required', 'before_or_equal:today'],
        ], [
            'bulkPaymentDate.before_or_equal' => 'Payment date must be equal or before ' . Carbon::parse(now())->format('m/d/y') . '.',
        ]);

        $successCount = 0;
        $errorCount = 0;

        // Verify Accounts Existence ONCE to save queries
        $cashOnHandAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'CASH' . '%')->first();
        $reconnectionFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'RECONNECTION FEE' . '%')->first();
        $penaltiesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'PENALTIES' . '%')->first();
        $microSavingsAccount = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'MICRO-SAVINGS' . '%')->first();
        $discountsAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'DISCOUNT' . '%')->first();
        $excessPaymentAccount = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'EXCESS PAYMENTS' . '%')->first();
        $billsReceivableAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

        if (!$cashOnHandAccount || !$billsReceivableAccount) {
             $this->dispatch('alert', [
                'message' => 'Critical: Missing Chart of Accounts (Cash or Receivables). Cannot proceed.',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
            return;
        }

        foreach ($this->selectedBillings as $billId) {
            DB::beginTransaction();
            try {
                $bill = Billings::find($billId);
                
                // Skip already paid
                if ($bill->bill_status == 'PAID') {
                    continue;
                }

                $member = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                    ->where('powas_members.member_id', $bill->member_id)
                    ->first();

                // 1. Calculate Fees (Replicates showAddPaymentModal logic)
                $daysPassed = Carbon::parse($bill->due_date)->diffInDays(Carbon::parse($this->bulkPaymentDate), false);
                
                $calculatedPenalty = 0;
                if ($this->powasSettings->penalty_per_day > 0) {
                     if ($daysPassed > 0 && $daysPassed < $this->powasSettings->days_before_disconnection) {
                        $calculatedPenalty = $this->powasSettings->penalty_per_day * $daysPassed;
                     } elseif ($daysPassed >= $this->powasSettings->days_before_disconnection) {
                        $calculatedPenalty = ($this->powasSettings->days_before_disconnection - 1) * $this->powasSettings->penalty_per_day;
                     }
                }

                $calculatedReconnectionFee = 0;
                $hasReconnectionFee = false;
                if ($this->powasSettings->reconnection_fee > 0) {
                    if ($daysPassed >= $this->powasSettings->days_before_disconnection) {
                        $calculatedReconnectionFee = $this->powasSettings->reconnection_fee;
                        $hasReconnectionFee = true;
                    }
                }

                $microSavingsAmt = 0;
                if ($this->powasSettings->members_micro_savings > 0) {
                    $microSavingsAmt = $this->powasSettings->members_micro_savings;
                }
                
                // Check Excess Payments
                $excessAmt = 0;
                $previousBill = Billings::where('member_id', $bill->member_id)
                    ->where('billing_month', Carbon::parse($bill->billing_month)->subMonth()->format('Y-m-01'))
                    ->first();
                
                if ($previousBill) {
                     $excessTrans = Transactions::where('paid_to', $previousBill->billing_id)
                        ->where('account_number', '208') // Excess Payments
                        ->where('transaction_side', 'CREDIT')
                        ->first();
                     if ($excessTrans) {
                         $excessAmt = $excessTrans->amount;
                     }
                }

                // Total Cash Calculation
                // Bill Amount + Penalty + MicroSavings + Reconnection - Discount - Excess
                $totalCashRequired = ($bill->billing_amount + $bill->penalty + $microSavingsAmt - $bill->discount_amount - $excessAmt) + ($calculatedPenalty + $calculatedReconnectionFee);

                // 2. Process Readings Check
                $present_reading = Readings::find($bill->present_reading_id);
                $previous_reading = Readings::find($bill->previous_reading_id);

                if (!$present_reading || !$previous_reading) {
                    throw new \Exception("Readings missing for bill $billId");
                }

                // 3. Generate Journal Entry
                $journalEntryNumber = CustomNumberFactory::journalEntryNumber($this->powasID, $this->bulkPaymentDate);

                // 4. Create Transactions
                
                // Reconnection Fee
                if ($hasReconnectionFee && $reconnectionFeeAccount) {
                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $reconnectionFeeAccount->account_number,
                        'description' => 'Reconnection fee received from ' . $member->lastname . ', ' . $member->firstname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $calculatedReconnectionFee,
                        'transaction_side' => $reconnectionFeeAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                    // Cash for Reconnection
                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $cashOnHandAccount->account_number,
                        'description' => 'Cash (Reconnection) from ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $calculatedReconnectionFee,
                        'transaction_side' => $cashOnHandAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                }

                // Penalty
                if ($calculatedPenalty > 0 && $penaltiesAccount) {
                    // Update Bill
                    $bill->penalty += $calculatedPenalty;
                    $bill->save();

                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $penaltiesAccount->account_number,
                        'description' => 'Penalty from ' . $member->lastname . ', ' . $member->firstname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $calculatedPenalty,
                        'transaction_side' => $penaltiesAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                     // Cash for Penalty
                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $cashOnHandAccount->account_number,
                        'description' => 'Cash (Penalty) from ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $calculatedPenalty,
                        'transaction_side' => $cashOnHandAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                } else {
                    // Old Penalty Payment if exists
                    if ($bill->penalty > 0 && $penaltiesAccount) {
                        Transactions::create([
                            'trxn_id' => CustomNumberFactory::getRandomID(),
                            'account_number' => $penaltiesAccount->account_number,
                            'description' => 'Penalty from ' . $member->lastname,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $bill->penalty, // Paying the existing penalty
                            'transaction_side' => $penaltiesAccount->normal_balance,
                             'received_from' => $member->lastname . ', ' . $member->firstname,
                            'paid_to' => $bill->billing_id,
                            'member_id' => $bill->member_id,
                            'powas_id' => $bill->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $this->bulkPaymentDate,
                        ]);
                        Transactions::create([
                            'trxn_id' => CustomNumberFactory::getRandomID(),
                            'account_number' => $cashOnHandAccount->account_number,
                            'description' => 'Cash (Penalty) from ' . $member->lastname,
                            'journal_entry_number' => $journalEntryNumber,
                            'amount' => $bill->penalty,
                            'transaction_side' => $cashOnHandAccount->normal_balance,
                            'received_from' => $member->lastname . ', ' . $member->firstname,
                            'paid_to' => $bill->billing_id,
                            'member_id' => $bill->member_id,
                            'powas_id' => $bill->powas_id,
                            'recorded_by_id' => Auth::user()->user_id,
                            'transaction_date' => $this->bulkPaymentDate,
                        ]);
                    }
                }

                // Micro Savings
                if ($microSavingsAmt > 0 && $microSavingsAccount) {
                    $msBalance = 0;
                    $latestMS = MicroSavings::where('member_id', $member->member_id)->orderByDesc('date_recorded')->first();
                    if ($latestMS) {
                        $msBalance = $latestMS->balance;
                    }
                    MicroSavings::create([
                        'savings_id' => CustomNumberFactory::getRandomID(),
                        'powas_id' => $this->powasID,
                        'member_id' => $member->member_id,
                        'recorded_by' => Auth::user()->user_id,
                        'billing_id' => $bill->billing_id,
                        'deposit' => $microSavingsAmt,
                        'balance' => $msBalance + $microSavingsAmt,
                        'date_recorded' => $this->bulkPaymentDate,
                    ]);
                    
                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $microSavingsAccount->account_number,
                        'description' => 'Micro-savings from ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $microSavingsAmt,
                        'transaction_side' => $microSavingsAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $cashOnHandAccount->account_number,
                        'description' => 'Cash (MS) from ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $microSavingsAmt,
                        'transaction_side' => $cashOnHandAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                }

                // Discount
                if ($bill->discount_amount > 0 && $discountsAccount) {
                     Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $discountsAccount->account_number,
                        'description' => 'Discount for ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $bill->discount_amount,
                        'transaction_side' => $discountsAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                }

                // Excess Payments (Deduction)
                if ($excessAmt > 0 && $excessPaymentAccount) {
                    Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $excessPaymentAccount->account_number,
                        'description' => 'Excess Payments consumed by ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $excessAmt,
                        'transaction_side' => 'DEBIT',
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                }

                // Bills Receivable
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $billsReceivableAccount->account_number,
                    'description' => 'Bill payment from ' . $member->lastname,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $bill->billing_amount,
                    'transaction_side' => 'CREDIT',
                    'received_from' => $member->lastname . ', ' . $member->firstname,
                    'paid_to' => $bill->billing_id,
                    'member_id' => $bill->member_id,
                    'powas_id' => $bill->powas_id,
                    'recorded_by_id' => Auth::user()->user_id,
                    'transaction_date' => $this->bulkPaymentDate,
                ]);

                // Cash for Bill
                // Cash = (BillAmount + Penalty + MS + Reconnect) - Discount - Excess
                // But specifically for the "Bill Portion" (Bill Amount - Discount - Excess)
                // Actually, typically the "Cash" entries are best grouped or done per line item.
                // In `savePayment`, there is a final specific cash entry for "Bills Receivables".
                // That entry amount is `paymentAmount - microSavings - reconnection - newPenalty`.
                // Here, `paymentAmount` (Total Cash) = `totalCashRequired`.
                // So Cash For Bill = `totalCashRequired - microSavingsAmt - calculatedReconnectionFee - calculatedPenalty`.
                // Which simplifies to: `bill->billing_amount - bill->discount_amount - excessAmt`. 
                // Wait, if `excessAmt` > `bill->billing_amount`, we have a problem. But typically excess is small.
                // Let's assume exact payment of the due amount.
                
                $cashForBill = $bill->billing_amount - $bill->discount_amount - $excessAmt;

                if ($cashForBill > 0) {
                     Transactions::create([
                        'trxn_id' => CustomNumberFactory::getRandomID(),
                        'account_number' => $cashOnHandAccount->account_number,
                        'description' => 'Cash (Bill) from ' . $member->lastname,
                        'journal_entry_number' => $journalEntryNumber,
                        'amount' => $cashForBill,
                        'transaction_side' => $cashOnHandAccount->normal_balance,
                        'received_from' => $member->lastname . ', ' . $member->firstname,
                        'paid_to' => $bill->billing_id,
                        'member_id' => $bill->member_id,
                        'powas_id' => $bill->powas_id,
                        'recorded_by_id' => Auth::user()->user_id,
                        'transaction_date' => $this->bulkPaymentDate,
                    ]);
                }
                
                // Update Bill Status
                $bill->bill_status = 'PAID';
                $bill->save();
                
                $this->toPrintReceipts[] = $bill->billing_id;
                
                // Log
                ActionLogger::dispatch('update', 'Bulk Payment: Marked bill ' . $bill->billing_id . ' as PAID', Auth::user()->user_id, 'billings', $this->powasID);

                DB::commit();
                $successCount++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                // Continue to next bill even if one fails
            }
        }

        $this->dispatch('alert', [
            'message' => "Bulk Payment Complete. Success: $successCount, Failed: $errorCount",
            'messageType' => 'info',
            'position' => 'top-right',
        ]);
        
        $this->showingBulkPaymentModal = false;
        $this->selectedBillings = []; // Reset selection via checkbox?? Livewire might keep it? 
        // Better to keep selection so user sees what happened, or clear it. Usage usually implies clearing.
        $this->selectedBillings = []; 
        
        $this->dispatch('transaction-added')->to(TransactionsList::class);
        
        if (count($this->toPrintReceipts) > 0) {
            $this->showingConfirmPrintModal = true;
        }
    }

    protected $messages = [
        'startDate.before_or_equal' => 'Start Date must be before or the same as the End Date!',
        'endDate.after_or_equal' => 'Start Date must be before or the same as the End Date!',
    ];

    public function showBillingMonthSelector()
    {
        $this->billingMonthSelections = Billings::select(DB::raw('DISTINCT(billing_month)'))
            ->where('powas_id', $this->powasID)
            ->orderBy('billing_month', 'desc')
            ->limit(24)
            ->get();

        $this->showingBillingMonthSelector = true;
    }

    public function showAddPaymentModal($selectedBillID)
    {
        $this->reset([
            'selectedBill',
            'selectedMember',
            'afterDuePenalty',
            'reconnectionFee',
            'withReconnectionFee',
            'withExcessPayments',
            'amountToPay',
            'excessPaymentFromDB',
            'saveError',
            'isReconnectionFeeExists',
            'isMicroSavingsExists',
            'isReceivablesExists',
            'isPenaltiesExists',
            'isExcessPaymentsExists',
        ]);

        $this->selectedBill = Billings::find($selectedBillID);
        $this->selectedMember = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')->where('powas_members.member_id', $this->selectedBill->member_id)->first();

        $this->isReconnectionFeeExists = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'RECONNECTION FEE' . '%')->exists();
        $this->isMicroSavingsExists = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'MICRO-SAVINGS' . '%')->exists();
        $this->isReceivablesExists = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->exists();
        $this->isPenaltiesExists = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'PENALTIES' . '%')->exists();
        $this->isExcessPaymentsExists = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'EXCESS PAYMENTS' . '%')->exists();

        if ($this->isReconnectionFeeExists == false) {
            $this->saveError[] = 'reconnection fee';
        }

        if ($this->isMicroSavingsExists == false) {
            $this->saveError[] = 'member\'s micro-savings';
        }

        if ($this->isReceivablesExists == false) {
            $this->saveError[] = 'bills receivables';
        }

        if ($this->isPenaltiesExists == false) {
            $this->saveError[] = 'penalties';
        }

        if ($this->isExcessPaymentsExists == false) {
            $this->saveError[] = 'excess payment';
        }

        $this->paymentDate = Carbon::parse(now())->format('Y-m-d');

        $this->billCutOffEnd = $this->selectedBill->cut_off_end;

        $this->daysPassedAfterDueDate = Carbon::parse($this->selectedBill->due_date)->diffInDays(Carbon::parse($this->paymentDate), false);

        if ($this->powasSettings->penalty_per_day > 0 || $this->powasSettings->penalty_per_day != null) {
            if ($this->powasSettings->days_before_disconnection > 0 || $this->powasSettings->days_before_disconnection != null) {
                if ($this->daysPassedAfterDueDate < $this->powasSettings->days_before_disconnection && $this->daysPassedAfterDueDate > 0) {
                    $this->afterDuePenalty = number_format($this->powasSettings->penalty_per_day * $this->daysPassedAfterDueDate, 2);
                } elseif ($this->daysPassedAfterDueDate >= $this->powasSettings->days_before_disconnection) {
                    $this->afterDuePenalty = number_format(($this->powasSettings->days_before_disconnection - 1) * $this->powasSettings->penalty_per_day, 2);
                } else {
                    $this->afterDuePenalty = number_format(0, 2);
                }
            } else {
                $this->afterDuePenalty = number_format(0, 2);
            }
        } else {
            $this->afterDuePenalty = number_format(0, 2);
        }

        if ($this->powasSettings->reconnection_fee > 0 || $this->powasSettings->reconnection_fee != null) {
            if ($this->powasSettings->days_before_disconnection > 0 || $this->powasSettings->days_before_disconnection != null) {
                if ($this->daysPassedAfterDueDate >= $this->powasSettings->days_before_disconnection) {
                    $this->reconnectionFee = $this->powasSettings->reconnection_fee;
                    $this->withReconnectionFee = true;
                } else {
                    $this->reconnectionFee = number_format(0, 2);
                    $this->withReconnectionFee = false;
                }
            } else {
                $this->reconnectionFee = number_format(0, 2);
                $this->withReconnectionFee = false;
            }
        } else {
            $this->reconnectionFee = number_format(0, 2);
            $this->withReconnectionFee = false;
        }

        $microSavings = 0;

        if ($this->powasSettings->members_micro_savings > 0 && $this->powasSettings->members_micro_savings != null) {
            $microSavings = $this->powasSettings->members_micro_savings;
        }

        $isExistsPreviousBill = Billings::where('member_id', $this->selectedBill->member_id)
            ->where('billings.billing_month', Carbon::parse($this->selectedBill->billing_month)->subMonth()->format('Y-m-01'))->exists();

        if ($isExistsPreviousBill == true) {
            $previousBillID = Billings::where('member_id', $this->selectedBill->member_id)
                ->where('billings.billing_month', Carbon::parse($this->selectedBill->billing_month)->subMonth()->format('Y-m-01'))->first()->billing_id;

            $isExistsExcessPayment = Transactions::where('paid_to', $previousBillID)
                ->where('account_number', '208')
                ->where('transaction_side', 'CREDIT')
                ->exists();

            if ($isExistsExcessPayment == true) {
                $this->excessPaymentFromDB = Transactions::where('paid_to', $previousBillID)
                    ->where('account_number', '208')
                    ->where('transaction_side', 'CREDIT')
                    ->first()->amount;
                $this->withExcessPayments = true;
            }
        }

        $this->amountToPay = ($this->selectedBill->billing_amount + $this->selectedBill->penalty + $microSavings - $this->selectedBill->discount_amount - $this->excessPaymentFromDB) + ($this->afterDuePenalty + $this->reconnectionFee);

        $this->paymentAmount = number_format($this->amountToPay, 2, '.', '');

        $this->showingAddPaymentModal = true;
    }

    public function updatedPaymentDate()
    {
        $this->resetErrorBag('paymentDate');
        $this->validate([
            'paymentDate' => ['required', 'before_or_equal:today', 'after_or_equal:' . $this->billCutOffEnd],
        ], [
            'paymentDate.before_or_equal' => 'Payment date must be equal or before ' . Carbon::parse(now())->format('m/d/y') . '.',
            'paymentDate.after_or_equal' => 'Payment date must be equal or after ' . Carbon::parse($this->billCutOffEnd)->format('m/d/Y') . '.',
        ], [
            'paymentDate' => 'payment date',
        ]);

        $this->reset([
            'afterDuePenalty',
        ]);

        $this->daysPassedAfterDueDate = Carbon::parse($this->selectedBill->due_date)->diffInDays(Carbon::parse($this->paymentDate), false);

        if ($this->powasSettings->penalty_per_day > 0 || $this->powasSettings->penalty_per_day != null) {
            if ($this->powasSettings->days_before_disconnection > 0 || $this->powasSettings->days_before_disconnection != null) {
                if ($this->daysPassedAfterDueDate < $this->powasSettings->days_before_disconnection && $this->daysPassedAfterDueDate > 0) {
                    $this->afterDuePenalty = number_format($this->powasSettings->penalty_per_day * $this->daysPassedAfterDueDate, 2);
                } elseif ($this->daysPassedAfterDueDate >= $this->powasSettings->days_before_disconnection) {
                    $this->afterDuePenalty = number_format(($this->powasSettings->days_before_disconnection - 1) * $this->powasSettings->penalty_per_day, 2);
                } else {
                    $this->afterDuePenalty = 0;
                }
            } else {
                $this->afterDuePenalty = 0;
            }
        } else {
            $this->afterDuePenalty = 0;
        }

        if ($this->powasSettings->reconnection_fee > 0 || $this->powasSettings->reconnection_fee != null) {
            if ($this->powasSettings->days_before_disconnection > 0 || $this->powasSettings->days_before_disconnection != null) {
                if ($this->daysPassedAfterDueDate >= $this->powasSettings->days_before_disconnection) {
                    $this->reconnectionFee = $this->powasSettings->reconnection_fee;
                    $this->withReconnectionFee = true;
                } else {
                    $this->reconnectionFee = 0;
                    $this->withReconnectionFee = false;
                }
            } else {
                $this->reconnectionFee = 0;
                $this->withReconnectionFee = false;
            }
        } else {
            $this->reconnectionFee = 0;
            $this->withReconnectionFee = false;
        }

        $microSavings = 0;

        if ($this->powasSettings->members_micro_savings > 0 && $this->powasSettings->members_micro_savings != null) {
            $microSavings = $this->powasSettings->members_micro_savings;
        }

        $this->amountToPay = ($this->selectedBill->billing_amount + $this->selectedBill->penalty + $microSavings - $this->selectedBill->discount_amount - $this->excessPaymentFromDB) + ($this->afterDuePenalty + $this->reconnectionFee);

        $this->paymentAmount = number_format($this->amountToPay, 2, '.', '');
    }

    public function updatedReconnectionFee()
    {
        $this->resetErrorBag('reconnectionFee');

        $this->validate([
            'reconnectionFee' => ['required', 'numeric', 'min:0'],
        ], [], [
            'reconnectionFee' => 'reconnection fee',
        ]);
    }

    public function updatedAfterDuePenalty()
    {
        $this->resetErrorBag('afterDuePenalty');

        $this->validate([
            'afterDuePenalty' => ['required', 'numeric', 'min:0'],
        ], [], [
            'afterDuePenalty' => 'after due date penalty',
        ]);
    }

    public function updatedPaymentAmount()
    {
        $this->resetErrorBag('paymentAmount');

        $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0', 'gte:' . $this->amountToPay],
        ], [], [
            'paymentAmount' => 'payment amount',
        ]);

        // dd($this->withReconnectionFee);
    }

    public function confirmSave()
    {
        $this->validate([
            'paymentDate' => ['required', 'before_or_equal:today', 'after_or_equal:' . $this->selectedBill->cut_off_end],
            'afterDuePenalty' => ['required', 'numeric', 'min:0'],
            'reconnectionFee' => ['required', 'numeric', 'min:0'],
            'paymentAmount' => ['required', 'numeric', 'min:0', 'gte:' . $this->amountToPay],
        ], [
            'paymentDate.before_or_equal' => 'Payment date must be equal or before ' . Carbon::parse(now())->format('m/d/y') . '.',
            'paymentDate.after_or_equal' => 'Payment date must be equal or after ' . Carbon::parse($this->selectedBill->cut_off_end)->format('m/d/Y') . '.',
        ], [
            'paymentDate' => 'payment date',
            'afterDuePenalty' => 'after due date penalty',
            'reconnectionFee' => 'reconnection fee',
            'paymentAmount' => 'payment amount',
        ]);

        $this->reset([
            'toPrintReceipts',
        ]);

        $this->showingConfirmSaveModal = true;
    }

    public function printAllReceipt()
    {
        $this->reset([
            'toPrintReceipts',
        ]);

        $this->printAllReceipts = true;
    }

    public function savePayment()
    {
        try {
        // Validate all required chart of accounts exist first
        $cashOnHandAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'CASH' . '%')->first();
        if (!$cashOnHandAccount) {
            throw new \Exception('Chart of Accounts error: CASH ON HAND account not found. Please check your chart of accounts configuration.');
        }

        $newPenalty = 0;
        $microSavingsAmount = 0;

        $previous_reading_id = $this->selectedBill->previous_reading_id;
        $present_reading_id = $this->selectedBill->present_reading_id;

        $present_reading = Readings::find($present_reading_id);
        $previous_reading = Readings::find($previous_reading_id);

        if (!$present_reading) {
            throw new \Exception('Error: Present reading record not found (ID: ' . $present_reading_id . '). Cannot calculate usage.');
        }

        if (!$previous_reading) {
            throw new \Exception('Error: Previous reading record not found (ID: ' . $previous_reading_id . '). Cannot calculate usage.');
        }

        // cubic_meter_used is stored in the billing record and was computed at billing time
        // (accounting for any mid-cycle meter changes). No need to recalculate here.
        $cubic_meter_used = $this->selectedBill->cubic_meter_used;

        $this->toPrintReceipts[] = $this->selectedBill->billing_id;

        $this->selectedMember = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')->where('powas_members.member_id', $this->selectedBill->member_id)->first();

        // Generate journal entry number ONCE for all transactions in this payment
        $journalEntryNumber = CustomNumberFactory::journalEntryNumber($this->powasID, $this->paymentDate);

        if ($this->withReconnectionFee == true) {
            // ReconnectionFees::create([
            //     'reconnection_id' => CustomNumberFactory::getRandomID(),
            //     'powas_id' => $this->powasID,
            //     'member_id' => $this->selectedBill->member_id,
            //     // ...
            // ]);

            // ActionLogger::dispatch(...);

            $reconnectionFeeAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'RECONNECTION FEE' . '%')->first();
            if (!$reconnectionFeeAccount) {
                throw new \Exception('Chart of Accounts error: RECONNECTION FEE account not found.');
            }

            // For Reconnection Fee
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $reconnectionFeeAccount->account_number,
                'description' => 'Reconnection fee received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->reconnectionFee,
                'transaction_side' => $reconnectionFeeAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($reconnectionFeeAccount->account_name) . '</i></b> with description <b>"' . 'Reconnection Fee received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->reconnectionFee, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

            // For Cash
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $cashOnHandAccount->account_number,
                'description' => 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Reconnection Fee',
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->reconnectionFee,
                'transaction_side' => $cashOnHandAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccount->account_name) . '</i></b> with description <b>"' . 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Reconnection Fee"</b> amounting to <b>&#8369;' . number_format($this->reconnectionFee, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        }

        if ($this->afterDuePenalty > 0) {
            $oldPenalty = $this->selectedBill->penalty;
            $newPenalty = $oldPenalty + $this->afterDuePenalty;
            $this->selectedBill->penalty = $newPenalty;
            $this->selectedBill->save();

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated penalty from <b><i>' . number_format($oldPenalty, 2) . '</i></b> to <b><i>' . number_format($newPenalty, 2) . '</i></b> for with reference number <i><u>' . $this->selectedBill->billing_id . '</u></i> and POWAS ID <b>' . $this->powasID . '</b>.';

            ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billings', $this->powasID);

            $penaltiesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' .'PENALTIES' . '%')->first();
            if (!$penaltiesAccount) {
                throw new \Exception('Chart of Accounts error: PENALTIES account not found.');
            }

            // Reuse the same journal entry number for grouping

            // For Penalties
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $penaltiesAccount->account_number,
                'description' => 'Penalty payment received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $newPenalty,
                'transaction_side' => $penaltiesAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($penaltiesAccount->account_name) . '</i></b> with description <b>"' . 'Penalty payment received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($newPenalty, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

            // For Cash
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $cashOnHandAccount->account_number,
                'description' => 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Penalty',
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $newPenalty,
                'transaction_side' => $cashOnHandAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccount->account_name) . '</i></b> with description <b>"' . 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Penalty"</b> amounting to <b>&#8369;' . number_format($newPenalty, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        } else {
            $oldPenalty = $this->selectedBill->penalty;
            if ($oldPenalty > 0) {
                $newPenalty = $oldPenalty + $this->afterDuePenalty;
                $this->selectedBill->penalty = $newPenalty;
                $this->selectedBill->save();

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated penalty from <b><i>' . number_format($oldPenalty, 2) . '</i></b> to <b><i>' . number_format($newPenalty, 2) . '</i></b> for with reference number <i><u>' . $this->selectedBill->billing_id . '</u></i> and POWAS ID <b>' . $this->powasID . '</b>.';

                ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billings', $this->powasID);

                $penaltiesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'PENALTIES' . '%')->first();
                if (!$penaltiesAccount) {
                    throw new \Exception('Chart of Accounts error: PENALTIES account not found.');
                }

                // Reuse the same journal entry number for grouping

                // For Penalties
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $penaltiesAccount->account_number,
                    'description' => 'Penalty payment received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $newPenalty,
                    'transaction_side' => $penaltiesAccount->normal_balance,
                    'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                    'paid_to' => $this->selectedBill->billing_id,
                    'member_id' => $this->selectedBill->member_id,
                    'powas_id' => $this->selectedBill->powas_id,
                    'recorded_by_id' => Auth::user()->user_id,
                    'transaction_date' => $this->paymentDate,
                ]);

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($penaltiesAccount->account_name) . '</i></b> with description <b>"' . 'Penalty payment received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($newPenalty, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

                // For Cash
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $cashOnHandAccount->account_number,
                    'description' => 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Penalty',
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $newPenalty,
                    'transaction_side' => $cashOnHandAccount->normal_balance,
                    'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                    'paid_to' => $this->selectedBill->billing_id,
                    'member_id' => $this->selectedBill->member_id,
                    'powas_id' => $this->selectedBill->powas_id,
                    'recorded_by_id' => Auth::user()->user_id,
                    'transaction_date' => $this->paymentDate,
                ]);

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccount->account_name) . '</i></b> with description <b>"' . 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Penalty"</b> amounting to <b>&#8369;' . number_format($newPenalty, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
            }
        }

        if ($this->powasSettings->members_micro_savings > 0 && $this->powasSettings->members_micro_savings != null) {
            $microSavingsAmount = floatval($this->powasSettings->members_micro_savings);

            $microSavings = MicroSavings::where('member_id', $this->selectedBill->member_id)
                ->orderByDesc('date_recorded')->first();

            if ($microSavings != null) {
                $msBalance = $microSavings->balance + $this->powasSettings->members_micro_savings;
            } else {
                $msBalance = 0;
            }

            MicroSavings::create([
                'savings_id' => CustomNumberFactory::getRandomID(),
                'powas_id' => $this->powasID,
                'member_id' => $this->selectedBill->member_id,
                'recorded_by' => Auth::user()->user_id,
                'billing_id' => $this->selectedBill->billing_id,
                'deposit' => $this->powasSettings->members_micro_savings,
                'balance' => $msBalance + $this->powasSettings->members_micro_savings,
                'date_recorded' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created micro-savings deposit amounting to <b><i>₱' . number_format($this->powasSettings->members_micro_savings, 2) . '</i></b> for member id <b>' . $this->selectedBill->member_id . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transanctions', $this->powasID);

            $microSavingsAccount = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'MICRO-SAVINGS' . '%')->first();
            if (!$microSavingsAccount) {
                throw new \Exception('Chart of Accounts error: MICRO-SAVINGS account not found.');
            }

            // Reuse the same journal entry number for grouping

            // For Micro-Savings
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $microSavingsAccount->account_number,
                'description' => 'Micro-savings deposit from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->powasSettings->members_micro_savings,
                'transaction_side' => $microSavingsAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($microSavingsAccount->account_name) . '</i></b> with description <b>"' . 'Micro-Savings Deposit from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->powasSettings->members_micro_savings, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

            // For Cash
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $cashOnHandAccount->account_number,
                'description' => 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Micro-Savings Deposit',
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->powasSettings->members_micro_savings,
                'transaction_side' => $cashOnHandAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccount->account_name) . '</i></b> with description <b>"' . 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Micro-Savings Deposit"</b> amounting to <b>&#8369;' . number_format($this->powasSettings->members_micro_savings, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        }

        // Reuse the same journal entry number for grouping

        if ($this->selectedBill->discount_amount > 0) {
            $discountsAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'DISCOUNT' . '%')->first();
            if (!$discountsAccount) {
                throw new \Exception('Chart of Accounts error: DISCOUNT account not found.');
            }

            // For Discount
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $discountsAccount->account_number,
                'description' => 'Discount for ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->selectedBill->discount_amount,
                'transaction_side' => $discountsAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($discountsAccount->account_name) . '</i></b> with description <b>"' . 'Discount for ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->selectedBill->discount_amount, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        }

        if ($this->withExcessPayments == true) {
            $excessPaymentAccount = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'EXCESS PAYMENTS' . '%')->first();
            if (!$excessPaymentAccount) {
                throw new \Exception('Chart of Accounts error: EXCESS PAYMENTS account not found.');
            }

            // For Excess Payment
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $excessPaymentAccount->account_number,
                'description' => 'Excess Payments debited from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $this->excessPaymentFromDB,
                'transaction_side' => 'DEBIT',  // Contra side - debiting liability to reduce excess payments
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            // FIXED: Use correct amount (excessPaymentFromDB, not discount_amount)
            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($excessPaymentAccount->account_name) . '</i></b> with description <b>"' . 'Excess Payments debited from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->excessPaymentFromDB, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        }

        $amountPaid = $this->paymentAmount - ($newPenalty + $microSavingsAmount + $this->reconnectionFee) + $this->selectedBill->discount_amount;

        // BillsPayments::create([
        //     'payment_id' => CustomNumberFactory::getRandomID(),
        //     'powas_id' => $this->powasID,
        //     'member_id' => $this->selectedBill->member_id,
        //     'recorded_by' => Auth::user()->user_id,
        //     'billing_id' => $this->selectedBill->billing_id,
        //     'amount_paid' => $amountPaid,
        //     'date_paid' => $this->paymentDate,
        // ]); Please take note for the possibility of having excess payments which shall be observed

        // $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created bills payment amounting to <b><i>₱' . number_format($amountPaid, 2) . '</i></b> for billing id <b>' . $this->selectedBill->billing_id . '</b>.';

        // ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

        $billsReceivableAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();
        if (!$billsReceivableAccount) {
            throw new \Exception('Chart of Accounts error: BILLS RECEIVABLES account not found.');
        }

        // For Bills Receivables
        Transactions::create([
            'trxn_id' => CustomNumberFactory::getRandomID(),
            'account_number' => $billsReceivableAccount->account_number,
            'description' => 'Bills Receivables received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
            'journal_entry_number' => $journalEntryNumber,
            'amount' => $this->selectedBill->billing_amount,
            'transaction_side' => 'CREDIT',  // Contra side - crediting asset to reduce receivables when paid
            'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
            'paid_to' => $this->selectedBill->billing_id,
            'member_id' => $this->selectedBill->member_id,
            'powas_id' => $this->selectedBill->powas_id,
            'recorded_by_id' => Auth::user()->user_id,
            'transaction_date' => $this->paymentDate,
        ]);

        // FIXED: Use billing_amount (the actual transaction amount), not amountPaid
        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($billsReceivableAccount->account_name) . '</i></b> with description <b>"' . 'Bills Receivables received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($this->selectedBill->billing_amount, 2) . '</b>.';

        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

        if ($this->withReconnectionFee == false) {
            $this->reconnectionFee = 0;
        } else {
            if ($this->powasSettings->reconnection_fee > 0 || $this->powasSettings->reconnection_fee != null) {
                $this->reconnectionFee = $this->powasSettings->reconnection_fee;
            } else {
                $this->reconnectionFee = 0;
            }
        }

        // For Cash
        Transactions::create([
            'trxn_id' => CustomNumberFactory::getRandomID(),
            'account_number' => $cashOnHandAccount->account_number,
            'description' => 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Bills Receivables',
            'journal_entry_number' => $journalEntryNumber,
            'amount' => $this->paymentAmount - $microSavingsAmount - $this->reconnectionFee - $newPenalty,
            'transaction_side' => $cashOnHandAccount->normal_balance,
            'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
            'paid_to' => $this->selectedBill->billing_id,
            'member_id' => $this->selectedBill->member_id,
            'powas_id' => $this->selectedBill->powas_id,
            'recorded_by_id' => Auth::user()->user_id,
            'transaction_date' => $this->paymentDate,
        ]);

        // FIXED: Use actual cash amount from the transaction
        $cashAmount = $this->paymentAmount - $microSavingsAmount - $this->reconnectionFee - $newPenalty;
        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccount->account_name) . '</i></b> with description <b>"' . 'Cash received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . ' for Bills Receivables"</b> amounting to <b>&#8369;' . number_format($cashAmount, 2) . '</b>.';

        ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);

        $excessPayment = $this->paymentAmount - $this->amountToPay;

        if ($excessPayment > 0) {
            $excessPaymentAccount = ChartOfAccounts::where('account_type', 'LIABILITY')->where('account_name', 'LIKE', '%' . 'EXCESS PAYMENTS' . '%')->first();
            if (!$excessPaymentAccount) {
                throw new \Exception('Chart of Accounts error: EXCESS PAYMENTS account not found.');
            }

            // For Excess Payment
            Transactions::create([
                'trxn_id' => CustomNumberFactory::getRandomID(),
                'account_number' => $excessPaymentAccount->account_number,
                'description' => 'Excess Payments received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'journal_entry_number' => $journalEntryNumber,
                'amount' => $excessPayment,
                'transaction_side' => $excessPaymentAccount->normal_balance,
                'received_from' => $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename,
                'paid_to' => $this->selectedBill->billing_id,
                'member_id' => $this->selectedBill->member_id,
                'powas_id' => $this->selectedBill->powas_id,
                'recorded_by_id' => Auth::user()->user_id,
                'transaction_date' => $this->paymentDate,
            ]);

            $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($excessPaymentAccount->account_name) . '</i></b> with description <b>"' . 'Excess Payments received from ' . $this->selectedMember->lastname . ', ' . $this->selectedMember->firstname . ' ' . $this->selectedMember->middlename . '"</b> amounting to <b>&#8369;' . number_format($excessPayment, 2) . '</b>.';

            ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $this->powasID);
        }

        $oldBillStat = '"' . $this->selectedBill->bill_status . '"';
        $newBillStat =  '"PAID"';

        $this->selectedBill->bill_status = 'PAID';
        $this->selectedBill->save();

        $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> updated bill status from <b><i>' . $oldBillStat . '</i></b> to <b><i>' . $newBillStat . '</i></b> for with billing id <i><u>' . $this->selectedBill->billing_id . '</u></i> and POWAS ID <b>' . $this->powasID . '</b>.';

        ActionLogger::dispatch('update', $log_message, Auth::user()->user_id, 'billings', $this->powasID);

        $this->dispatch('alert', [
            'message' => 'Payment successfully saved!',
            'messageType' => 'info',
            'position' => 'top-right',
        ]);

        $this->dispatch('transaction-added')->to(TransactionsList::class);

        $this->reset([
            'paymentDate',
            'afterDuePenalty',
            'reconnectionFee',
            'paymentAmount',
        ]);
        $this->showingAddPaymentModal = false;
        $this->showingConfirmSaveModal = false;

        if ($this->printAllReceipts == false) {
            $this->showingConfirmPrintModal = true;
        } else {
            $this->printAllReceipts = false;
        }

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error: ' . $e->getMessage(),
                'messageType' => 'error',
                'position' => 'top-right',
            ]);
            $this->saveError[] = $e->getMessage();
            $this->showingConfirmSaveModal = false;
        }
    }

    public function printReceipt()
    {
        $this->showingConfirmPrintModal = false;
    }

    public function updatedStartDate()
    {
        $this->validateOnly('startDate');
    }

    public function updatedEndDate()
    {
        $this->validateOnly('endDate');
    }

    public function clearFilter()
    {
        $this->reset([
            'search',
            'startDate',
            'endDate',
            'pagination',
        ]);

        $this->resetErrorBag();

        $this->resetPage('billings');

        $this->dispatch('alert', [
            'message' => 'All filters cleared!',
            'messageType' => 'info',
            'position' => 'top-right',
        ]);
    }



    public function mount($powasID)
    {
        $this->powas = Powas::find($powasID);
        $this->powasID = $powasID;
        $this->powasSettings = PowasSettings::where('powas_id', $powasID)->first();
        $this->paymentDate = Carbon::parse(now())->format('Y-m-d');
    }

    public function render()
    {
        $users = User::all();
        $this->powas = Powas::find($this->powasID);
        $this->powasSettings = PowasSettings::where('powas_id', $this->powasID)->first();

        $this->membersList = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_status', 'ACTIVE')
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        $this->billingMonths = Billings::select(DB::raw('DISTINCT(billing_month)'))
            ->where('powas_id', $this->powasID)
            ->orderBy('billing_month', 'desc')
            ->limit(24)
            ->get();

        $query = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('billings.powas_id', $this->powasID);

        if ($this->filterBillingMonth != 'All') {
            $query->where('billings.billing_month', $this->filterBillingMonth);
        }

        if ($this->search) {
            $searchTerm = '%' . strtoupper($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('powas_applications.lastname', 'like', $searchTerm)
                    ->orWhere('powas_applications.firstname', 'like', $searchTerm)
                    ->orWhere('powas_applications.middlename', 'like', $searchTerm)
                    ->orWhere('billings.billing_id', 'like', $searchTerm)
                    ->orWhere('billings.bill_status', 'like', $searchTerm)
                    ->orWhere('powas_members.member_id', 'like', $searchTerm);
            });
        }

        $powasBillings = $query->orderBy('billings.billing_month', 'desc')
            ->orderBy('billings.bill_status', 'desc')
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->paginate($this->pagination, ['*'], 'billings');

        $usersList = [];
        $readingsList = [];

        $transacted = [];
        $billsReceivablesAccount = ChartOfAccounts::where('account_type', 'ASSET')->where('account_name', 'LIKE', '%' . 'BILLS RECEIVABLES' . '%')->first();

        foreach ($users as $key => $value) {
            $usersList[$value->user_id] = $value->userinfo->lastname . ', ' . $value->userinfo->firstname;
        }

        foreach ($powasBillings as $value) {
            $readingsList[$value->billing_id] = [
                'previous_reading' => number_format(Readings::find($value->previous_reading_id)->reading, 2),
                'present_reading' => number_format(Readings::find($value->present_reading_id)->reading, 2),
            ];
            $isTransacted = Transactions::where('transaction_side', 'CREDIT')
                ->where('paid_to', $value->billing_id)
                ->where('account_number', $billsReceivablesAccount->account_number)
                ->exists();

            if ($isTransacted == true) {
                $transacted[$value->billing_id] = 'YES';
            } else {
                $transacted[$value->billing_id] = 'NO';
            }
        }

        $this->reset([
            'existingBillingCount',
            'notExistingBillingCount',
        ]);

        $this->reset([
            'powasSettingsChanges',
        ]);

        $readingIDs = [];
        $existingBills = [];

        foreach ($this->membersList as $member) {
            $readingExists = Readings::where('member_id', $member->member_id)->exists();
            if ($readingExists == true) {
                $readingIDs[] = Readings::where('member_id', $member->member_id)
                    ->orderBy('reading_date', 'desc')
                    ->first()->reading_id;
            }
        }

        foreach ($readingIDs as $readID) {
            $reading = Readings::find($readID);

            if ($reading->reading_count > 1) {
                $billQuery = Billings::where('present_reading_id', $readID)->exists();
                if ($billQuery == true) {
                    $existingBills[] = $readID;
                    $bill = Billings::where('present_reading_id', $readID)->first();

                    $prevRead = Readings::where('reading_id', $bill->previous_reading_id)->first()->reading;
                    $presRead = Readings::where('reading_id', $bill->present_reading_id)->first()->reading;

                    $new_cm_used = $presRead - $prevRead;
                    $new_billing_amount = $new_cm_used * $this->powasSettings->water_rate;

                    $due_date_day = $this->powasSettings->due_date_day;

                    if ($this->powasSettings->due_date_day < 10) {
                        $due_date_day = '0' . $this->powasSettings->due_date_day;
                    }

                    $new_due_date = Carbon::parse($reading->reading_date)->addMonth()->format('Y-m-' . $due_date_day);
                    $new_due_date = Carbon::parse($new_due_date)->format('Y-m-d');

                    if ($new_cm_used <= 5) {
                        $new_billing_amount = $this->powasSettings->minimum_payment;
                        $this->isMinimum = true;
                    } else {
                        $new_billing_amount = $new_cm_used * $this->powasSettings->water_rate;
                        $this->isMinimum = false;
                    }

                    if ($bill->cubic_meter_used != number_format($new_cm_used, 2)) {
                        $this->powasSettingsChanges['reading'] = 'reading';
                    }

                    if (number_format($bill->billing_amount, 2) != number_format($new_billing_amount, 2)) {
                        $this->powasSettingsChanges['water_rate'] = 'water rate';
                    }
                }
            }
        }

        $baseMember = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_status', 'ACTIVE')
            ->where('powas_applications.powas_id', $this->powasID)
            ->orderBy('powas_members.membership_date', 'asc')
            ->first();

        $baseReading = Readings::orderBy('reading_date', 'desc')->first();

        return view('livewire.powas.powas-billings', [
            'powasBillings' => $powasBillings,
            'usersList' => $usersList,
            'readingsList' => $readingsList,
            'existingBills' => $existingBills,
            'readingIDs' => $readingIDs,
            'baseReading' => $baseReading,
            'transacted' => $transacted,
        ]);
    }
}
