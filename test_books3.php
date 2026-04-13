<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new App\Livewire\Accounting\BooksOfAccounts();
$powasID = App\Models\Transactions::first()->powas_id;
$comp->powasID = $powasID; 
$comp->dateFrom = '2026-04-01';
$comp->dateTo = '2026-04-30';

$comp->selectedBook = 'general_ledger';
$comp->fetchEntries();

$cash_found = false;
$app_fee_found = false;

foreach($comp->entries as $entry) {
    if ($entry['type'] === 'header') {
        echo "Found Header: " . $entry['account'] . "\n";
        if (strpos($entry['account'], '101') !== false) $cash_found = true;
        if (strpos($entry['account'], '401') !== false) $app_fee_found = true;
    }
}
echo "Cash on Hand (101) Found: " . ($cash_found ? 'YES' : 'NO') . "\n";
echo "Application Fee (401) Found: " . ($app_fee_found ? 'YES' : 'NO') . "\n";
