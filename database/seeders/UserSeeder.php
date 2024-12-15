<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'Super admin',
                'email' => 'super_admin@gmail.com',
                'password' => bcrypt('password'),
                'role' => User::SUPER_ADMIN_ROLE
            ],
            [
                'name' => 'John Doe',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('adminpass'),
                'role' => User::ADMIN_ROLE
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('userpass'),
                'role' => User::USER_ROLE
            ]
        ];

        User::insert($users);
    }
}
