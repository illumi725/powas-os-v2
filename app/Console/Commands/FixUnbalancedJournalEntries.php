<?php

namespace App\Console\Commands;

use App\Factory\CustomNumberFactory;
use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUnbalancedJournalEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-unbalanced-journal-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix unbalanced journal entries by creating balancing transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding unbalanced journal entries...');

        $unbalanced = Transactions::selectRaw('journal_entry_number, 
            SUM(CASE WHEN transaction_side = "DEBIT" THEN amount ELSE 0 END) as total_debit,
            SUM(CASE WHEN transaction_side = "CREDIT" THEN amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN transaction_side = "DEBIT" THEN amount ELSE 0 END) - SUM(CASE WHEN transaction_side = "CREDIT" THEN amount ELSE 0 END) as diff')
            ->whereNotNull('journal_entry_number')
            ->groupBy('journal_entry_number')
            ->havingRaw('ABS(total_debit - total_credit) > 0.01')
            ->get();

        $this->info("Found {$unbalanced->count()} unbalanced journal entries.");
        $totalDiscrepancy = $unbalanced->sum('diff');
        $this->info("Total discrepancy: {$totalDiscrepancy}");

        if ($unbalanced->isEmpty()) {
            $this->info('All journal entries are balanced!');
            return;
        }

        $fixedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($unbalanced as $entry) {
                $journalEntryNumber = $entry->journal_entry_number;
                $diff = $entry->diff;

                $this->line("Fixing {$journalEntryNumber}: Diff = {$diff}");

                // Get one sample transaction from this journal entry for context
                $sample = Transactions::where('journal_entry_number', $journalEntryNumber)->first();

                if (!$sample) {
                    $this->warn("  No transactions found for {$journalEntryNumber}. Skipping.");
                    continue;
                }

                // Determine the balancing entry needed
                if ($diff > 0) {
                    // Too much debit, need to add credit
                    $side = 'CREDIT';
                    $accountNumber = 406; // Miscellaneous Income
                } else {
                    // Too much credit, need to add debit
                    $side = 'DEBIT';
                    $accountNumber = 510; // Miscellaneous Expense
                }

                $amount = abs($diff);

                // Create balancing transaction
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $accountNumber,
                    'description' => "Balancing Entry for {$journalEntryNumber}",
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $amount,
                    'transaction_side' => $side,
                    'received_from' => 'System Correction',
                    'paid_to' => null,
                    'member_id' => $sample->member_id,
                    'powas_id' => $sample->powas_id,
                    'recorded_by_id' => $sample->recorded_by_id,
                    'transaction_date' => $sample->transaction_date,
                ]);

                $this->line("  Created {$side} {$amount} to Account {$accountNumber}");
                $fixedCount++;
            }

            DB::commit();
            $this->info("Fixed {$fixedCount} journal entries.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }
}
