<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('pass');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'role' => 0,
                'email_verified_at' => now(),
                'password' => $password,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'role' => 1,
                'email_verified_at' => now(),
                'password' => $password,
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@gmail.com',
                'role' => 2,
                'email_verified_at' => now(),
                'password' => $password,
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@gmail.com',
                'role' => 3,
                'email_verified_at' => now(),
                'password' => $password,
            ]
        ];

        foreach($users as $user) {
            User::create($user);
        }
    }
}
