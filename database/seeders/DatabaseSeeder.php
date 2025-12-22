<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the UserSeeder we created
        $this->call([
            UserSeeder::class,
            StudentSeeder::class,
            CourseStudentSeeder::class,
        ]);
    }
}
