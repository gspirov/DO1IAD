<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class MyProjectImagesControllerTest extends TestCase
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

    public function test_guest_cannot_view_project_images(): void
    {
        $response = $this->get(route('my-projects.images', [
            'project' => Project::query()->firstOrFail()
        ]));

        $response->assertRedirect(route('login'));
    }

    public function test_prevent_unauthorized_access()
    {
        Storage::fake('public');

        $users = User::query()
                     ->whereHas('projects')
                     ->with('projects')
                     ->take(2)
                     ->get();

        $firstUser = $users->get(0);
        $secondUser = $users->get(1);

        $firstUserProject = $firstUser->projects->first();

        $this->actingAs($secondUser)
             ->get(route('my-projects.images', [
                 'project' => $firstUserProject
             ]))
             ->assertForbidden();

        $file = UploadedFile::fake()->image('test.jpg');

        $this->actingAs($secondUser)
             ->post(
                 route('my-projects.images.upload', ['project' => $firstUserProject]),
                 [
                     'images' => [$file]
                 ]
             )
             ->assertForbidden();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_prevent_uploading_invalid_file()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.pdf', 3000);

        $this->grabFirstUserAndUploadFile($file);

        $errors = session('errors')->get('images.0');

        $this->assertContains('The image field must be an image.', $errors);
        $this->assertContains('The image field must not be greater than 2048 kilobytes.', $errors);
        $this->assertContains('The image field must be a file of type: jpg, jpeg, png, webp.', $errors);
    }

    public function test_successfully_uploaded_file()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('image.png');

        $response = $this->grabFirstUserAndUploadFile($file);

        $response->assertOk();

        $response->assertExactJson([
            'ok' => true,
            'message' => 'Images uploaded successfully.'
        ]);

        $this->assertDatabaseHas('project_images', [
            'path' => 'projects/' . $file->hashName()
        ]);

        Storage::disk('public')->assertExists('projects/' . $file->hashName());
    }

    public function test_successfully_deleted_file()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('image.png', 2000);

        $user = User::query()
                    ->whereHas('projects')
                    ->with('projects')
                    ->firstOrFail();

        $firstProject = $user->projects->first();

        $path = 'projects/' . $file->hashName();

        Storage::disk('public')->put($path, $file->getContent());

        $image = ProjectImage::factory()->create([
            'path' => $path,
            'project_id' => $firstProject->id
        ]);

        $response = $this->actingAs($user)
                         ->post(route(
                             'my-projects.images.delete',
                             ['project' => $firstProject, 'image' => $image]
                         ));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully deleted image.');
        $this->assertDatabaseMissing('project_images', [
            'id' => $image->id
        ]);
        Storage::disk('public')->assertMissing($path);
    }

    private function grabFirstUserAndUploadFile(UploadedFile $file): TestResponse
    {
        $user = User::query()
                    ->whereHas('projects')
                    ->with('projects')
                    ->firstOrFail();

        return $this->actingAs($user)
                    ->post(
                        route('my-projects.images.upload', $user->projects->first()),
                        [
                            'images' => [$file]
                        ]
                    );
    }
}
