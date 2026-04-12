<?php

namespace App\Console\Commands;

use App\Factory\CustomNumberFactory;
use App\Models\Billings;
use App\Models\ChartOfAccounts;
use App\Models\PowasMembers;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMissingBillReceivables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-missing-bill-receivables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing Bills Receivable debit transactions for bills that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding bills without Bills Receivable transactions...');

        // Find bills that don't have a corresponding Bills Receivable DEBIT transaction
        $billsWithoutTrxn = Billings::whereNotIn('billing_id', function($query) {
            $query->select('paid_to')
                ->from('transactions')
                ->where('account_number', 105)
                ->where('transaction_side', 'DEBIT');
        })->get();

        $this->info("Found {$billsWithoutTrxn->count()} bills without Bills Receivable transactions.");
        $totalAmount = $billsWithoutTrxn->sum('billing_amount');
        $this->info("Total amount: {$totalAmount}");

        if ($billsWithoutTrxn->isEmpty()) {
            $this->info('All bills have Bills Receivable transactions!');
            return;
        }

        $createdCount = 0;
        $billsReceivablesAccount = ChartOfAccounts::where('account_number', 105)->first();
        $revenuesAccount = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%REVENUE%')->first();

        if (!$billsReceivablesAccount || !$revenuesAccount) {
            $this->error('Could not find required accounts (105: Bills Receivables or Revenue account).');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($billsWithoutTrxn as $billing) {
                $member = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
                    ->where('powas_members.member_id', $billing->member_id)
                    ->first();

                if (!$member) {
                    $this->warn("  Skipping Bill {$billing->billing_id}: Member not found");
                    continue;
                }

                $memberName = strtoupper($member->lastname . ', ' . $member->firstname);
                $journalEntryNumber = CustomNumberFactory::journalEntryNumber($billing->powas_id, $billing->cut_off_end ?? Carbon::now()->format('Y-m-d'));

                // Create Bills Receivable DEBIT transaction
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $billsReceivablesAccount->account_number,
                    'description' => "Bills Receivable generated for {$memberName}",
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $billing->billing_amount,
                    'transaction_side' => $billsReceivablesAccount->normal_balance,
                    'received_from' => $memberName,
                    'paid_to' => $billing->billing_id,
                    'member_id' => $billing->member_id,
                    'powas_id' => $billing->powas_id,
                    'recorded_by_id' => $billing->recorded_by,
                    'transaction_date' => $billing->cut_off_end ?? Carbon::now()->format('Y-m-d'),
                ]);

                // Create Revenue CREDIT transaction
                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $revenuesAccount->account_number,
                    'description' => "Revenue from {$memberName}",
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $billing->billing_amount,
                    'transaction_side' => $revenuesAccount->normal_balance,
                    'received_from' => $memberName,
                    'paid_to' => $billing->billing_id,
                    'member_id' => $billing->member_id,
                    'powas_id' => $billing->powas_id,
                    'recorded_by_id' => $billing->recorded_by,
                    'transaction_date' => $billing->cut_off_end ?? Carbon::now()->format('Y-m-d'),
                ]);

                $this->line("  Created transactions for Bill {$billing->billing_id}: {$billing->billing_amount}");
                $createdCount++;
            }

            DB::commit();
            $this->info("Created Bills Receivable transactions for {$createdCount} bills.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }
}
