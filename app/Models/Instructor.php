<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;
    protected $primaryKey = 'instructor_id';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'specialization',
        'biography',
        'profile_photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
}
