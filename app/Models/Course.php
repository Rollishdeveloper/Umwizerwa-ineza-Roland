<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;
    protected $primaryKey = 'course_id';

    protected $fillable = [
        'instructor_id',
        'title',
        'slug',
        'description',
        'learning_objectives',
        'prerequisites',
        'thumbnail',
        'category_id',
        'duration',
        'level',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration' => 'integer',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title) . '-' . Str::random(6);
            }
        });
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'course_id')->orderBy('position');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'course_id', 'student_id', 'course_id', 'student_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'course_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }

    public function finalExams()
    {
        return $this->hasMany(FinalExam::class, 'course_id');
    }

    public function learningMaterials()
    {
        return $this->morphMany(LearningMaterial::class, 'materialable');
    }

    public function approvalWorkflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'course_id');
    }

    public function contentVersions()
    {
        return $this->hasMany(ContentVersion::class, 'course_id');
    }

    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('enrollments')->orderBy('enrollments_count', 'desc')->take($limit);
    }

    public function scopeRecommended($query, $categoryId = null)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId)->published();
        }
        return $query->published()->popular();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    public function scopeWithDurationRange($query, $min, $max)
    {
        return $query->whereBetween('duration', [$min, $max]);
    }
}
