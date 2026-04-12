<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChartOfAccounts;

$accounts = ChartOfAccounts::where('account_name', 'LIKE', '%Balance%')
    ->orWhere('account_name', 'LIKE', '%Adjustment%')
    ->orWhere('account_name', 'LIKE', '%Suspense%')
    ->orWhere('account_name', 'LIKE', '%System%')
    ->orWhere('account_name', 'LIKE', '%Miscellaneous%')
    ->get();

foreach ($accounts as $a) {
    echo "{$a->account_number} - {$a->account_name} ({$a->account_type})\n";
}
