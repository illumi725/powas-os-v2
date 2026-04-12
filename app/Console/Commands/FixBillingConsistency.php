<?php

namespace App\Console\Commands;

use App\Models\Billings;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixBillingConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-billing-consistency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix inconsistencies between billings and transactions by aligning transaction amounts to billing amounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting consistency fix...');

        $billings = Billings::where('bill_status', 'PAID')->get();
        $fixedCount = 0;
        $skippedCount = 0;

        foreach ($billings as $billing) {
            $transactions = Transactions::where('paid_to', $billing->billing_id)->get();
            
            // Find the Bills Receivables Credit transaction
            // We assume there is only one main payment transaction for the bill itself in this simple scenario
            $billsReceivableCredit = $transactions->filter(function ($trxn) {
                // Determine if it's the payment for the bill.
                // Based on previous checks, we look for CREDIT to account linked to Bills Receivables.
                // We don't have the Account ID hardcoded easily, but we can check the name or side.
                // "Bills Receivables received..."
                return trim($trxn->description) == "Bills Receivables received from " . trim($trxn->received_from) 
                    && $trxn->transaction_side == 'CREDIT';
            })->first();

            if (!$billsReceivableCredit) {
                 // Try looser matching if exact description fails, or check account 105 from dump
                 $billsReceivableCredit = $transactions->filter(function ($trxn) {
                    return $trxn->transaction_side == 'CREDIT' && str_contains($trxn->description, 'Bills Receivables');
                })->first();
            }

            if (!$billsReceivableCredit) {
                // If still not found, search by account number if known, or skip
                // In the dump, account_number was 105 for Bills Receivables.
                 $billsReceivableCredit = $transactions->filter(function ($trxn) {
                    return $trxn->account_number == 105 && $trxn->transaction_side == 'CREDIT';
                })->first();
            }

            if ($billsReceivableCredit) {
                $currentPaidAmount = $billsReceivableCredit->amount;
                
                if ($currentPaidAmount > $billing->billing_amount) {
                    
                    // Verify it is the 1.2x case or similar overpayment
                    $this->info("Fixing Bill {$billing->billing_id}: Paid {$currentPaidAmount} > Bill {$billing->billing_amount}");

                    // Find the corresponding Debit (Cash) transaction
                    // Usually shares the same journal_entry_number and amount
                    $cashDebit = Transactions::where('journal_entry_number', $billsReceivableCredit->journal_entry_number)
                        ->where('amount', $currentPaidAmount)
                        ->where('transaction_side', 'DEBIT')
                        ->where('paid_to', $billing->billing_id)
                        ->first();
                    
                    DB::beginTransaction();
                    try {
                        // Update Credit
                        $billsReceivableCredit->amount = $billing->billing_amount;
                        $billsReceivableCredit->save();

                        // Update Debit if found
                        if ($cashDebit) {
                            $cashDebit->amount = $billing->billing_amount;
                            $cashDebit->save();
                            $this->line("   Updated Cash Debit {$cashDebit->trxn_id} and BillsReceivable Credit {$billsReceivableCredit->trxn_id}");
                        } else {
                            $this->warn("   Warning: Could not find matching Cash Debit for Bill {$billing->billing_id}. Only updated Credit.");
                        }
                        
                        // Also check for Revenue transaction?
                        // In GeneratedBilling.php, when a bill is created, a "Revenue" transaction (Credit) and "Bills Receivable" (Debit) are created.
                        // When PAID, we Credit Bills Receivable and Debit Cash.
                        // BUT, looking at the dump earlier (Step 61):
                        // There was a "Revenue from..." Credit (trxn 5481281608363755486) and "Bills Receivable generated..." Debit (trxn 4625785184839899641).
                        // These were created at 2025-01-30.
                        // The PAYMENT were transactions:
                        // "Bills Receivables received..." Credit (3175733514181697102) - Amount 204.00
                        // "Cash received..." Debit (3364383626953471135) - Amount 204.00
                        //
                        // AND there were Revenue transactions created at 2025-01-28 (the bill generation time?)
                        // Wait, the dump shows:
                        // trxn 5481281608363755486 (Revenue Credit) Amount 204.00
                        // trxn 4625785184839899641 (Bills Rec Debit) Amount 204.00
                        //
                        // AND the BILLING record says billing_amount 170.00.
                        //
                        // So the INITIAL bill generation created transactions for 204.00.
                        // And the PAYMENT was for 204.00.
                        //
                        // So the BILLING RECORD itself was changed to 170.00, but the initial transactions (Accrual) and the payment (Cash) are both 204.00.
                        //
                        // If we align to 170.00, we must update:
                        // 1. The Payment: Credit Bills Rec, Debit Cash.
                        // 2. The Accrual: Debit Bills Rec, Credit Revenue.
                        //
                        // If we only update the payment, the Bills Receivable account will be unbalanced (Debit 204, Credit 170 -> Balance 34).
                        // We must update ALL transactions related to this bill that are 204.00 to 170.00.
                        
                        // Let's find ALL transactions for this bill with the old amount.
                        $allTrxns = Transactions::where('paid_to', $billing->billing_id)
                            ->where('amount', $currentPaidAmount)
                            ->get();
                        
                        $count = 0;
                        foreach ($allTrxns as $t) {
                            $t->amount = $billing->billing_amount;
                            $t->save();
                            $count++;
                        }
                        $this->line("   Updated $count transactions from $currentPaidAmount to {$billing->billing_amount}");

                        DB::commit();
                        $fixedCount++;

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("   Failed to update Bill {$billing->billing_id}: " . $e->getMessage());
                        $skippedCount++;
                    }

                } else {
                    // $this->line("Bill {$billing->billing_id} matches.");
                }
            }
        }

        $this->info("Fix complete. Fixed: {$fixedCount}, Skipped/Error: {$skippedCount}");
    }
}
