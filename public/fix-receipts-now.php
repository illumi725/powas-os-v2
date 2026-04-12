<?php
/**
 * Receipt Fixer - Auto-detect Laravel path
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
echo "Finding Laravel installation...\n\n";

// Try different possible paths
$possiblePaths = [
    __DIR__ . '/powas-os',
    __DIR__ . '/../powas-os',
    __DIR__ . '/laravel',
    __DIR__,
];

$laravelPath = null;

foreach ($possiblePaths as $path) {
    echo "Checking: {$path}\n";
    if (file_exists($path . '/vendor/autoload.php')) {
        $laravelPath = $path;
        echo "✓ FOUND!\n\n";
        break;
    }
}

if (!$laravelPath) {
    die("\n❌ Could not find Laravel installation!\n\nManual check needed:\n1. Where is vendor/autoload.php?\n2. Where is bootstrap/app.php?\n</pre>");
}

try {
    require $laravelPath . '/vendor/autoload.php';
    $app = require_once $laravelPath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    echo "Laravel loaded from: {$laravelPath}\n\n";
    
    // Find missing receipts
    $missing = \DB::table('transactions as t')
        ->leftJoin('issued_receipts as ir', 't.trxn_id', '=', 'ir.trxn_id')
        ->where(function($q) {
            $q->where('t.description', 'LIKE', '%Application Fee%')
              ->orWhere('t.description', 'LIKE', '%Membership Fee%');
        })
        ->where('t.transaction_side', 'CREDIT')
        ->whereNull('ir.trxn_id')
        ->select('t.*')
        ->get();

    echo "Found {$missing->count()} missing receipts\n\n";

    if ($missing->count() == 0) {
        echo "✓ Nothing to fix!\n</pre>";
        exit;
    }

    $grouped = $missing->groupBy('journal_entry_number');
    $created = 0;

    foreach ($grouped as $je => $trans) {
        $first = $trans->first();
        
        $receiptNum = \App\Factory\CustomNumberFactory::receipt(
            $first->powas_id, 
            $first->transaction_date
        );
        
        echo "JE {$je} → {$receiptNum}: ";
        
        foreach ($trans as $t) {
            \App\Models\IssuedReceipts::create([
                'print_id' => \App\Factory\CustomNumberFactory::getRandomID(),
                'receipt_number' => $receiptNum,
                'trxn_id' => $t->trxn_id,
                'powas_id' => $first->powas_id,
                'transaction_date' => $first->transaction_date,
                'is_printed' => 'NO',
                'print_count' => 0,
            ]);
            $created++;
        }
        echo "{$trans->count()} txns\n";
    }

    echo "\n✓ Created {$created} receipts!\n";
    echo "\n🗑️  DELETE THIS FILE NOW!\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR:\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "</pre>";
