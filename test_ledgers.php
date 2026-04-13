<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$powasID = App\Models\Transactions::first()->powas_id;
echo "POWAS ID: $powasID\n";

$disbursements = App\Models\Transactions::where('powas_id', $powasID)
    ->where('account_number', '101')
    ->where('transaction_side', 'CREDIT')
    ->count();
    
echo "Cash Disbursements Count for 101: $disbursements\n";

$all_cash_credits = App\Models\Transactions::where('powas_id', $powasID)
    ->where('transaction_side', 'CREDIT')
    ->join('powas_chart_of_accounts', 'powas_transactions.account_number', '=', 'powas_chart_of_accounts.account_number')
    ->where('powas_chart_of_accounts.account_name', 'LIKE', '%CASH%')
    ->get(['powas_transactions.account_number', 'powas_chart_of_accounts.account_name', 'transaction_date']);
    
echo "All Cash Credits: " . count($all_cash_credits) . "\n";
if (count($all_cash_credits) > 0) {
    echo "Sample Cash Credit Acct: " . $all_cash_credits[0]->account_number . " - " . $all_cash_credits[0]->account_name . " on " . $all_cash_credits[0]->transaction_date . "\n";
}

$start = '2020-01-01';
$end = '2026-12-31';
$jeNumbers = App\Models\Transactions::where('powas_id', $powasID)
    ->where('account_number', '101') // Wait, what if Cash is NOT 101?
    ->where('transaction_side', 'CREDIT')
    ->whereBetween('transaction_date', [$start, $end])
    ->pluck('journal_entry_number');

echo "JE Numbers for Cash Credits: " . count($jeNumbers) . "\n";

$cash_transactions = App\Models\Transactions::where('powas_id', $powasID)
    ->where('account_number', '101')
    ->count();
echo "Total transactions for account 101: $cash_transactions\n";

