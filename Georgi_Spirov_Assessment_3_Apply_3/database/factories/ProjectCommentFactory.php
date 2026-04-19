<?php

namespace Database\Factories;

use App\Models\ProjectComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectComment>
 */
class ProjectCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment' => $this->faker->randomElement([
                'Really impressive work, the structure is very clean.',
                'I like the idea behind this project, well done!',
                'The UI looks modern and easy to use.',
                'Great attention to detail throughout the app.',
                'Nice implementation, everything feels smooth.',
                'This could be very useful in a real-world scenario.',
                'Well structured code and good overall design.',
                'I enjoyed exploring this project, good job!',
                'The functionality is solid and intuitive.',
                'Great concept, I would definitely use this.',
                'Clean and simple design, I like it.',
                'Good work, especially on the frontend!',
                'The user experience is really nice here.',
                'Looks like a production-ready application!',
                'Nice job, everything works as expected.',
                'The layout is very clear and easy to navigate.',
                'Good use of modern tools and technologies.',
                'I like how everything is organised.',
                'Very neat implementation, well done!',
                'This project has a lot of potential.',
                'The features are useful and well thought out.',
                'Good balance between design and functionality.',
                'The interface feels responsive and fast.',
                'Nice clean UI, easy to understand.',
                'Great work on making this user-friendly.',
                'I can see this being used in a real product.',
                'Everything is well put together.',
                'The design is simple but effective.',
                'Nice job on handling user interactions.',
                'Very smooth experience overall.',
            ])
        ];
    }
}
