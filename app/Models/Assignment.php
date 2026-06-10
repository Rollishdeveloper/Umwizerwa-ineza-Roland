<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $primaryKey = 'assignment_id';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date',
        'total_marks',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'total_marks' => 'decimal:2',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
}
