<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Role
        $user = Role::create(['name' => 'user']);
        $admin = Role::create(['name' => 'admin']);
       
        //Create permissions
        $permissions = ['create_atelier', 'create_type', 'create_equipment'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        //edit permissions
        $permissions = ['edit_atelier', 'edit_type', 'edit_equipment', 'assign_teacher', 'edit_user'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        //manage permissions
        $permissions = ['manage_atelier', 'manage_type', 'manage_equipment', 'manage_user'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin->givePermissionTo(Permission::all());
        $user->givePermissionTo(['']);

    }
}
