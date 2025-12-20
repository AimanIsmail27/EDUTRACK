<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = [
            [
                'StaffID' => 'L001',
                'Name' => 'Dr. Khalid Smith',
                'Email' => 'lecturer@gmail.com'
            ],
            [
                'StaffID' => 'L002',
                'Name' => 'Dr. Siti Aminah',
                'Email' => 'siti@ump.edu.my'
            ],
            [
                'StaffID' => 'L003',
                'Name' => 'Prof. Ahmad Zaki',
                'Email' => 'zaki@ump.edu.my'
            ],
            [
                'StaffID' => 'L004',
                'Name' => 'Dr. Wong Kah Chun',
                'Email' => 'wong@ump.edu.my'
            ],
            [
                'StaffID' => 'L005',
                'Name' => 'Pn. Norhaliza binti Musa',
                'Email' => 'norhaliza@ump.edu.my'
            ],
            [
                'StaffID' => 'L006',
                'Name' => 'Dr. Murali Krishnan',
                'Email' => 'murali@ump.edu.my'
            ],
        ];

        foreach ($lecturers as $lec) {
            DB::table('lecturer')->updateOrInsert(
                ['StaffID' => $lec['StaffID']], // Unique key to check
                [
                    'Name'  => $lec['Name'],
                    'Email' => $lec['Email'],
                ]
            );
        }
    }
}