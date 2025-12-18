<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'MatricID' => 'CA22001',
                'Name' => 'Ahmad Bin Hassan',
                'Course' => 'Networking',
                'Year' => 2
            ],
            [
                'MatricID' => 'CB22002',
                'Name' => 'Siti Aisyah Binti Azman',
                'Course' => 'Software Engineering',
                'Year' => 2
            ],
            [
                'MatricID' => 'CS22003',
                'Name' => 'Tan Kar Wei',
                'Course' => 'Cyber Security',
                'Year' => 3
            ],
            [
                'MatricID' => 'CB22004',
                'Name' => 'Magesh A/L Kumar',
                'Course' => 'Software Engineering',
                'Year' => 1
            ],
            [
                'MatricID' => 'CA22005',
                'Name' => 'Nurul Huda Binti Ramli',
                'Course' => 'Networking',
                'Year' => 2
            ],
        ];

        foreach ($students as $student) {
            // updateOrInsert will update the course if the MatricID already exists
            DB::table('student')->updateOrInsert(
                ['MatricID' => $student['MatricID']],
                $student
            );
        }
    }
}