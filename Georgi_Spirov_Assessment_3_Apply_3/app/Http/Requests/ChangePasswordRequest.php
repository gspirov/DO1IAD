<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            /**
             * Current password is required to prevent unauthorized password change.
             */
            'current_password' => ['required', 'current_password'],
            'password' => [
                Password::default()
            ],
            'password_confirmation' => ['required'],
        ];
    }
}
