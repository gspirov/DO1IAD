<?php

namespace App\Http\Requests\Auth;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    /**
     * Define the validation rules for the registration request.
     *
     * @return array The rules that the incoming request must satisfy.
     */
    public function rules(): array
    {
        return [
            /**
             * The username is required, must be a string with a:
             * - minimum of 6 characters
             * - maximum of 255 characters
             * - unique within the "users" table.
             */
            'username' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'unique:users,username'
            ],
            /**
             * The email is required, must be a string with a:
             * - all lowercase characters
             * - valid email address format
             * - maximum of 255 characters
             * - unique within the "users" table.
             */
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                Password::default()
            ],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'The password field must contain at least one special character.',
        ];
    }
}
