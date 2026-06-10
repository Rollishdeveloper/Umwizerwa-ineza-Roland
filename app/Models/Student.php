<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'user_id',
        'student_number',
        'name',
        'email',
        'phone',
        'gender',
        'address',
        'date_of_birth',
        'profile_photo',
        'points',
        'level',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'points' => 'integer',
            'level' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id', 'student_id', 'course_id');
    }

    public function quizResults()
    {
        return $this->hasMany(QuizResult::class, 'student_id');
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    // Gamification relationships
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'student_badges', 'student_id', 'badge_id', 'student_id', 'badge_id')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'student_achievements', 'student_id', 'achievement_id', 'student_id', 'achievement_id')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    // Add points and check level up
    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
        $this->recalculateLevel();
    }

    public function recalculateLevel(): void
    {
        $points = $this->points;
        $newLevel = 1;

        if ($points >= 5000) $newLevel = 10;
        elseif ($points >= 4000) $newLevel = 9;
        elseif ($points >= 3200) $newLevel = 8;
        elseif ($points >= 2500) $newLevel = 7;
        elseif ($points >= 1900) $newLevel = 6;
        elseif ($points >= 1400) $newLevel = 5;
        elseif ($points >= 900) $newLevel = 4;
        elseif ($points >= 500) $newLevel = 3;
        elseif ($points >= 200) $newLevel = 2;

        if ($newLevel !== $this->level) {
            $this->update(['level' => $newLevel]);
        }
    }

    public function hasBadge($badgeId): bool
    {
        return $this->badges()->where('badge_id', $badgeId)->exists();
    }

    public function hasAchievement($achievementId): bool
    {
        return $this->achievements()->where('achievement_id', $achievementId)->exists();
    }
}
