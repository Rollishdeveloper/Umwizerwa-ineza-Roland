<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Achievement;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        // ===== BADGES =====
        $badges = [
            [
                'name' => 'First Steps',
                'description' => 'Earn your first 100 points',
                'icon' => 'bi-star-fill',
                'color' => '#6c5ce7',
                'type' => 'points',
                'required_points' => 100,
            ],
            [
                'name' => 'Rising Star',
                'description' => 'Earn 500 points',
                'icon' => 'bi-star-half',
                'color' => '#fd79a8',
                'type' => 'points',
                'required_points' => 500,
            ],
            [
                'name' => 'Knowledge Seeker',
                'description' => 'Earn 1000 points',
                'icon' => 'bi-book-fill',
                'color' => '#00b894',
                'type' => 'points',
                'required_points' => 1000,
            ],
            [
                'name' => 'Scholar',
                'description' => 'Earn 2500 points',
                'icon' => 'bi-mortarboard-fill',
                'color' => '#e17055',
                'type' => 'points',
                'required_points' => 2500,
            ],
            [
                'name' => 'Quiz Master',
                'description' => 'Pass 5 quizzes',
                'icon' => 'bi-pencil-square',
                'color' => '#0984e3',
                'type' => 'quizzes',
                'required_count' => 5,
            ],
            [
                'name' => 'Quiz Legend',
                'description' => 'Pass 15 quizzes',
                'icon' => 'bi-pencil-fill',
                'color' => '#6c5ce7',
                'type' => 'quizzes',
                'required_count' => 15,
            ],
            [
                'name' => 'Assignment Ace',
                'description' => 'Submit 5 assignments',
                'icon' => 'bi-file-text-fill',
                'color' => '#00cec9',
                'type' => 'assignments',
                'required_count' => 5,
            ],
            [
                'name' => 'Hard Worker',
                'description' => 'Submit 15 assignments',
                'icon' => 'bi-journal-text',
                'color' => '#636e72',
                'type' => 'assignments',
                'required_count' => 15,
            ],
            [
                'name' => 'Course Explorer',
                'description' => 'Complete 1 course',
                'icon' => 'bi-compass-fill',
                'color' => '#fdcb6e',
                'type' => 'courses',
                'required_count' => 1,
            ],
            [
                'name' => 'Course Enthusiast',
                'description' => 'Complete 3 courses',
                'icon' => 'bi-map-fill',
                'color' => '#e17055',
                'type' => 'courses',
                'required_count' => 3,
            ],
            [
                'name' => 'Course Master',
                'description' => 'Complete 5 courses',
                'icon' => 'bi-globe',
                'color' => '#d63031',
                'type' => 'courses',
                'required_count' => 5,
            ],
            [
                'name' => 'Certificate Collector',
                'description' => 'Earn 3 certificates',
                'icon' => 'bi-award-fill',
                'color' => '#f39c12',
                'type' => 'certificates',
                'required_count' => 3,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        // ===== ACHIEVEMENTS =====
        $achievements = [
            [
                'name' => 'First Course Completed',
                'description' => 'Complete your first course',
                'icon' => 'bi-check-circle-fill',
                'color' => '#00b894',
                'type' => 'first_course',
                'required_value' => 1,
            ],
            [
                'name' => 'Quiz Star',
                'description' => 'Pass 10 quizzes',
                'icon' => 'bi-star-fill',
                'color' => '#fdcb6e',
                'type' => 'quiz_star',
                'required_value' => 10,
            ],
            [
                'name' => 'Assignment Pro',
                'description' => 'Submit 10 assignments',
                'icon' => 'bi-file-check-fill',
                'color' => '#6c5ce7',
                'type' => 'assignment_pro',
                'required_value' => 10,
            ],
            [
                'name' => 'Course Master',
                'description' => 'Complete 5 courses',
                'icon' => 'bi-mortarboard-fill',
                'color' => '#e17055',
                'type' => 'course_master',
                'required_value' => 5,
            ],
            [
                'name' => 'Century Club',
                'description' => 'Earn 100 points',
                'icon' => 'bi-100',
                'color' => '#0984e3',
                'type' => 'points_milestone',
                'required_value' => 100,
            ],
            [
                'name' => 'Point Millionaire',
                'description' => 'Earn 5000 points',
                'icon' => 'bi-cash-stack',
                'color' => '#f39c12',
                'type' => 'points_milestone',
                'required_value' => 5000,
            ],
            [
                'name' => 'Certificate Collector',
                'description' => 'Collect 3 certificates',
                'icon' => 'bi-award-fill',
                'color' => '#d63031',
                'type' => 'certificate_collector',
                'required_value' => 3,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}
