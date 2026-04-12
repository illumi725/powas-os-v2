<?php
/**
 * Application Approval Simulation (Debug Mode)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Mode</h1>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";

$possiblePaths = [
    $_SERVER['DOCUMENT_ROOT'],
    $_SERVER['DOCUMENT_ROOT'] . '/powas-os',
    __DIR__,
    dirname(__DIR__),
    dirname(__DIR__) . '/powas-os'
];

echo "<h3>Scanning Paths:</h3><ul>";
$laravelPath = null;

foreach ($possiblePaths as $path) {
    echo "<li>Checking <b>$path</b>... ";
    if (file_exists($path . '/vendor/autoload.php')) {
        echo "<span style='color:green'>FOUND!</span></li>";
        $laravelPath = $path;
        break;
    } else {
        echo "<span style='color:red'>Not found</span></li>";
    }
}
echo "</ul>";

if (!$laravelPath) {
    die("❌ could not find Laravel root! Check the paths above.");
}

require $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<h3>✅ Laravel Loaded from $laravelPath</h3>";

// Minimal Simulation
try {
    \DB::beginTransaction();
    echo "DB Transaction Started (Rollback enabled)<br>";
    
    $mode = $_GET['mode'] ?? 'regular';
    echo "Mode: <b>" . strtoupper($mode) . "</b><br><br>";
    
    // Just output accounts to verify connection
    $accounts = \App\Models\ChartOfAccounts::limit(5)->get();
    echo "Connected to Database! Found " . $accounts->count() . " accounts.<br>";
    
    \DB::rollBack();
    echo "<br><i>Rolled back successfully.</i>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
