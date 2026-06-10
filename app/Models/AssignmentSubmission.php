<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;
    protected $primaryKey = 'submission_id';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'notes',
        'submitted_at',
        'marks',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'marks' => 'decimal:2',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
