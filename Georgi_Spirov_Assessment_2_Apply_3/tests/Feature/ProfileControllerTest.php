<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class
        ]);
    }

    public function test_prevent_guests_access()
    {
        $this->get(route('profile.edit'))
             ->assertRedirectToRoute('login');

        $this->post(route('profile.update'))
             ->assertRedirectToRoute('login');

        $this->get(route('profile.change-password'))
             ->assertRedirectToRoute('login');

        $this->post(route('profile.update-password'))
             ->assertRedirectToRoute('login');
    }

    public function test_edit_profile_with_empty_mandatory_fields()
    {
        $user = User::query()->firstOrFail();

        $response = $this->actingAs($user)
                         ->post(route('profile.update', [
                             'username' => '',
                             'email' => '',
                        ]));

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'username' => 'The username field is required.',
            'email' => 'The email field is required.'
        ]);
    }

    public function test_edit_profile_with_already_existing_username_and_email()
    {
        $user = User::query()->firstOrFail();

        $secondUser = User::query()->where('id', '<>', $user->id)->firstOrFail();

        $response = $this->actingAs($user)
                         ->post(route('profile.update', [
                             'username' => $secondUser->username,
                             'email' => $secondUser->email,
                        ]));

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'username' => 'The username has already been taken.',
            'email' => 'The email has already been taken.'
        ]);
    }

    /**
     * @throws JsonException
     */
    public function test_edit_profile_successfully()
    {
        $user = User::query()->firstOrFail();

        do {
            $randomUsername = fake()->unique()->userName();
            $randomEmail = fake()->unique()->safeEmail();

            $foundUser = User::query()
                             ->where('username', $randomUsername)
                             ->where('email', $randomEmail)
                             ->first();

            $notFound = $foundUser === null;
        } while (!$notFound);

        $response = $this->actingAs($user)
                         ->post(route('profile.update', [
                             'username' => $randomUsername,
                             'email' => $randomEmail,
                        ]));

        $response->assertRedirect();

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'username' => $randomUsername,
            'email' => $randomEmail,
        ]);
    }

    public function test_avatar_is_required(): void
    {
        Storage::fake('public');

        $user = User::query()->firstOrFail();

        $this->actingAs($user)
             ->post(route('profile.avatar'), [
                 'avatar' => '',
             ])
             ->assertSessionHasErrors([
                 'avatar' => 'The avatar field is required.',
             ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_avatar_must_be_valid_image(): void
    {
        Storage::fake('public');

        $user = User::query()->firstOrFail();

        $file = UploadedFile::fake()->create('test.pdf', 5000, 'application/pdf');

        $response = $this->actingAs($user)
                         ->post(route('profile.avatar'), [
                             'avatar' => $file
                         ]);

        $response->assertSessionHasErrors('avatar');

        $errors = session('errors')->get('avatar');

        $this->assertContains('The avatar field must be an image.', $errors);
        $this->assertContains('The avatar field must not be greater than 2048 kilobytes.', $errors);
        $this->assertContains('The avatar field must be a file of type: jpg, jpeg, png, webp.', $errors);
    }

    /**
     * @throws JsonException
     */
    public function test_avatar_successfully_uploaded(): void
    {
        Storage::fake('public');

        $user = User::query()->firstOrFail();

        $file = UploadedFile::fake()->create('test.png', 2000);

        $response = $this->actingAs($user)
                         ->post(
                             route('profile.avatar'),
                             [
                                 'avatar' => $file
                             ]
                         );

        $response->assertSessionHasNoErrors();

        $path = sprintf('avatars/%s', $file->hashName());

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar' => $path
        ]);

        Storage::disk('public')->assertExists($path);
    }

    /**
     * @throws JsonException
     */
    public function test_delete_avatar_successfully()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('image.png', 2000);
        $path = 'projects/' . $file->hashName();
        Storage::disk('public')->put($path, $file->getContent());

        $user = User::query()->firstOrFail();

        $user->update(['avatar' => $path]);

        $response = $this->actingAs($user)
                         ->post(route('profile.delete-avatar'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'avatar' => $path
        ]);

        Storage::disk('public')->assertMissing($path);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Avatar deleted successfully.');
    }
}
