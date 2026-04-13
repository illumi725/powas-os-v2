<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new App\Livewire\Accounting\BooksOfAccounts();
$comp->powasID = 'NEC-SJC-PIN-004'; // Pick one
$comp->dateFrom = '2020-01-01';
$comp->dateTo = '2026-12-31';

$comp->selectedBook = 'cash_receipts';
try {
    $comp->fetchEntries();
    echo "cash_receipts OK: " . count($comp->entries) . " entries\n";
} catch (\Exception $e) {
    echo "EX cash_receipts: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}

$comp->selectedBook = 'general_ledger';
try {
    $comp->fetchEntries();
    echo "general_ledger OK: " . count($comp->entries) . " entries\n";
} catch (\Exception $e) {
    echo "EX general_ledger: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
