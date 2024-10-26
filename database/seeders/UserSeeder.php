<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'role_id' => 3, 
            ]
        ];

        User::insert($users);
    }
}
