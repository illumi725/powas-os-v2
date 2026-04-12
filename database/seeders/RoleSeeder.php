<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            // 'barangay-coordinator',
            'president',
            'vice-president',
            'secretary',
            'treasurer',
            'auditor',
            'collector-reader',
            'board',
            'member',
        ];

        foreach ($roles as $roleName) {
            $role = Role::create(['name' => $roleName]);

            switch ($roleName) {
                case 'admin':
                    // case 'barangay-coordinator':
                    $permissions = Permission::all();
                    break;

                case 'president':
                case 'vice-president':
                case 'board':
                    $permissions = [
                        'verify application',
                        'reject application',
                        'view members list',
                        'view member profile',
                        'view reports',
                        'view powas records',
                        'view transactions',
                    ];
                    break;
                case 'secretary':
                    $permissions = [
                        'verify application',
                        'reject application',
                        'view members list',
                        'view member profile',
                        'view reports',
                        'add member',
                        'edit member',
                        'view powas records',
                        'view transactions',
                    ];
                    break;

                case 'treasurer':
                    $permissions = [
                        'approve application',
                        'reject application',
                        'view members list',
                        'view member profile',
                        'edit member profile',
                        'add reading',
                        'edit reading',
                        'create billing',
                        'edit billing',
                        'view powas records',
                        'create bill payment',
                        'edit bill payment',
                        'view transactions',
                        'create transaction',
                        'edit transaction',
                        'view reports',
                        'edit powas preferences',
                        'add member',
                        'edit member',
                    ];
                    break;

                case 'auditor':
                    $permissions = [
                        'view members list',
                        'view member profile',
                        'view reports',
                        'view logs',
                        'view powas records',
                        'view transactions',
                    ];
                    break;

                case 'collector-reader':
                    $permissions = [
                        'view members list',
                        'add reading',
                        'edit reading',
                        'create bill payment',
                        'edit bill payment',
                        'view powas records',
                    ];
                    break;

                default:
                    $permissions = [
                        'view member reading',
                        'view member billing',
                        'view member payment',
                    ];
                    break;
            }

            $role->givePermissionTo($permissions);
        }
    }
}
