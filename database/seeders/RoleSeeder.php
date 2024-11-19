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
        $teacher = Role::create(['name' => 'teacher']);
        $manager = Role::create(['name' => 'manager']);
       
        //Create permissions
        $permissions = ['create_atelier', 'create_type', 'create_equipment'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

         //edit permissions
         $permissions = ['edit_atelier', 'edit_type', 'edit_equipment', 'assign_teacher'];
         foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
         }

        $admin->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['create_type', 'edit_type']);
        $teacher->givePermissionTo(['create_equipment', 'edit_equipment']);
        // $user->givePermissionTo(['']);


    }
}
