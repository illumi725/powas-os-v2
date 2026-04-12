<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 'update transaction' permission already exists as 'edit transaction'
        // Just ensure treasurer role has it
        $permission = Permission::firstOrCreate(['name' => 'update transaction']);
        $role = Role::where('name', 'treasurer')->first();
        
        if ($role && !$role->hasPermissionTo('update transaction')) {
            $role->givePermissionTo('update transaction');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'update transaction')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
