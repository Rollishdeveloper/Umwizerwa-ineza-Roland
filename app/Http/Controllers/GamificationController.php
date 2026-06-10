<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use App\Models\Badge;
use App\Models\Achievement;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function index()
    {
        if (!auth()->user()->isStudent()) {
            $leaderboard = $this->gamificationService->getLeaderboard();
            return view('gamification.leaderboard', compact('leaderboard'));
        }

        $student = auth()->user()->student;
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $summary = $this->gamificationService->getStudentSummary($student);
        $badges = Badge::withCount('students')->get();
        $achievements = Achievement::withCount('students')->get();
        $studentBadges = $student->badges;
        $studentAchievements = $student->achievements;
        $leaderboard = $this->gamificationService->getLeaderboard(10);

        return view('gamification.index', compact(
            'student', 'summary', 'badges', 'achievements',
            'studentBadges', 'studentAchievements', 'leaderboard'
        ));
    }

    public function leaderboard()
    {
        $leaderboard = $this->gamificationService->getLeaderboard(50);
        $student = auth()->user()->student;
        $studentRank = null;

        if ($student) {
            $allStudents = \App\Models\Student::orderBy('points', 'desc')->pluck('student_id')->toArray();
            $studentRank = array_search($student->student_id, $allStudents);
            $studentRank = $studentRank !== false ? $studentRank + 1 : null;
        }

        return view('gamification.leaderboard', compact('leaderboard', 'student', 'studentRank'));
    }

    public function badges()
    {
        $badges = Badge::withCount('students')->get();
        $student = auth()->user()->student;
        $studentBadges = $student ? $student->badges : collect();

        return view('gamification.badges', compact('badges', 'studentBadges'));
    }

    public function achievements()
    {
        $achievements = Achievement::withCount('students')->get();
        $student = auth()->user()->student;
        $studentAchievements = $student ? $student->achievements : collect();

        return view('gamification.achievements', compact('achievements', 'studentAchievements'));
    }
}
