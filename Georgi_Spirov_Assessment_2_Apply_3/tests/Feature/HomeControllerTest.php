<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\User;
use App\Models\UserProjectFavourite;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
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

    public function test_search_returns_empty_when_no_results(): void
    {
        $response = $this->get(route('home', [
            'title' => 'somethingwhichdoesnotexist'
        ]));

        $response->assertSeeHtml('No projects found');
        $response->assertSeeHtml('Try changing the filters and search again.');
    }

    public function test_search_sorted_by_highest_rated(): void
    {
        $users = User::all();

        $projects = Project::take(3)->get();

        [$projectA, $projectB, $projectC] = $projects;

        $users->slice(0, 1)->each(function ($user) use ($projectA) {
            ProjectRating::factory()->create(['project_id' => $projectA->id, 'user_id' => $user->id, 'rating' => 2]);
        });

        $users->slice(1, 2)->each(function ($user) use ($projectB) {
            ProjectRating::factory()->create(['project_id' => $projectB->id, 'user_id' => $user->id, 'rating' => 3]);
        });

        $users->slice(3, 3)->each(function ($user) use ($projectC) {
            ProjectRating::factory()->create(['project_id' => $projectC->id, 'user_id' => $user->id, 'rating' => 5]);
        });

        $response = $this->get(route('home', [
            'sort' => 'highest_rated'
        ]));

        $response->assertOk();

        $response->assertSeeInOrder([
            $projectC->title,
            $projectB->title,
            $projectA->title
        ]);
    }

    public function test_search_sorted_by_most_liked(): void
    {
        $users = User::all();

        $projects = Project::take(3)->get();

        [$projectA, $projectB, $projectC] = $projects;

        $users->slice(0, 1)->each(function ($user) use ($projectA) {
            UserProjectFavourite::factory()->create(['project_id' => $projectA->id, 'user_id' => $user->id]);
        });

        $users->slice(1, 2)->each(function ($user) use ($projectB) {
            UserProjectFavourite::factory()->create(['project_id' => $projectB->id, 'user_id' => $user->id]);
        });

        $users->slice(3, 3)->each(function ($user) use ($projectC) {
            UserProjectFavourite::factory()->create(['project_id' => $projectC->id, 'user_id' => $user->id]);
        });

        $response = $this->get(route('home', [
            'sort' => 'most_liked'
        ]));

        $response->assertOk();

        $response->assertSeeInOrder([
            $projectC->title,
            $projectB->title,
            $projectA->title
        ]);
    }

    public function test_search_all_filters_returns_found_result()
    {
        $user = User::query()->firstOrFail();

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'title' => 'Exact Matching Project',
            'short_description' => 'Exact Description',
            'phase' => 'development',
            'start_date' => '2025-01-10',
            'end_date' => '2025-02-10',
        ]);

        $otherProject = Project::factory()->create([
            'user_id' => $user->id,
            'title' => 'Another Project',
            'short_description' => 'Another Description',
        ]);

        $response = $this->get(route('home', [
            'title' => $project->title,
            'short_description' => $project->short_description,
            'phase' => $project->phase->value,
            'start_date' => $project->start_date->format('Y-m-d'),
            'end_date' => $project->end_date->format('Y-m-d'),
        ]));

        $response->assertOk();

        $response->assertSee($project->title);
        $response->assertDontSee($otherProject->title);
    }
}
