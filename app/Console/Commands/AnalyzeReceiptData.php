<?php

namespace App\Console\Commands;

use App\Models\IssuedReceipts;
use App\Models\Transactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeReceiptData extends Command
{
    protected $signature = 'receipts:analyze';
    protected $description = 'Analyze receipt database structure and identify issues';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('         RECEIPT DATABASE ANALYSIS REPORT');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // 1. Total issued receipts
        $totalReceipts = IssuedReceipts::count();
        $this->info("📊 Total Issued Receipts: {$totalReceipts}");
        $this->newLine();

        // 2. Receipts with duplicate receipt numbers
        $duplicateReceiptNumbers = DB::table('issued_receipts')
            ->select('receipt_number', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(trxn_id) as trxn_ids'))
            ->groupBy('receipt_number')
            ->having('count', '>', 1)
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $this->info("🔍 Duplicate Receipt Numbers (Top 10):");
        if ($duplicateReceiptNumbers->count() > 0) {
            $this->table(
                ['Receipt Number', 'Count', 'Transaction IDs'],
                $duplicateReceiptNumbers->map(fn($r) => [
                    $r->receipt_number,
                    $r->count,
                    substr($r->trxn_ids, 0, 50) . '...'
                ])
            );
        } else {
            $this->line("   ✓ No duplicate receipt numbers found");
        }
        $this->newLine();

        // 3. Recent Application/Membership Fee transactions
        $this->info("💰 Recent Application/Membership Fee Transactions:");
        $feeTransactions = Transactions::where(function($q) {
                $q->where('description', 'LIKE', '%Application Fee%')
                  ->orWhere('description', 'LIKE', '%Membership Fee%');
            })
            ->with('printedreceipt')
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        $feeData = $feeTransactions->map(function($t) {
            $receipts = $t->printedreceipt;
            $receiptNum = $receipts->count() > 0 ? $receipts->first()->receipt_number : 'NO RECEIPT';
            return [
                substr($t->trxn_id, 0, 8),
                $t->journal_entry_number,
                substr($t->description, 0, 30),
                $t->account_number,
                number_format($t->amount, 2),
                $receiptNum
            ];
        });

        $this->table(
            ['TrxnID', 'JE#', 'Description', 'Acct#', 'Amount', 'Receipt#'],
            $feeData
        );
        $this->newLine();

        // 4. Transactions WITHOUT receipts (Application/Membership fees)
        $missingReceipts = DB::table('transactions as t')
            ->leftJoin('issued_receipts as ir', 't.trxn_id', '=', 'ir.trxn_id')
            ->where(function($q) {
                $q->where('t.description', 'LIKE', '%Application Fee%')
                  ->orWhere('t.description', 'LIKE', '%Membership Fee%');
            })
            ->where('t.transaction_side', 'CREDIT') // Revenue side
            ->whereNull('ir.trxn_id')
            ->count();

        $this->warn("⚠️  Application/Membership Fee transactions missing receipts: {$missingReceipts}");
        $this->newLine();

        // 5. Check journal entries with mixed receipt numbers
        $this->info("🔗 Journal Entries with Multiple Receipt Numbers:");
        $mixedReceipts = DB::table('transactions as t')
            ->join('issued_receipts as ir', 't.trxn_id', '=', 'ir.trxn_id')
            ->select('t.journal_entry_number', DB::raw('COUNT(DISTINCT ir.receipt_number) as receipt_count'), DB::raw('GROUP_CONCAT(DISTINCT ir.receipt_number) as receipts'))
            ->groupBy('t.journal_entry_number')
            ->having('receipt_count', '>', 1)
            ->limit(10)
            ->get();

        if ($mixedReceipts->count() > 0) {
            $this->table(
                ['Journal Entry #', 'Receipt Count', 'Receipt Numbers'],
                $mixedReceipts->map(fn($r) => [
                    $r->journal_entry_number,
                    $r->receipt_count,
                    $r->receipts
                ])
            );
        } else {
            $this->line("   ✓ No journal entries with multiple receipt numbers");
        }
        $this->newLine();

        // 6. Sample of proper receipt structure
        $this->info("✅ Sample Proper Receipt Structure (Latest 5):");
        $properReceipts = IssuedReceipts::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $sampleData = $properReceipts->map(function($r) {
            // Manually fetch transaction if needed
            $transaction = Transactions::find($r->trxn_id);
            
            return [
                substr($r->receipt_number, 0, 15),
                substr($r->trxn_id, 0, 8),
                $transaction ? substr($transaction->journal_entry_number, 0, 10) : 'N/A',
                $transaction ? substr($transaction->description, 0, 25) : 'N/A',
                $r->transaction_date,
                $r->is_printed ?? 'NO'
            ];
        });

        $this->table(
            ['Receipt#', 'TrxnID', 'JE#', 'Description', 'Date', 'Printed'],
            $sampleData
        );

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('                    END OF REPORT');
        $this->info('═══════════════════════════════════════════════════════');

        return 0;
    }
}
