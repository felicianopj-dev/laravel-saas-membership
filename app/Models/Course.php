<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'is_published',
    ];
    
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
    
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
    
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class)->withTimestamps();
    }
    
    public function isAvailableForPlan(?Plan $plan): bool
    {
        if (! $plan) {
            return false;
        }
        
        return $this->plans->contains('id', $plan->id);
    }
}