<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedMaterial extends Model
{
    protected $table = 'uploaded_materials';
    protected $primaryKey = 'material_id';

    protected $fillable = [
        'user_id', 'course_id', 'original_filename', 'stored_path',
        'mime_type', 'file_size', 'status', 'extracted_text', 'metadata', 'ai_confidence'
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'metadata' => 'array',
            'ai_confidence' => 'decimal:2',
        ];
    }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function course() { return $this->belongsTo(Course::class, 'course_id'); }
}
