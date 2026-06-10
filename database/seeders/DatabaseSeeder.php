<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // First: Create users, categories, and gamification data
            UserSeeder::class,
            CategorySeeder::class,
            GamificationSeeder::class,

            // Second: Create comprehensive course content
            CourseContentSeeder::class,

            // Third: Create student enrollments
            EnrollmentSeeder::class,
        ]);
    }
}
