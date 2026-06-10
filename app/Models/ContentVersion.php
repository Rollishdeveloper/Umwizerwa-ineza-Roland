<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentVersion extends Model
{
    protected $table = 'content_versions';
    protected $primaryKey = 'version_id';

    protected $fillable = [
        'course_id', 'version_number', 'changes', 'created_by', 'status', 'ai_confidence', 'snapshot'
    ];

    protected function casts(): array
    {
        return [
            'ai_confidence' => 'decimal:2',
            'snapshot' => 'array',
        ];
    }

    public function course() { return $this->belongsTo(Course::class, 'course_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
