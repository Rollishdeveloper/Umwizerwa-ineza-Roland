<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $primaryKey = 'badge_id';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'type',
        'required_points',
        'required_count',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_badges', 'badge_id', 'student_id', 'badge_id', 'student_id')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}
