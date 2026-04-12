<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'add powas']);
        Permission::create(['name' => 'delete powas']);
        Permission::create(['name' => 'edit powas location']);
        Permission::create(['name' => 'edit powas preferences']);
        Permission::create(['name' => 'edit powas officer']);
        Permission::create(['name' => 'edit powas']);
        Permission::create(['name' => 'view powas records']);

        Permission::create(['name' => 'add member']);
        Permission::create(['name' => 'edit member']);
        Permission::create(['name' => 'delete member']);

        Permission::create(['name' => 'add user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view users list']);
        Permission::create(['name' => 'assign roles']);
        Permission::create(['name' => 'assign permissions']);
        Permission::create(['name' => 'reset user password']);

        Permission::create(['name' => 'verify application']);
        Permission::create(['name' => 'approve application']);
        Permission::create(['name' => 'reject application']);

        Permission::create(['name' => 'view members list']);

        Permission::create(['name' => 'view member profile']);
        Permission::create(['name' => 'edit member profile']);
        Permission::create(['name' => 'view member reading']);
        Permission::create(['name' => 'view member billing']);
        Permission::create(['name' => 'view member payment']);

        Permission::create(['name' => 'add chart of account']);
        Permission::create(['name' => 'edit chart of account']);

        Permission::create(['name' => 'add reading']);
        Permission::create(['name' => 'edit reading']);

        Permission::create(['name' => 'create billing']);
        Permission::create(['name' => 'edit billing']);

        Permission::create(['name' => 'create bill payment']);
        Permission::create(['name' => 'edit bill payment']);

        Permission::create(['name' => 'view transactions']);
        Permission::create(['name' => 'create transaction']);
        Permission::create(['name' => 'edit transaction']);

        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'view logs']);
    }
}
