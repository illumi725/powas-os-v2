<?php

namespace App\Console\Commands;

use App\Models\Billings;
use App\Models\Transactions;
use Illuminate\Console\Command;

class CheckBillingConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-billing-consistency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inconsistencies between billings and transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting consistency check...');

        $billings = Billings::all();
        $inconsistencies = 0;

        foreach ($billings as $billing) {
            $transactions = Transactions::where('paid_to', $billing->billing_id)->get();
            
            // Calculate total paid amount from transactions
            // We are looking for "Bills Receivables" credits which represent payments against the bill
            // Based on AddPayment.php, account 103 (typially Bills Receivables, checking logic needed) is credited.
            // But simplify: Check for transactions where paid_to is this billing.
            // In AddPayment.php:
            // "Bills Receivables received..." amount is credit. account_number for Bills Receivables.
            // However, we should check what transactions are actually recorded.
            // Usually cash is debited and Bills Receivable is credited.
            // Or Cash is debited and Revenue is credited?
            // Let's rely on the sum of amounts of transactions linked to this bill which are NOT reversing entries if any.
            // Use 'paid_to' linkage.
            
            /*
             * In AddPayment.php:
             * 1. Reconnection Fee (Revenue) - Credit
             * 2. Cash (Debit)
             * 3. Penalty (Revenue) - Credit
             * 4. Cash (Debit)
             * 5. Micro-Savings (Liability) - Credit
             * 6. Cash (Debit)
             * 7. Discount (Revenue) - Credit (It says 'received from' but discounts are usually expense or reduction of revenue? In AddPayment it says transaction_side = normal_balance. Revenue normal balance is Credit.)
             * 8. Excess Payment from DB (Debit)
             * 9. Bills Receivables (Asset) - Credit.
             * 10. Cash (Debit)
             * 11. Excess Payment (Liability) - Credit.
             * 
             * Effectively, the amount that "Pays" the bill is the credit to "Bills Receivables" + "Discount" (?) + "Excess Payment from DB" usage?
             * Actually, simpler: The bill consists of `billing_amount`.
             * When paid, we expect `billing_amount` to be cleared.
             * The `bill_status` should be PAID if fully cleared.
             * 
             * Let's check:
             * If bill_status == 'PAID':
             *   Check if there are transactions linked.
             *   Check if there is a Credit to Bills Receivables matching billing_amount.
             * 
             * If bill_status == 'UNPAID':
             *   Check if there are NO transactions linked (or at least no Bills Receivables credits).
             */

            $billsReceivableCredits = $transactions->filter(function ($trxn) {
                // Assuming Bills Receivables account. 
                // We need to fetch account name or ID. 
                // Detailed check: Account name "BILLS RECEIVABLES"
                // Ideally we filter by account type/name properly, but for now let's inspect the transactions.
                return $trxn->chartofaccounts->account_name == 'BILLS RECEIVABLES' && $trxn->transaction_side == 'CREDIT';
            });
            
            $totalPaid = $billsReceivableCredits->sum('amount');

            // Also check for discounts applied at time of payment
             $discounts = $transactions->filter(function ($trxn) {
                return str_contains(strtoupper($trxn->chartofaccounts->account_name), 'DISCOUNT') && $trxn->transaction_side == 'CREDIT';
            });
            $totalDiscount = $discounts->sum('amount');
            
            // Total effective payment against the bill
            $totalCleared = $totalPaid; // Discount acts as payment conceptually? 
            // In AddPayment, amount_paid calculation:
            // $amountPaid = $this->paymentAmount - (...) + $this->selectedBill->discount_amount;
            // The transaction for BillsReceivable is created with amount = $this->selectedBill->billing_amount.
            // So we entered a Credit to BillsReceivable for the full billing amount.
            
            if ($billing->bill_status === 'PAID') {
                // Check if total paid matches billing amount
                if ($totalPaid < $billing->billing_amount) {
                     $this->error("Inconsistency: Underpaid Bill {$billing->billing_id}");
                     $this->line("   Status: PAID");
                     $this->line("   Billing Amount: {$billing->billing_amount}");
                     $this->line("   Total Paid: {$totalPaid}");
                     $inconsistencies++;
                } elseif ($totalPaid > $billing->billing_amount) {
                     // Check if it's the specific 1.2x pattern
                     $ratio = $billing->billing_amount > 0 ? $totalPaid / $billing->billing_amount : 0;
                     if (abs($ratio - 1.2) < 0.01) {
                         $this->warn("Inconsistency: Rate Mismatch (1.2x) for Bill {$billing->billing_id}");
                     } else {
                         $this->warn("Inconsistency: Overpaid Bill {$billing->billing_id}");
                     }
                     $this->line("   Status: PAID");
                     $this->line("   Billing Amount: {$billing->billing_amount}");
                     $this->line("   Total Paid: {$totalPaid}");
                     $inconsistencies++;
                }
            } elseif ($billing->bill_status === 'UNPAID') {
                if ($totalPaid > 0) {
                     $this->error("Inconsistency: Unpaid Bill has payments {$billing->billing_id}");
                     $this->line("   Status: UNPAID");
                     $this->line("   Total Paid: {$totalPaid}");
                     $inconsistencies++;
                }
            } elseif ($billing->bill_status === 'PARTIAL') {
                 if ($totalPaid > $billing->billing_amount) {
                      $this->warn("Inconsistency: Partial Bill Overpaid {$billing->billing_id}");
                      $this->line("   Paid: {$totalPaid} / {$billing->billing_amount}");
                      $inconsistencies++;
                 }
            }
        }

        $this->newLine();
        $this->info("Check complete. Found {$inconsistencies} inconsistencies.");
    }
}
