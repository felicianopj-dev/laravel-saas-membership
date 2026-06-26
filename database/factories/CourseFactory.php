<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => Str::headline($title),
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'thumbnail' => null,
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
