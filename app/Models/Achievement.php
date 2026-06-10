<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $primaryKey = 'achievement_id';

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'type',
        'required_value',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_achievements', 'achievement_id', 'student_id', 'achievement_id', 'student_id')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
