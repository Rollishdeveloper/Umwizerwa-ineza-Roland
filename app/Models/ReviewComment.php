<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewComment extends Model
{
    protected $table = 'review_comments';
    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'review_id', 'content_type', 'content_id', 'comment', 'severity'
    ];

    public function review() { return $this->belongsTo(CourseReview::class, 'review_id'); }
}
