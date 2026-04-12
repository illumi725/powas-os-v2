<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function showUserInfo()
    {
        if (!Auth::check()) {
            return "Not logged in";
        }

        $user = Auth::user();
        
        $info = [
            'User ID' => $user->user_id ?? 'N/A',
            'Username' => $user->username ?? 'N/A',
            'Role (raw)' => $user->role ?? 'N/A',
            'Role (lowercase)' => strtolower($user->role ?? ''),
            'Role (uppercase)' => strtoupper($user->role ?? ''),
            'Role Comparison' => [
                'Equals "admin"' => ($user->role === 'admin') ? 'TRUE' : 'FALSE',
                'Equals "ADMIN"' => ($user->role === 'ADMIN') ? 'TRUE' : 'FALSE',
                'Lowercase equals "admin"' => (strtolower($user->role) === 'admin') ? 'TRUE' : 'FALSE',
            ],
        ];

        echo "<h1>User Debug Information</h1>";
        echo "<table border='1' cellpadding='10'>";
        foreach ($info as $key => $value) {
            if (is_array($value)) {
                echo "<tr><td><strong>{$key}</strong></td><td>";
                foreach ($value as $k => $v) {
                    echo "{$k}: <strong>{$v}</strong><br>";
                }
                echo "</td></tr>";
            } else {
                echo "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
            }
        }
        echo "</table>";
        
        echo "<hr>";
        echo "<h2>Full User Object:</h2>";
        echo "<pre>";
        print_r($user->toArray());
        echo "</pre>";
    }
}
