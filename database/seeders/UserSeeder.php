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

            DB::table('lecturer')->updateOrInsert(
                ['StaffID' => $lec['StaffID']],
                [
                    'Name'    => $lec['Name'],
                    'Email'   => $lec['Email'],
                ]
            );
        }

        // 4. Handle ALL Students from the 'student' table
        // This ensures the user_id column in 'student' table is correctly populated
        $allStudents = DB::table('student')->get();

        foreach ($allStudents as $std) {
            // Generate a consistent email based on MatricID (e.g., cb21001@student.com)
            $email = strtolower($std->MatricID) . '@student.com';
            
            // Special Case: If this is Ahmad Firdaus, use the specific email you wanted
            if ($std->MatricID === 'CB21001') {
                $email = 'student@gmail.com';
            }

            // Create/Update the User record
            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'name' => $std->Name,
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Get the User ID to link back
            $user = DB::table('users')->where('email', $email)->first();

            // Update the student table with the correct user_id link
            DB::table('student')
                ->where('MatricID', $std->MatricID)
                ->update(['user_id' => $user->id]);
        }
    }
}