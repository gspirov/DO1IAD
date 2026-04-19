<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class
        ]);
    }

    public function test_authenticated_users_cannot_request_reset_password(): void
    {
        $user = User::query()->firstOrFail();

        $this->actingAs($user)
             ->get(route('password.request'))
             ->assertRedirect(route('home'));

        $this->actingAs($user)
             ->post(route('password.email'), ['email' => fake()->email()])
             ->assertRedirect(route('home'));

        $this->actingAs($user)
             ->get(route('password.reset', ['token' => fake()->uuid()]))
             ->assertRedirect(route('home'));

        $this->actingAs($user)
             ->post(route('password.update'))
             ->assertRedirect(route('home'));
    }

    public function test_request_password_reset_with_invalid_email()
    {
        $this->post(
            route('password.email'),
            [
                'email' => fake()->text()
            ]
        )
        ->assertsessionHasErrors(['email' => 'The email field must be a valid email address.']);
    }

    public function test_request_password_reset_with_non_existing_email()
    {
        do {
            $email = fake()->unique()->safeEmail();
            $user = User::query()->where('email', '=', $email)->first();
            $notFound = $user === null;
        } while (!$notFound);

        $this->post(
            route('password.email'),
            [
                'email' => $email
            ]
        )
        ->assertSessionHasErrors(['email' => 'We can\'t find a user with that email address.']);
    }

    public function test_reset_password_with_invalid_token()
    {
        $user = User::query()->firstOrFail();

        do {
            $token = Str::random(64);
            $tokenRow = DB::query()
                          ->select('*')
                          ->from('password_reset_tokens')
                          ->where('token', '=', $token)
                          ->first();
            $notFound = $tokenRow === null;
        } while (!$notFound);

        $this->post(
            route('password.update', ['token' => $token]),
            [
                'email' => $user->email,
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!'
            ]
        )
        ->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);
    }

    public function test_request_password_reset_successfully()
    {
        $user = User::query()->firstOrFail();

        $this->post(
            route('password.email'),
            [
                'email' => $user->email
            ]
        )
        ->assertSessionHas([
             'success' => __(Password::ResetLinkSent)
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email
        ]);
    }

    public function test_reset_password_successfully()
    {
        $user = User::query()->firstOrFail();

        $token = Password::createToken($user);

        $rawPassword = 'Password123!';

        $this->post(
            route('password.update', ['token' => $token]),
            [
                'email' => $user->email,
                'password' => $rawPassword,
                'password_confirmation' => $rawPassword
            ]
        )
        ->assertRedirect()
        ->assertSessionHas([
            'success' => 'Your password has been reset.'
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email
        ]);

        $this->assertTrue(Hash::check($rawPassword, $user->fresh()->password));
    }
}
