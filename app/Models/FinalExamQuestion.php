<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalExamQuestion extends Model
{
    protected $table = 'final_exam_questions';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'exam_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
    ];

    public function exam()
    {
        return $this->belongsTo(FinalExam::class, 'exam_id');
    }
}
