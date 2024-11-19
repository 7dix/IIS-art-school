<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'admin',
                'last_name' => 'bigboss',
                'email' => 'admin@IIS.com',
                'password' => bcrypt('admin'),
                'role_id' => Role::where('name', 'admin')->first()->id,
            ],
            [
                'first_name' => 'teacher',
                'last_name' => 'lilbro',
                'email' => 'teacher@IIS.com',
                'password' => bcrypt('teacher'),
                'role_id' => Role::where('name', 'teacher')->first()->id,
            ],
            [
                'first_name' => 'manager',
                'last_name' => 'lilbro',
                'email' => 'manager@IIS.com',
                'password' => bcrypt('manager'),
                'role_id' => Role::where('name', 'manager')->first()->id,
            ],
            [
                'first_name' => 'user',
                'last_name' => 'lilbro',
                'email' => 'user@IIS.com',
                'password' => bcrypt('user'),
                'role_id' => Role::where('name', 'user')->first()->id,
            ],

        ];

        User::insert($users);
    }
}
