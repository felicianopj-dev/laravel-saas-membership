<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'content',
        'sort_order',
        'is_published',
    ];
    
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
    
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}