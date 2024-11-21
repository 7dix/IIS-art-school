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
       
        //Admin permissions
        $permissions = ['manage_atelier'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        //Teacher permissions
        $permissions = ['manage_equipment', 'assign_students', 'manage_reservation', 'restrict_equipment'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        //Manager permissions
        $permissions = ['manage_type', 'assign_teacher']; //'assign_students'
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin->givePermissionTo(Permission::all());
        $user->givePermissionTo(['']);

    }
}
