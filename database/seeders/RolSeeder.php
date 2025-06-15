<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'create_permission',
            'edit_permission',
            'delete_permission',
            'view_permission',
            'create_user',
            'edit_user',
            'delete_user',
            'view_user',
        ]);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([
            'view_permission',
            'view_user',
        ]);
    }
}
