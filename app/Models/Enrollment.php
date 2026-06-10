<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;
    protected $primaryKey = 'enroll_id';

    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_date',
        'completion_percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'datetime',
            'completion_percentage' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
