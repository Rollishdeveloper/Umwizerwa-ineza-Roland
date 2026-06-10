<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExam extends Model
{
    protected $primaryKey = 'exam_id';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'num_questions',
        'auto_grade',
        'attempts_allowed',
    ];

    protected function casts(): array
    {
        return [
            'total_marks' => 'decimal:2',
            'passing_marks' => 'decimal:2',
            'duration_minutes' => 'integer',
            'num_questions' => 'integer',
            'auto_grade' => 'boolean',
            'attempts_allowed' => 'integer',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(FinalExamQuestion::class, 'exam_id');
    }

    public function results()
    {
        return $this->hasMany(FinalExamResult::class, 'exam_id');
    }
}
