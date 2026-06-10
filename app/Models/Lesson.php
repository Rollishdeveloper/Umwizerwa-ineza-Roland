<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $primaryKey = 'lesson_id';

    protected $fillable = [
        'module_id',
        'title',
        'content',
        'video_url',
        'audio_url',
        'practice_exercises',
        'document_path',
        'duration',
        'position',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function learningMaterials()
    {
        return $this->morphMany(LearningMaterial::class, 'materialable');
    }
}
