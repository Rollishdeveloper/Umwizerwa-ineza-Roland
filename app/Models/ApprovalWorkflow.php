<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflow extends Model
{
    protected $table = 'approval_workflows';
    protected $primaryKey = 'workflow_id';

    protected $fillable = [
        'course_id', 'current_stage', 'assigned_reviewer', 'rejection_reason',
        'suggested_corrections', 'priority', 'completed_at'
    ];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function course() { return $this->belongsTo(Course::class, 'course_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'assigned_reviewer'); }

    public function scopeAtStage($query, $stage) { return $query->where('current_stage', $stage); }
    public function scopePending($query) { return $query->whereNotIn('current_stage', ['published', 'rejected', 'archived']); }
}
