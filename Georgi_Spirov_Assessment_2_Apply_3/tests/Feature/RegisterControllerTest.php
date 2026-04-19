<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_without_provided_required_fields()
    {
        $response = $this->from('/register')
                         ->post(route('register.store'), [
                             'username' => '',
                             'email' => '',
                             'password' => '',
                             'password_confirmation' => ''
                         ]);

        $response->assertSessionHasErrors([
            'username' => 'The username field is required.',
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
            'password_confirmation' => 'The password confirmation field is required.',
        ]);
    }

    public function test_register_with_already_existing_email_and_username()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->from('/register')
                         ->post(route('register.store'), [
                             'username' => $user->username,
                             'email' => $user->email,
                             'password' => 'password123',
                             'password_confirmation' => 'password123',
                         ]);


        $response->assertSessionHasErrors([
            'username' => 'The username has already been taken.',
            'email' => 'The email has already been taken.'
        ]);
    }

    public function test_register_with_invalid_email()
    {
        $response = $this->from('/register')
                         ->post(route('register.store'), [
                             'username' => fake()->unique()->userName(),
                             'email' => fake()->unique()->userName(),
                             'password' => 'Password123!',
                             'password_confirmation' => 'Password123!',
                         ]);


        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.'
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_register_without_sufficient_password_requirements()
    {
        $response = $this->from('/register')
                         ->post(route('register.store'), [
                             'username' => fake()->unique()->userName(),
                             'email' => fake()->unique()->safeEmail(),
                             'password' => 'passwor',
                             'password_confirmation' => 'passwor',
                         ]);

        $response->assertSessionHasErrors('password');

        $errors = session('errors')->get('password');

        $this->assertContains('The password field must be at least 8 characters.', $errors);
        $this->assertContains('The password field must contain at least one uppercase and one lowercase letter.', $errors);
        $this->assertContains('The password field must contain at least one number.', $errors);
        $this->assertContains('The password field must contain at least one special character.', $errors);
    }

    public function test_register_successfully()
    {
        $username = fake()->unique()->userName();

        $response = $this->from('/register')
                         ->post(route('register.store'), [
                             'username' => $username,
                             'email' => fake()->unique()->safeEmail(),
                             'password' => 'Password123!',
                             'password_confirmation' => 'Password123!',
                         ]);

        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email_verified_at' => null
        ]);

        $response->assertRedirect(route('login', [], false));
    }
}
