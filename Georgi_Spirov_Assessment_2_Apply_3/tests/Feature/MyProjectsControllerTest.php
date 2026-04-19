<?php

namespace Tests\Feature;

use App\Enums\ProjectPhaseEnum;
use App\Models\ProjectComment;
use App\Models\ProjectImage;
use App\Models\ProjectRating;
use App\Models\User;
use App\Models\UserProjectFavourite;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Tests\TestCase;

class MyProjectsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            ProjectSeeder::class,
        ]);
    }

    public function test_guest_cannot_view_my_projects_page(): void
    {
        $response = $this->get(route('my-projects.index',));

        $response->assertRedirect(route('login'));
    }

    public function test_prevent_unauthorized_access()
    {
        $users = User::query()
                     ->whereHas('projects')
                     ->with('projects')
                     ->take(2)
                     ->get();

        $firstUser = $users->get(0);
        $secondUser = $users->get(1);

        $firstUserProject = $firstUser->projects->first();

        $this->actingAs($secondUser)
             ->get(route('my-projects.edit', ['project' => $firstUserProject]))
             ->assertForbidden();

        $this->actingAs($secondUser)
             ->post(route('my-projects.update', ['project' => $firstUserProject]))
             ->assertForbidden();
    }

    public function test_creating_project_with_empty_fields()
    {
        $user = User::query()->firstOrFail();

        $response = $this->actingAs($user)
                         ->post(route('my-projects.create', [
                             'title' => '',
                             'start_date' => '',
                             'end_date' => '',
                             'short_description' => '',
                             'phase' => ''
                         ]));

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'start_date' => 'The start date field is required.',
            'end_date' => 'The end date field is required.',
            'short_description' => 'The short description field is required.',
            'phase' => 'The phase field is required.',
        ]);
    }

    public function test_creating_project_with_start_date_greater_than_end_date()
    {
        $user = User::query()->firstOrFail();

        $response = $this->actingAs($user)
                         ->post(route('my-projects.create', [
                             'title' => fake()->unique()->sentence(),
                             'start_date' => '2025-01-02',
                             'end_date' => '2025-01-01',
                             'short_description' => fake()->unique()->paragraph(),
                             'phase' => ProjectPhaseEnum::DESIGN->value
                         ]));

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'end_date' => 'The end date field must be a date after start date.',
        ]);
    }

    /**
     * @throws JsonException
     */
    public function test_creating_project_successfully()
    {
        $user = User::query()->firstOrFail();

        $title = fake()->unique()->sentence();

        $response = $this->actingAs($user)
                         ->post(route('my-projects.create', [
                             'title' => $title,
                             'start_date' => '2025-01-02',
                             'end_date' => '2025-01-03',
                             'short_description' => fake()->unique()->paragraph(),
                             'phase' => ProjectPhaseEnum::DESIGN->value
                         ]));

        $response->assertRedirect();

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'title' => $title,
        ]);
    }

    public function test_delete_project_successfully()
    {
        $user = User::query()
                    ->whereHas('projects')
                    ->with('projects')
                    ->firstOrFail();

        $firstProject = $user->projects->first();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.png', 2000);

        $path = 'projects/' . $file->hashName();

        Storage::disk('public')->put($path, $file->getContent());

        $image = ProjectImage::factory()->create([
            'path' => $path,
            'project_id' => $firstProject->id
        ]);

        $interactionUser = User::query()->where('id', '<>', $user->id)->firstOrFail();

        ProjectRating::factory()->create([
            'user_id' => $interactionUser->id,
            'project_id' => $firstProject->id,
        ]);

        ProjectComment::factory()->create([
            'user_id' => $interactionUser->id,
            'comment' => fake()->paragraph(),
            'project_id' => $firstProject->id,
        ]);

        UserProjectFavourite::factory()->create([
            'user_id' => $interactionUser->id,
            'project_id' => $firstProject->id,
        ]);

        $response = $this->actingAs($user)
                         ->post(route('my-projects.delete', ['project' => $firstProject]));

        $response->assertRedirect();

        $response->assertSessionHas('success', 'Project deleted successfully.');

        $this->assertDatabaseMissing('projects', [
            'id' => $firstProject->id
        ]);

        $this->assertDatabaseMissing('project_images', [
            'id' => $image->id
        ]);

        $this->assertDatabaseMissing('project_ratings', [
            'project_id' => $firstProject->id
        ]);

        $this->assertDatabaseMissing('project_comments', [
            'project_id' => $firstProject->id
        ]);

        $this->assertDatabaseMissing('user_project_favourites', [
            'project_id' => $firstProject->id,
        ]);

        Storage::disk('public')->assertMissing($path);
    }
}
