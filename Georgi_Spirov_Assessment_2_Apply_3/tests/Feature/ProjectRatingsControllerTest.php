<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\User;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRatingsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            ProjectSeeder::class
        ]);
    }

    public function test_guest_cannot_rate_projects(): void
    {
        $project = Project::query()->firstOrFail();

        $response = $this->post(
            route('project-ratings.store'),
            [
                'project_id' => $project->id,
                'rating' => fake()->numberBetween(1, 5),
            ]
        );

        $response->assertRedirect(route('login'));
    }

    public function test_project_owner_should_not_be_able_to_rate(): void
    {
        $user = User::query()
                    ->whereHas('projects')
                    ->with('projects')
                    ->firstOrFail();

        $firstUserProject = $user->projects->first();

        $response = $this->actingAs($user)
                         ->post(
                             route('project-ratings.store'),
                             [
                                 'project_id' => $firstUserProject->id,
                                 'rating' => fake()->numberBetween(1, 5),
                             ]
                         );

        $response->assertForbidden();
    }

    public function test_successfully_rate_project()
    {
        $user = User::query()->firstOrFail();
        $project = Project::query()
                          ->whereNot('user_id', '=', $user->id)
                          ->firstOrFail();

        $rating = fake()->numberBetween(1, 5);

        $response = $this->actingAs($user)
                         ->post(
                             route('project-ratings.store'),
                             [
                                 'project_id' => $project->id,
                                 'rating' => $rating
                             ]
                         );

        $response->assertOk();

        $ratingCriteria = [
            'rating' => $rating,
            'user_id' => $user->id,
            'project_id' => $project->id
        ];

        $this->assertDatabaseHas('project_ratings', $ratingCriteria);

        $project = $project->fresh();

        $response->assertExactJson([
            'numberOfRatings' => $project->ratings()->count(),
            'averageRating' => $project->ratings()->avg('rating')
        ]);
    }

    public function test_unset_project_rate()
    {
        $user = User::query()->firstOrFail();
        $project = Project::query()
                          ->whereNot('user_id', '=', $user->id)
                          ->firstOrFail();

        ProjectRating::factory()->create([
            'user_id' => $user->id,
            'rating' => fake()->numberBetween(1, 5),
            'project_id' => $project->id
        ]);

        $response = $this->actingAs($user)
                         ->post(
                             route('project-ratings.store'),
                             [
                                 'project_id' => $project->id,
                                 'rating' => null
                             ]
                         );

        $response->assertOk();

        $this->assertdatabaseMissing('project_ratings', [
            'user_id' => $user->id,
            'project_id' => $project->id
        ]);

        $project = $project->fresh();

        $response->assertExactJson([
            'numberOfRatings' => $project->ratings()->count(),
            'averageRating' => $project->ratings()->avg('rating')
        ]);
    }
}
