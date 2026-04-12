<?php

namespace App\Console\Commands;

use App\Models\Transactions;
use Illuminate\Console\Command;

class FindUnbalancedJournalEntries extends Command
{
    protected $signature = 'app:find-unbalanced-journal-entries';
    protected $description = 'Find journal entries where debits ≠ credits';

    public function handle()
    {
        $this->info('Checking all journal entries...');
        
        $transactions = Transactions::all();
        $journalEntries = $transactions->groupBy('journal_entry_number');
        
        $this->info("Total journal entries: " . $journalEntries->count());
        $this->newLine();
        
        $unbalanced = [];
        
        foreach ($journalEntries as $jeNum => $entries) {
            $debits = $entries->where('transaction_side', 'DEBIT')->sum('amount');
            $credits = $entries->where('transaction_side', 'CREDIT')->sum('amount');
            $diff = $debits - $credits;
            
            if (abs($diff) > 0.01) {
                $unbalanced[] = [
                    'je_number' => $jeNum,
                    'debits' => $debits,
                    'credits' => $credits,
                    'difference' => $diff,
                    'date' => $entries->first()->transaction_date,
                    'count' => $entries->count()
                ];
            }
        }
        
        if (count($unbalanced) == 0) {
            $this->info('✓ All journal entries are balanced!');
            return;
        }
        
        $this->error("Found " . count($unbalanced) . " unbalanced journal entries:");
        $this->newLine();
        
        foreach ($unbalanced as $je) {
            $this->line("JE: {$je['je_number']} ({$je['date']})");
            $this->line("  Transactions: {$je['count']}");
            $this->line("  Debits:  " . number_format($je['debits'], 2));
            $this->line("  Credits: " . number_format($je['credits'], 2));
            $this->error("  Diff:    " . number_format($je['difference'], 2));
            $this->newLine();
        }
        
        $totalDiff = array_sum(array_column($unbalanced, 'difference'));
        $this->warn("Total cumulative difference: " . number_format($totalDiff, 2));
    }
}
