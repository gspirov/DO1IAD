<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\User;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCommentsControllerTest extends TestCase
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

    public function test_guest_cannot_leave_comments(): void
    {
        $project = Project::query()->firstOrFail();

        $response = $this->post(
            route('project-comments.store'),
            [
                'project_id' => $project->id,
                'comment' => fake()->sentence(),
            ]
        );

        $response->assertRedirect(route('login'));
    }

    public function test_project_owner_should_not_be_able_to_leave_comments(): void
    {
        $user = User::query()
                    ->whereHas('projects')
                    ->with('projects')
                    ->firstOrFail();

        $firstUserProject = $user->projects->first();

        $response = $this->actingAs($user)
                         ->post(
                             route('project-comments.store'),
                             [
                                 'project_id' => $firstUserProject->id,
                                 'comment' => fake()->sentence()
                             ]
                         );

        $response->assertForbidden();
    }

    public function test_only_comment_owner_and_project_owner_can_delete(): void
    {
        $commentator = User::query()->firstOrFail();

        $randomUserDifferentThanCommentator = User::query()
                                                  ->whereNot('id', '=', $commentator->id)
                                                  ->firstOrFail();

        $project = Project::query()
                          ->whereNotIn('user_id', [$commentator->id, $randomUserDifferentThanCommentator->id])
                          ->firstOrFail();

        $projectOwner = $project->user;

        $comment = ProjectComment::factory()->create([
            'user_id' => $commentator->id,
            'project_id' => $project->id,
            'comment' => fake()->sentence()
        ]);

        $totalProjectComments = $project->comments()->count();

        $randomUserResponse = $this->actingAs($randomUserDifferentThanCommentator)
                                   ->post(route('project-comments.delete', ['comment' => $comment]));

        $randomUserResponse->assertForbidden();

        $ownerResponse = $this->actingAs($commentator)
                              ->post(route('project-comments.delete', ['comment' => $comment]));

        $ownerResponse->assertOk();

        $ownerResponse->assertExactJson([
            'success' => true,
            'totalCount' => $totalProjectComments - 1
        ]);

        $this->assertDatabaseMissing('project_comments', [
            'id' => $comment->id,
        ]);

        $comment = ProjectComment::factory()->create([
            'user_id' => $commentator->id,
            'project_id' => $project->id,
            'comment' => fake()->sentence()
        ]);

        $totalProjectComments = $project->comments()->count();

        $projectOwnerResponse = $this->actingAs($projectOwner)
                                     ->post(route('project-comments.delete', ['comment' => $comment]));

        $projectOwnerResponse->assertOk();

        $projectOwnerResponse->assertExactJson([
            'success' => true,
            'totalCount' => $totalProjectComments - 1
        ]);

        $this->assertDatabaseMissing('project_comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_leave_comment_with_invalid_field(): void
    {
        $user = User::query()->firstOrFail();
        $userWithProject = User::query()
                               ->whereHas('projects')
                               ->with('projects')
                               ->whereNot('id', '=', $user->id)
                               ->firstOrFail();

        $project = $userWithProject->projects->first();

        $tooShortValidation = $this->actingAs($user)
                                   ->post(
                                       route('project-comments.store'),
                                       [
                                           'project_id' => $project->id,
                                           'comment' => fake()->randomLetter() . fake()->randomLetter() // 2 characters too short violation
                                       ]
                                   );

        $tooShortValidation->assertSessionHasErrors([
            'comment' => 'The comment field must be at least 3 characters.'
        ]);

        $tooLongValidation = $this->actingAs($user)
                                  ->post(
                                       route('project-comments.store'),
                                       [
                                           'project_id' => $project->id,
                                           'comment' => fake()->realTextBetween(1001, 2000) // 1001 characters too long violation
                                       ]
                                  );

        $tooLongValidation->assertSessionHasErrors([
            'comment' => 'The comment field must not be greater than 1000 characters.'
        ]);
    }

    public function test_successfully_leave_comment()
    {
        $user = User::query()->firstOrFail();
        $userWithProject = User::query()
                               ->whereHas('projects')
                               ->with('projects')
                               ->whereNot('id', '=', $user->id)
                               ->firstOrFail();

        $project = $userWithProject->projects->first();

        $commentSentence = fake()->sentence();

        $response = $this->actingAs($user)
                         ->post(
                             route('project-comments.store'),
                             [
                                 'project_id' => $project->id,
                                 'comment' => $commentSentence
                             ]
                         );

        $response->assertOk();

        $commentCriteria = [
            'comment' => $commentSentence,
            'user_id' => $user->id,
            'project_id' => $project->id
        ];

        $this->assertDatabaseHas('project_comments', $commentCriteria);

        $comment = ProjectComment::query()->where($commentCriteria)->firstOrFail();

        $response->assertExactJson([
            'id' => $comment->id,
            'comment' => $comment->comment,
            'created_at' => $comment->created_at->format('Y-m-d H:i'),
            'username' => $comment->user->username,
            'totalCount' => $project->comments()->count()
        ]);
    }
}
