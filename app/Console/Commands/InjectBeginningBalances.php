<?php

namespace App\Console\Commands;

use App\Factory\CustomNumberFactory;
use App\Models\ChartOfAccounts;
use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InjectBeginningBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inject-beginning-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inject beginning balances from JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting beginning balance injection...');

        // Hardcoded path based on exploration
        $jsonPath = storage_path('app/beginning_balances/NEC-SJC-PIN-004.json');
        
        if (!File::exists($jsonPath)) {
            $this->error("JSON file not found at: $jsonPath");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        
        // The JSON structure is {"2023-12-31": {"101": "...", ...}}
        // We take the first date key
        $dateKey = array_key_first($data);
        $balances = $data[$dateKey];

        $this->info("Processing balances for date: $dateKey");

        // Use a slight modification to the date to ensure it appears as "Beginning Of Time" relative to 2024 operations?
        // JSON key is 2023-12-31. We should probably use that date.
        $transactionDate = $dateKey;
        $powasId = 'NEC-SJC-PIN-004'; // Hardcoded based on filename/context
        // Or fetch from DB? There is only one active usually in these single-tenant contexts or distinct by path. 
        // Let's assume NEC-SJC-PIN-004.
        
        // Fetch valid accounts
        $accounts = ChartOfAccounts::all()->keyBy('account_number');

        $injectedCount = 0;
        $skippedCount = 0;
        
        DB::beginTransaction();

        try {
            foreach ($balances as $accNum => $balanceVal) {
                // Determine account
                $account = $accounts->get($accNum);
                if (!$account) {
                    $this->warn("Account $accNum not found in ChartOfAccounts. Skipping.");
                    continue;
                }

                $amount = floatval($balanceVal);
                if ($amount == 0) {
                    continue;
                }

                // Check if already exists
                $exists = Transactions::where('account_number', $accNum)
                    ->where('description', 'Beginning Balance')
                    ->where('transaction_date', $transactionDate)
                    ->exists();
                
                if ($exists) {
                    $this->info("Beginning balance for $accNum already exists. Skipping.");
                    $skippedCount++;
                    continue;
                }

                // Identify side
                $side = $account->normal_balance; // Default to normal
                
                // If amount is negative, flip the side
                // E.g. Asset (Normal Debit). JSON = -100.
                // Means Credit 100.
                
                $finalAmount = abs($amount);
                
                if ($amount < 0) {
                    if ($side == 'DEBIT') $side = 'CREDIT';
                    else $side = 'DEBIT';
                }

                $journalEntry = CustomNumberFactory::journalEntryNumber($powasId, $transactionDate);

                // Create Transaction
                // We need a dummy user ID for `recorded_by_id` if allowed, or pick the first admin?
                // Let's try finding a user or hardcode 17779 (seen in previous dumps) or similar.
                $user = DB::table('users')->first();
                $userId = $user ? $user->user_id : 1; 

                Transactions::create([
                    'trxn_id' => CustomNumberFactory::getRandomID(),
                    'account_number' => $accNum,
                    'description' => 'Beginning Balance',
                    'journal_entry_number' => $journalEntry,
                    'amount' => $finalAmount,
                    'transaction_side' => $side,
                    'received_from' => 'System Initialization',
                    'paid_to' => null, // Not paid to a bill
                    'member_id' => null,
                    'powas_id' => $powasId,
                    'recorded_by_id' => $userId,
                    'transaction_date' => $transactionDate,
                ]);

                $this->line("Injected $side $finalAmount for Account $accNum ($account->account_name)");
                $injectedCount++;
            }
            
            DB::commit();
            $this->info("Injection complete. Injected: $injectedCount, Skipped: $skippedCount");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error injecting balances: " . $e->getMessage());
        }
    }
}
