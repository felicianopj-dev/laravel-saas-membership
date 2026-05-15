<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);
        
        return [
            'course_id' => Course::factory(),
            'title' => Str::headline($title),
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'sort_order' => 1,
            'is_published' => true,
        ];
    }
    
    public function unpublished(): static
    {
        return $this->state([
            'is_published' => false,
        ]);
    }
}