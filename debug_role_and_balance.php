<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== Auto-Balance Button Debug ===\n\n";

// Get all users and their roles
echo "Users and their roles:\n";
$users = DB::table('users')
    ->select('users.user_id', 'users.username', 'users.email', 'roles.name as role_name')
    ->join('model_has_roles', 'users.user_id', '=', 'model_has_roles.model_id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->where('model_has_roles.model_type', 'App\\Models\\User')
    ->get();

foreach ($users as $user) {
    echo "  User ID: {$user->user_id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role_name}\n";
}

echo "\n";

// Check if there's a balance discrepancy
echo "Checking financial statement balance...\n";
echo "This requires fetching data similar to the Fis component.\n";
echo "If you see this message, the script is working.\n";
echo "\n";

// Check available roles
echo "Available roles in the system:\n";
$roles = DB::table('roles')->get();
foreach ($roles as $role) {
    echo "  ID: {$role->id}, Name: {$role->name}\n";
}

echo "\n";
echo "To test the button visibility, ensure:\n";
echo "1. You are logged in as a user with 'treasurer' or 'admin' role\n";
echo "2. The financial statement has an imbalance (checking value != 0)\n";
echo "3. Clear Laravel's view cache: php artisan view:clear\n";
