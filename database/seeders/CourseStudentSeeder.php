<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseStudentSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed if the course exists to avoid FK issues.
        $courseCode = 'BCS2311';
        $matricId = 'CA22001';

        $courseExists = DB::table('courses')->where('C_Code', $courseCode)->exists();

        if (!$courseExists) {
            return;
        }

        DB::table('course_student')->updateOrInsert(
            [
                'course_code' => $courseCode,
                'student_matric' => $matricId,
            ],
            [
                'semester' => 1,
                'year' => now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
