<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $primaryKey = 'certificate_id';

    protected $fillable = [
        'student_id',
        'course_id',
        'certificate_number',
        'issue_date',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'datetime',
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
