<?php

namespace Database\Factories;

use App\Models\ProjectRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectRating>
 */
class ProjectRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating' => fake()->randomElement([2,3,4,4,5,5])
        ];
    }
}
