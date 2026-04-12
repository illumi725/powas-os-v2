<?php

namespace App\Console\Commands;

use App\Factory\CustomNumberFactory;
use App\Models\Billings;
use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AdjustBillsReceivables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:adjust-bills-receivables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adjust Bills Receivables balance to match actual unpaid bills';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Calculating Bills Receivables adjustment...');

        // Calculate current balance
        $beginningBalance = 15672; // From JSON
        $debits = Transactions::where('account_number', 105)
            ->where('transaction_side', 'DEBIT')
            ->where('transaction_date', '>', '2023-12-31')
            ->sum('amount');
        $credits = Transactions::where('account_number', 105)
            ->where('transaction_side', 'CREDIT')
            ->where('transaction_date', '>', '2023-12-31')
            ->sum('amount');
        
        $currentBalance = $beginningBalance + $debits - $credits;
        
        // Get actual unpaid bills
        $unpaidBills = Billings::where('bill_status', 'UNPAID')->sum('billing_amount');
        
        // Calculate adjustment
        $adjustment = $unpaidBills - $currentBalance;
        
        $this->info("Current Bills Receivables Balance: {$currentBalance}");
        $this->info("Actual Unpaid Bills: {$unpaidBills}");
        $this->info("Adjustment Needed: {$adjustment}");
        
        if (abs($adjustment) < 0.01) {
            $this->info('Bills Receivables is already correct!');
            return;
        }

        $this->line('');
        $this->warn('This will create an adjustment transaction to correct the balance.');
        
        DB::beginTransaction();
        try {
            $journalEntryNumber = CustomNumberFactory::journalEntryNumber('NEC-SJC-PIN-004', '2023-12-31');
            $userId = DB::table('users')->first()->user_id ?? 1;
            
            if ($adjustment > 0) {
                // Need to increase Bills Receivables - DEBIT it
                // And CREDIT Prior Period Adjustment (use Misc Income 406 or create equity adjustment)
                
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => 105, // Bills Receivables
                    'description' => 'Prior Period Adjustment - Legacy Bills',
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => abs($adjustment),
                    'transaction_side' => 'DEBIT',
                    'received_from' => 'System Adjustment',
                    'paid_to' => null,
                    'member_id' => null,
                    'powas_id' => 'NEC-SJC-PIN-004',
                    'recorded_by_id' => $userId,
                    'transaction_date' => '2023-12-31',
                ]);
                
                // Credit to balance it - use account 406 (Misc Income) to ensure it hits P&L
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => 406, // Miscellaneous Income
                    'description' => 'Prior Period Adjustment - Legacy Bills',
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => abs($adjustment),
                    'transaction_side' => 'CREDIT',
                    'received_from' => 'System Adjustment',
                    'paid_to' => null,
                    'member_id' => null,
                    'powas_id' => 'NEC-SJC-PIN-004',
                    'recorded_by_id' => $userId,
                    'transaction_date' => '2023-12-31',
                ]);
                
                $this->line("Created DEBIT to Bills Receivables: " . abs($adjustment));
                $this->line("Created CREDIT to Net Income: " . abs($adjustment));
                
            } else {
                // Need to decrease Bills Receivables - CREDIT it
                // And DEBIT expense or contra-revenue
                
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => 105, // Bills Receivables
                    'description' => 'Prior Period Adjustment - Legacy Bills',
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => abs($adjustment),
                    'transaction_side' => 'CREDIT',
                    'received_from' => 'System Adjustment',
                    'paid_to' => null,
                    'member_id' => null,
                    'powas_id' => 'NEC-SJC-PIN-004',
                    'recorded_by_id' => $userId,
                    'transaction_date' => '2023-12-31',
                ]);
                
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => 302, // Net Income
                    'description' => 'Prior Period Adjustment - Legacy Bills',
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => abs($adjustment),
                    'transaction_side' => 'DEBIT',
                    'received_from' => 'System Adjustment',
                    'paid_to' => null,
                    'member_id' => null,
                    'powas_id' => 'NEC-SJC-PIN-004',
                    'recorded_by_id' => $userId,
                    'transaction_date' => '2023-12-31',
                ]);
                
                $this->line("Created CREDIT to Bills Receivables: " . abs($adjustment));
                $this->line("Created DEBIT to Net Income: " . abs($adjustment));
            }
            
            DB::commit();
            $this->info('Adjustment complete!');
            
            // Verify - recalculate including the new adjustment on 2023-12-31
            $debitsAfter = Transactions::where('account_number', 105)
                ->where('transaction_side', 'DEBIT')
                ->where('transaction_date', '>', '2023-12-31')
                ->sum('amount');
            $creditsAfter = Transactions::where('account_number', 105)
                ->where('transaction_side', 'CREDIT')
                ->where('transaction_date', '>', '2023-12-31')
                ->sum('amount');
            $adjustmentOnDate = Transactions::where('account_number', 105)
                ->where('transaction_date', '=', '2023-12-31')
                ->where('description', 'LIKE', '%Prior Period Adjustment%')
                ->get();
            $adjustmentDebit = $adjustmentOnDate->where('transaction_side', 'DEBIT')->sum('amount');
            $adjustmentCredit = $adjustmentOnDate->where('transaction_side', 'CREDIT')->sum('amount');
            
            $newBalance = $beginningBalance + $adjustmentDebit - $adjustmentCredit + $debitsAfter - $creditsAfter;
            
            $this->info("New Bills Receivables Balance: {$newBalance}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
