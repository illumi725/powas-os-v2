<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new App\Livewire\Accounting\BooksOfAccounts();
$powasID = App\Models\Transactions::first()->powas_id;
$comp->powasID = $powasID; 
$comp->dateFrom = '2020-01-01';
$comp->dateTo = '2026-12-31';

$comp->selectedBook = 'cash_disbursements';
$comp->fetchEntries();
echo "cash_disbursements count: " . count($comp->entries) . "\n";
if (count($comp->entries) > 0) {
    echo "First JE: " . $comp->entries[0]['journal_entry_number'] . "\n";
}

$comp->selectedBook = 'general_ledger';
$comp->fetchEntries();
echo "general_ledger count: " . count($comp->entries) . "\n";
$cash_found = false;
foreach($comp->entries as $entry) {
    if ($entry['type'] === 'header' && strpos($entry['account'], '101') !== false) {
        $cash_found = true;
        echo "Found Cash on Hand in GL: " . $entry['account'] . "\n";
        break;
    }
}
if (!$cash_found) echo "Cash on Hand (101) NOT FOUND in GL headers!\n";
