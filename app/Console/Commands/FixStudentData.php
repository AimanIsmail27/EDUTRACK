<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStudentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:fix-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix swapped course/year data in users table by syncing with student table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing student data in users table...');
        $this->newLine();

        $students = User::where('role', 'student')->get();
        $fixed = 0;
        $skipped = 0;

        foreach ($students as $user) {
            $studentRecord = Student::where('MatricID', $user->matric_id)->first();
            
            if ($studentRecord) {
                // Update user with correct data from student table
                $user->course = $studentRecord->Course;
                $user->year = $studentRecord->Year;
                $user->email = $studentRecord->Email ?? strtolower($user->matric_id) . '@student.edu';
                $user->save();
                $fixed++;
                $this->info("✓ Fixed: {$user->name} ({$user->matric_id})");
            } else {
                $skipped++;
                $this->warn("⚠ Skipped: {$user->name} ({$user->matric_id}) - No student record found");
            }
        }

        $this->newLine();
        $this->info("Fixed: {$fixed} student(s)");
        if ($skipped > 0) {
            $this->warn("Skipped: {$skipped} student(s)");
        }
        $this->info('Data fix complete!');
    }
}
