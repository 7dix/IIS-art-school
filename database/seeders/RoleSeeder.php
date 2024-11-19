<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'user',
                'permissions' => 0b0001,
            ],
            [
                'name' => 'teacher',
                'permissions' => 0b0011,
            ],
            [
                'name' => 'manager',
                'permissions' => 0b0111,
            ],
            [
                'name' => 'admin',
                'permissions' => 0b1111,
            ]
        ];

        Role::insert($roles);
    }
}
