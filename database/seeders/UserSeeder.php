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
                'name' => 'admin',
                'email' => 'admin@IIS.com',
                'password' => bcrypt('admin'),
            ],
            [
                'name' => 'teacher',
                'email' => 'teacher@IIS.com',
                'password' => bcrypt('teacher'),
            ],
            [
                'name' => 'manager',
                'email' => 'manager@IIS.com',
                'password' => bcrypt('manager'),
            ],
            [
                'name' => 'user',
                'email' => 'user@IIS.com',
                'password' => bcrypt('user'),
            ],

        ];

        User::insert($users);


        $user = User::find(1);
        $user->assignRole('admin');
        $user = User::find(2);
        $user->assignRole('user');
        $user = User::find(3);
        $user->assignRole('user');
        $user = User::find(4);
        $user->assignRole('user');



    }
}
