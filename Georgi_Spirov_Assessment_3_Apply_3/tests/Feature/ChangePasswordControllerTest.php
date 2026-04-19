<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use JsonException;
use Tests\TestCase;

class ChangePasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class
        ]);
    }

    public function test_guest_cannot_change_password(): void
    {
        $response = $this->post(route('profile.change-password'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_change_password_when_confirmation_does_not_match(): void
    {
        $user = User::where('username', 'testuser')->firstOrFail();

        $response = $this->actingAs($user->fresh())
                         ->from('/profile/change-password')
                         ->post(route('profile.update-password'), [
                             'current_password' => 'password123',
                             'password' => 'new-password-123',
                             'password_confirmation' => 'different-password',
                         ]);

        $response->assertSessionHasErrors([
            'password' => 'The password field confirmation does not match.'
        ]);

        $this->assertTrue(
            Hash::check('password123', $user->fresh()->password)
        );
    }

    public function test_user_cannot_change_password_with_empty_fields(): void
    {
        $user = User::where('username', 'testuser')->firstOrFail();

        $response = $this->actingAs($user)
                         ->from('/profile')
                         ->post(route('profile.update-password'), [
                             'current_password' => '',
                             'password' => '',
                             'password_confirmation' => '',
                         ]);

        $response->assertSessionHasErrors([
            'current_password',
            'password',
        ]);

        $this->assertTrue(
            Hash::check('password123', $user->fresh()->password)
        );
    }

    /**
     * @throws JsonException
     */
    public function test_user_successfully_changed_password(): void
    {
        $user = User::where('username', 'testuser')->firstOrFail();

        $response = $this->actingAs($user)
                         ->from('/profile')
                         ->post(route('profile.update-password'), [
                             'current_password' => 'password123',
                             'password' => 'NewPassword123!',
                             'password_confirmation' => 'NewPassword123!',
                         ]);

        $response->assertSessionHasNoErrors();

        $this->assertTrue(
            Hash::check('NewPassword123!', $user->fresh()->password)
        );
    }
}
