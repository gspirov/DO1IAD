<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImage>
 */
class ProjectImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => 'projects/sample-' . rand(1, 5) . '.jpeg'
        ];
    }
}
