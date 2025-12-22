<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'administrator',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Khalid Smith',
                'email' => 'lecturer@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'lecturer',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ahmad Firdaus',
                'email' => 'student@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'matric_id' => 'CA22001',
                'course' => 'BCS2311',
                'year' => 2,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']], // Unique identifier to check
                $user
            );
        }
    }
}