<?php

namespace Database\Factories;

use App\Enums\ProjectPhaseEnum;
use App\Models\Project;
use DateMalformedStringException;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws DateMalformedStringException
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year');
        $endDate = $this->faker->dateTimeBetween(
            (clone $startDate)->modify('+1 day'),
            '+6 months'
        );

        return [
            'title' => $this->faker->sentence(3),
            'short_description' => $this->faker->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'phase' => $this->faker->randomElement(array_map('value', ProjectPhaseEnum::cases())),
        ];
    }
}
