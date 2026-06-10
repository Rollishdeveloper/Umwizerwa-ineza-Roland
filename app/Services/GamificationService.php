<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Badge;
use App\Models\Achievement;
use App\Models\ActivityLog;
use App\Models\Notification;

class GamificationService
{
    const POINTS_ENROLL_COURSE = 10;
    const POINTS_COMPLETE_LESSON = 5;
    const POINTS_COMPLETE_COURSE = 100;
    const POINTS_PASS_QUIZ = 25;
    const POINTS_SUBMIT_ASSIGNMENT = 15;
    const POINTS_EARN_CERTIFICATE = 75;
    const POINTS_LOGIN_STREAK = 5;
    const POINTS_PERFECT_QUIZ = 50;

    public function awardPoints(Student $student, int $points, string $reason): void
    {
        $student->addPoints($points);

        ActivityLog::create([
            'user_id' => $student->user_id,
            'activity' => "Earned {$points} points: {$reason}",
        ]);

        $this->checkBadges($student);
        $this->checkAchievements($student);
    }

    public function awardBadge(Student $student, Badge $badge): bool
    {
        if ($student->hasBadge($badge->badge_id)) {
            return false;
        }

        $student->badges()->attach($badge->badge_id, ['awarded_at' => now()]);

        Notification::create([
            'user_id' => $student->user_id,
            'title' => '🏆 New Badge Earned!',
            'message' => "You earned the \"{$badge->name}\" badge! {$badge->description}",
            'type' => 'achievement',
        ]);

        ActivityLog::create([
            'user_id' => $student->user_id,
            'activity' => "Earned badge: {$badge->name}",
        ]);

        return true;
    }

    public function unlockAchievement(Student $student, Achievement $achievement): bool
    {
        if ($student->hasAchievement($achievement->achievement_id)) {
            return false;
        }

        $student->achievements()->attach($achievement->achievement_id, ['unlocked_at' => now()]);
        $student->addPoints(30);

        Notification::create([
            'user_id' => $student->user_id,
            'title' => '⭐ Achievement Unlocked!',
            'message' => "Achievement unlocked: \"{$achievement->name}\"! {$achievement->description}",
            'type' => 'achievement',
        ]);

        ActivityLog::create([
            'user_id' => $student->user_id,
            'activity' => "Unlocked achievement: {$achievement->name}",
        ]);

        return true;
    }

    public function checkBadges(Student $student): void
    {
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if ($student->hasBadge($badge->badge_id)) {
                continue;
            }

            $qualifies = false;

            switch ($badge->type) {
                case 'points':
                    $qualifies = $student->points >= ($badge->required_points ?? 999999);
                    break;
                case 'courses':
                    $completedCourses = $student->enrollments()
                        ->where('status', 'completed')
                        ->count();
                    $qualifies = $completedCourses >= ($badge->required_count ?? 1);
                    break;
                case 'quizzes':
                    $passedQuizzes = $student->quizResults()
                        ->where('status', 'passed')
                        ->count();
                    $qualifies = $passedQuizzes >= ($badge->required_count ?? 1);
                    break;
                case 'assignments':
                    $submissions = $student->assignmentSubmissions()
                        ->whereNotNull('marks')
                        ->count();
                    $qualifies = $submissions >= ($badge->required_count ?? 1);
                    break;
                case 'certificates':
                    $certificates = $student->certificates()->count();
                    $qualifies = $certificates >= ($badge->required_count ?? 1);
                    break;
            }

            if ($qualifies) {
                $this->awardBadge($student, $badge);
            }
        }
    }

    public function checkAchievements(Student $student): void
    {
        $achievements = Achievement::all();

        foreach ($achievements as $achievement) {
            if ($student->hasAchievement($achievement->achievement_id)) {
                continue;
            }

            $unlocked = false;

            switch ($achievement->type) {
                case 'first_course':
                    $unlocked = $student->enrollments()
                        ->where('status', 'completed')
                        ->count() >= $achievement->required_value;
                    break;
                case 'quiz_star':
                    $unlocked = $student->quizResults()
                        ->where('status', 'passed')
                        ->count() >= $achievement->required_value;
                    break;
                case 'assignment_pro':
                    $unlocked = $student->assignmentSubmissions()
                        ->whereNotNull('marks')
                        ->count() >= $achievement->required_value;
                    break;
                case 'course_master':
                    $unlocked = $student->enrollments()
                        ->where('completion_percentage', 100)
                        ->count() >= $achievement->required_value;
                    break;
                case 'points_milestone':
                    $unlocked = $student->points >= $achievement->required_value;
                    break;
                case 'certificate_collector':
                    $unlocked = $student->certificates()->count() >= $achievement->required_value;
                    break;
            }

            if ($unlocked) {
                $this->unlockAchievement($student, $achievement);
            }
        }
    }

    public function handleCourseCompletion(Student $student): void
    {
        $this->awardPoints($student, self::POINTS_COMPLETE_COURSE, 'Completed a course');
        $this->checkBadges($student);
        $this->checkAchievements($student);
    }

    public function handleQuizCompletion(Student $student, bool $passed, bool $perfect = false): void
    {
        if ($passed) {
            $points = $perfect ? self::POINTS_PERFECT_QUIZ : self::POINTS_PASS_QUIZ;
            $this->awardPoints($student, $points, $perfect ? 'Perfect quiz score' : 'Passed a quiz');
        }
        $this->checkBadges($student);
        $this->checkAchievements($student);
    }

    public function handleAssignmentSubmission(Student $student): void
    {
        $this->awardPoints($student, self::POINTS_SUBMIT_ASSIGNMENT, 'Submitted an assignment');
        $this->checkBadges($student);
        $this->checkAchievements($student);
    }

    public function handleCertificateEarned(Student $student): void
    {
        $this->awardPoints($student, self::POINTS_EARN_CERTIFICATE, 'Earned a certificate');
        $this->checkAchievements($student);
    }

    /**
     * Get leaderboard - returns Collection of Student models (not arrays).
     */
    public function getLeaderboard(int $limit = 20)
    {
        return Student::with('user')
            ->withCount(['badges', 'achievements'])
            ->orderBy('points', 'desc')
            ->take($limit)
            ->get();
    }

    public function getStudentSummary(Student $student): array
    {
        $nextLevelPoints = $this->getNextLevelPoints($student->level);
        $currentLevelMin = $this->getLevelMinPoints($student->level);

        return [
            'points' => $student->points,
            'level' => $student->level,
            'badges' => $student->badges->count(),
            'achievements' => $student->achievements->count(),
            'next_level_points' => $nextLevelPoints,
            'points_to_next_level' => max(0, $nextLevelPoints - $student->points),
            'progress_percentage' => $nextLevelPoints > $currentLevelMin
                ? min(100, (($student->points - $currentLevelMin) / ($nextLevelPoints - $currentLevelMin)) * 100)
                : 0,
        ];
    }

    private function getNextLevelPoints(int $level): int
    {
        $levels = [0, 200, 500, 900, 1400, 1900, 2500, 3200, 4000, 5000, 6500];
        return $levels[min($level, count($levels) - 1)] ?? 6500;
    }

    private function getLevelMinPoints(int $level): int
    {
        $levels = [0, 0, 200, 500, 900, 1400, 1900, 2500, 3200, 4000, 5000];
        return $levels[min($level - 1, count($levels) - 1)] ?? 0;
    }
}
