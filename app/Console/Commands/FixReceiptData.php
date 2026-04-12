<?php

namespace App\Console\Commands;

use App\Models\IssuedReceipts;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Factory\CustomNumberFactory;

class FixReceiptData extends Command
{
    protected $signature = 'receipts:fix {--dry-run : Preview changes without applying them}';
    protected $description = 'Fix missing receipts for Application/Membership Fee transactions';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
        } else {
            $this->error('⚠️  LIVE MODE - Changes WILL be applied to database!');
            if (!$this->confirm('Do you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('         RECEIPT DATA FIX UTILITY');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Find all Application/Membership Fee CREDIT transactions without receipts
        $missingReceiptTransactions = DB::table('transactions as t')
            ->leftJoin('issued_receipts as ir', 't.trxn_id', '=', 'ir.trxn_id')
            ->where(function($q) {
                $q->where('t.description', 'LIKE', '%Application Fee%')
                  ->orWhere('t.description', 'LIKE', '%Membership Fee%');
            })
            ->where('t.transaction_side', 'CREDIT') // Revenue side only
            ->whereNull('ir.trxn_id')
            ->select('t.*')
            ->orderBy('t.transaction_date', 'asc')
            ->get();

        $this->info("Found {$missingReceiptTransactions->count()} transactions missing receipts");
        $this->newLine();

        if ($missingReceiptTransactions->count() == 0) {
            $this->info('✓ No missing receipts to fix!');
            return 0;
        }

        // Group by journal entry number
        $grouped = $missingReceiptTransactions->groupBy('journal_entry_number');
        
        $this->info("Grouped into " . $grouped->count() . " journal entries");
        $this->newLine();

        $createdCount = 0;
        $errorCount = 0;

        foreach ($grouped as $je => $transactions) {
            $first = $transactions->first();
            $powasId = $first->powas_id;
            $transactionDate = $first->transaction_date;

            $this->line("Processing JE: {$je} | Date: {$transactionDate} | Transactions: {$transactions->count()}");

            try {
                // Generate receipt number
                $receiptNumber = CustomNumberFactory::receipt($powasId, $transactionDate);
                
                $this->line("  → Receipt Number: {$receiptNumber}");

                // Create receipt for each transaction in this journal entry
                foreach ($transactions as $trxn) {
                    if (!$dryRun) {
                        $printId = CustomNumberFactory::getRandomID();
                        
                        IssuedReceipts::create([
                            'print_id' => $printId,
                            'receipt_number' => $receiptNumber,
                            'trxn_id' => $trxn->trxn_id,
                            'powas_id' => $powasId,
                            'transaction_date' => $transactionDate,
                            'is_printed' => 'NO',
                            'print_count' => 0,
                        ]);
                    }
                    
                    $desc = substr($trxn->description, 0, 40);
                    $this->line("  ✓ Created receipt for: {$desc} (Trxn: {$trxn->trxn_id})");
                    $createdCount++;
                }

            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
                $errorCount++;
            }

            $this->newLine();
        }

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('                    SUMMARY');
        $this->info('═══════════════════════════════════════════════════════');
        $this->line("Mode: " . ($dryRun ? 'DRY RUN' : 'LIVE'));
        $this->line("Receipts created: {$createdCount}");
        $this->line("Errors: {$errorCount}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  This was a DRY RUN. Run without --dry-run to apply changes.');
        } else {
            $this->info('✓ Receipt fix completed!');
        }

        return 0;
    }
}
