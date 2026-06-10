<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    protected $primaryKey = 'material_id';

    protected $fillable = [
        'materialable_id',
        'materialable_type',
        'title',
        'description',
        'type',
        'file_path',
        'url',
        'file_size',
        'mime_type',
        'position',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function materialable()
    {
        return $this->morphTo();
    }
}
