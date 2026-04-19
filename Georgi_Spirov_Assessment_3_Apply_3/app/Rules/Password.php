<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Password as BasePassword;

/**
 * Extends the base Laravel password validation rules with custom rules.
 * Provides enhanced validation functionality for password requirements.
 */
class Password extends BasePassword
{
    /**
     * Default password validation rules.
     *
     * Ensures the following constraints:
     * - Minimum length of 8 characters.
     * - Includes both uppercase and lowercase letters.
     * - Contains at least one numeric digit.
     * - Must match a specified regex pattern for special characters.
     * - Must be confirmed by an additional input field.
     */
    public static function default(): static
    {
        return static::min(8)
                     ->mixedCase()
                     ->numbers()
                     ->rules([
                         'required',
                         'regex:/[!@#$%^&*(),.?":{}|<>]/',
                         'confirmed'
                     ]);
    }
}
