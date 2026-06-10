<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExamResult extends Model
{
    protected $table = 'final_exam_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'exam_id',
        'student_id',
        'score',
        'percentage',
        'status',
        'attempt',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'attempt' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(FinalExam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
