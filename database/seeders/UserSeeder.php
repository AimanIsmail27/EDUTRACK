<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data for all lecturers (Used for both tables)
        $lecturers = [
            ['StaffID' => 'L001', 'Name' => 'Dr. Khalid Smith', 'Email' => 'lecturer@gmail.com'],
            ['StaffID' => 'L002', 'Name' => 'Dr. Siti Aminah', 'Email' => 'siti@ump.edu.my'],
            ['StaffID' => 'L003', 'Name' => 'Prof. Ahmad Zaki', 'Email' => 'zaki@ump.edu.my'],
            ['StaffID' => 'L004', 'Name' => 'Dr. Wong Kah Chun', 'Email' => 'wong@ump.edu.my'],
            ['StaffID' => 'L005', 'Name' => 'Pn. Norhaliza binti Musa', 'Email' => 'norhaliza@ump.edu.my'],
            ['StaffID' => 'L006', 'Name' => 'Dr. Murali Krishnan', 'Email' => 'murali@ump.edu.my'],
        ];

        // 2. Handle Administrator (users table only)
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'administrator',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 3. Handle Lecturers (Double Entry: users AND lecturer tables)
        foreach ($lecturers as $lec) {
            // Update/Insert into 'users' table for Authentication
            DB::table('users')->updateOrInsert(
                ['email' => $lec['Email']],
                [
                    'name' => $lec['Name'],
                    'password' => Hash::make('password123'),
                    'role' => 'lecturer',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Update/Insert into 'lecturer' table for Academic Records
            // Using StaffID as the unique key to prevent duplicate PK errors
            DB::table('lecturer')->updateOrInsert(
                ['StaffID' => $lec['StaffID']],
                [
                    'Name'    => $lec['Name'],
                    'Email'   => $lec['Email'],
                ]
            );
        }

        // 4. Handle Student (users table only)
        DB::table('users')->updateOrInsert(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Ahmad Firdaus',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}