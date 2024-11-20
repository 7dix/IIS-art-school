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
            ],
            [
                'first_name' => 'teacher',
                'last_name' => 'lilbro',
                'email' => 'teacher@IIS.com',
                'password' => bcrypt('teacher'),
            ],
            [
                'first_name' => 'manager',
                'last_name' => 'midbro',
                'email' => 'manager@IIS.com',
                'password' => bcrypt('manager'),
            ],
            [
                'first_name' => 'user',
                'last_name' => 'lilpump',
                'email' => 'user@IIS.com',
                'password' => bcrypt('user'),
            ],

        ];

        User::insert($users);


        $user = User::find(1);
        $user->assignRole('admin');
        $user = User::find(2);
        $user->assignRole('teacher');
        $user = User::find(3);
        $user->assignRole('manager');
        $user = User::find(4);
        $user->assignRole('user');



    }
}
