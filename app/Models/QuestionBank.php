<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $table = 'question_banks';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'course_id', 'module_id', 'user_id', 'question_text', 'question_type',
        'options', 'correct_answer', 'explanation', 'topic', 'difficulty',
        'marks', 'source_reference', 'source_page', 'ai_confidence', 'status'
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'marks' => 'decimal:2',
            'ai_confidence' => 'decimal:2',
        ];
    }

    public function course() { return $this->belongsTo(Course::class, 'course_id'); }
    public function module() { return $this->belongsTo(Module::class, 'module_id'); }
    public function creator() { return $this->belongsTo(User::class, 'user_id'); }

    public function scopeByDifficulty($query, $level) { return $query->where('difficulty', $level); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
}
