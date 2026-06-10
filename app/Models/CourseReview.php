<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    protected $table = 'course_reviews';
    protected $primaryKey = 'review_id';

    protected $fillable = [
        'course_id', 'reviewer_id', 'review_type', 'comments', 'status', 'reviewed_at'
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function course() { return $this->belongsTo(Course::class, 'course_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
    public function comments() { return $this->hasMany(ReviewComment::class, 'review_id'); }
}
