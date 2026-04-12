<?php
/**
 * Spatie Permission Diagnostic Script
 * Place this in public/ folder and access via browser
 * DELETE after troubleshooting for security!
 */

// Prevent direct access in production - uncomment this line after testing
// die('This diagnostic is disabled. Delete this file for security.');

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$app->make(Illuminate\Contracts\Console\Kernel::class);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "<h1>Spatie Permission Diagnostics</h1>";
echo "<style>
    body { font-family: monospace; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    table { border-collapse: collapse; margin: 10px 0; }
    td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// 1. Check Config
echo "<h2>1. Configuration Check</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$defaultGuard = Config::get('auth.defaults.guard');
echo "<tr><td>Default Auth Guard</td><td>$defaultGuard</td><td class='" . ($defaultGuard === 'web' ? 'success' : 'error') . "'>" . ($defaultGuard === 'web' ? '✓' : '✗') . "</td></tr>";

$guards = Config::get('permission.guards', []);
echo "<tr><td>Permission Guards</td><td>" . implode(', ', $guards) . "</td><td class='" . (in_array('web', $guards) ? 'success' : 'error') . "'>" . (in_array('web', $guards) ? '✓' : '✗') . "</td></tr>";

$cacheStore = Config::get('permission.cache.store', 'default');
echo "<tr><td>Cache Store</td><td>$cacheStore</td><td class='success'>✓</td></tr>";

$cacheKey = Config::get('permission.cache.key');
echo "<tr><td>Cache Key</td><td>$cacheKey</td><td class='success'>✓</td></tr>";

echo "</table>";

// 2. Check Database Tables
echo "<h2>2. Database Tables Check</h2>";
echo "<table>";
echo "<tr><th>Table</th><th>Rows</th><th>Status</th></tr>";

try {
    $roleCount = DB::table('roles')->count();
    echo "<tr><td>roles</td><td>$roleCount</td><td class='" . ($roleCount > 0 ? 'success' : 'warning') . "'>" . ($roleCount > 0 ? '✓' : '⚠') . "</td></tr>";
    
    $permissionCount = DB::table('permissions')->count();
    echo "<tr><td>permissions</td><td>$permissionCount</td><td class='success'>✓</td></tr>";
    
    $modelHasRolesCount = DB::table('model_has_roles')->count();
    echo "<tr><td>model_has_roles</td><td>$modelHasRolesCount</td><td class='" . ($modelHasRolesCount > 0 ? 'success' : 'warning') . "'>" . ($modelHasRolesCount > 0 ? '✓' : '⚠') . "</td></tr>";
} catch (Exception $e) {
    echo "<tr><td colspan='3' class='error'>Database Error: " . $e->getMessage() . "</td></tr>";
}

echo "</table>";

// 3. Check Roles
echo "<h2>3. Roles in Database</h2>";
echo "<table>";
echo "<tr><th>ID</th><th>Name</th><th>Guard Name</th><th>Status</th></tr>";

try {
    $roles = DB::table('roles')->get();
    foreach ($roles as $role) {
        $status = $role->guard_name === 'web' ? 'success' : 'error';
        $icon = $role->guard_name === 'web' ? '✓' : '✗';
        echo "<tr><td>$role->id</td><td>$role->name</td><td>$role->guard_name</td><td class='$status'>$icon</td></tr>";
    }
} catch (Exception $e) {
    echo "<tr><td colspan='4' class='error'>Error: " . $e->getMessage() . "</td></tr>";
}

echo "</table>";

// 4. Check Current User (if logged in)
echo "<h2>4. Current User Check</h2>";

if (Auth::check()) {
    $user = Auth::user();
    echo "<table>";
    echo "<tr><th>Property</th><th>Value</th></tr>";
    echo "<tr><td>User ID</td><td>" . ($user->user_id ?? 'N/A') . "</td></tr>";
    echo "<tr><td>Username</td><td>" . ($user->username ?? 'N/A') . "</td></tr>";
    echo "<tr><td>Guard Name (Model)</td><td>" . ($user->guard_name ?? 'NOT SET') . "</td></tr>";
    echo "<tr><td>Primary Key Name</td><td>" . $user->getKeyName() . "</td></tr>";
    
    try {
        $roleNames = $user->getRoleNames()->toArray();
        echo "<tr><td>Assigned Roles</td><td>" . implode(', ', $roleNames) . "</td></tr>";
        
        // Test specific role checks
        echo "<tr><td>Has 'admin' role</td><td>" . ($user->hasRole('admin') ? 'YES' : 'NO') . "</td></tr>";
        echo "<tr><td>Has 'member' role</td><td>" . ($user->hasRole('member') ? 'YES' : 'NO') . "</td></tr>";
        
    } catch (Exception $e) {
        echo "<tr><td colspan='2' class='error'>Role Check Error: " . $e->getMessage() . "</td></tr>";
    }
    
    echo "</table>";
} else {
    echo "<p class='warning'>⚠ No user is currently logged in. Please log in and refresh this page.</p>";
}

// 5. Check Model Has Roles (sample)
echo "<h2>5. Sample User-Role Assignments</h2>";
echo "<table>";
echo "<tr><th>Model Type</th><th>Model ID</th><th>Role ID</th></tr>";

try {
    $assignments = DB::table('model_has_roles')
        ->where('model_type', 'App\Models\User')
        ->limit(10)
        ->get();
    
    foreach ($assignments as $assignment) {
        echo "<tr><td>$assignment->model_type</td><td>$assignment->model_id</td><td>$assignment->role_id</td></tr>";
    }
    
    if (count($assignments) === 0) {
        echo "<tr><td colspan='3' class='warning'>No role assignments found!</td></tr>";
    }
} catch (Exception $e) {
    echo "<tr><td colspan='3' class='error'>Error: " . $e->getMessage() . "</td></tr>";
}

echo "</table>";

// 6. Cache Info
echo "<h2>6. Cache Information</h2>";
echo "<table>";
echo "<tr><th>Function</th><th>Status</th></tr>";

echo "<tr><td>OPcache Enabled</td><td>" . (function_exists('opcache_get_status') && opcache_get_status() ? 'YES' : 'NO') . "</td></tr>";
echo "<tr><td>APCu Enabled</td><td>" . (function_exists('apcu_cache_info') ? 'YES' : 'NO') . "</td></tr>";

try {
    $cacheWorking = Cache::has('test_key') || true;
    Cache::put('test_key', 'test_value', 60);
    echo "<tr><td>Laravel Cache Working</td><td class='success'>YES</td></tr>";
    Cache::forget('test_key');
} catch (Exception $e) {
    echo "<tr><td>Laravel Cache Working</td><td class='error'>ERROR: " . $e->getMessage() . "</td></tr>";
}

echo "</table>";

// 7. Environment Info
echo "<h2>7. Environment Information</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Laravel Version</td><td>" . app()->version() . "</td></tr>";
echo "<tr><td>Environment</td><td>" . config('app.env') . "</td></tr>";
echo "<tr><td>Debug Mode</td><td>" . (config('app.debug') ? 'ON' : 'OFF') . "</td></tr>";
echo "</table>";

echo "<hr>";
echo "<h2>⚠️ SECURITY WARNING ⚠️</h2>";
echo "<p style='color: red; font-weight: bold;'>DELETE THIS FILE (diagnose-permission.php) IMMEDIATELY AFTER TROUBLESHOOTING!</p>";
echo "<p>This file exposes sensitive information about your application.</p>";
?>
