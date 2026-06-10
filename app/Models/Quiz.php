<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $primaryKey = 'quiz_id';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'total_marks',
        'passing_marks',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'total_marks' => 'decimal:2',
            'passing_marks' => 'decimal:2',
            'duration_minutes' => 'integer',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class, 'quiz_id');
    }
}
