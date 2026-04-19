<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
        ]);
    }

    public function test_logged_user_cannot_use_forgotten_password(): void
    {
        $user = User::where('username', 'testuser')->firstOrFail();

        $response = $this->actingAs($user)
                         ->get(route('password.request'));

        $response->assertRedirect(route('home'));
    }

    public function test_guest_cannot_request_forgotten_password_with_empty_email(): void
    {
        $response = $this->post(route('password.email', [
            'email' => ''
        ]));

        $response->assertSessionHasErrors(['email' => 'The email field is required.']);
    }

    public function test_guest_cannot_request_forgotten_password_with_invalid_email(): void
    {
        $response = $this->post(route('password.email', [
            'email' => 'test.'
        ]));

        $response->assertSessionHasErrors(['email' => 'The email field must be a valid email address.']);
    }

    public function test_guest_cannot_request_forgotten_password_with_non_existing_email(): void
    {
        $response = $this->post(route('password.email', [
            'email' => 'testsomethinwhichwouldneverexist@test.te'
        ]));

        $response->assertSessionHasErrors(['email' => 'We can\'t find a user with that email address.']);
    }

    public function test_guest_successfully_requested_forgotten_password(): void
    {
        $user = User::where('username', 'testuser')->firstOrFail();

        $response = $this->from('/forgot-password')
                         ->post(route('password.email', [
                             'email' => $user->email
                         ]));

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email
        ]);

        $response->assertSessionHas('success', __(Password::ResetLinkSent));
        $response->assertRedirect(route('password.request', [], false));
    }
}
