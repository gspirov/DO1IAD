/**
 * Reusable component with the main responsibility to validate password fields.
 */
export default (config = {}) => ({
    password: "",
    password_confirmation: "",
    passwordRulesTranslations: config.passwordRulesTranslations ?? {},

    /**
     * Checks if the password has a minimum length of 8 characters.
     */
    isPasswordLongEnough() {
        return this.password.trim().length >= 8;
    },
    /**
     * Checks if the password contains both uppercase and lowercase letters.
     */
    hasPasswordMixedCase() {
        const value = this.password.trim();
        return /[a-z]/.test(value) && /[A-Z]/.test(value);
    },
    /**
     * Checks if the password contains at least one numeric digit.
     */
    hasPasswordNumber() {
        return /[0-9]/.test(this.password.trim());
    },
    /**
     * Checks if the password contains at least one special character.
     */
    hasPasswordSpecialChar() {
        return /[!@#$%^&*(),.?":{}|<>]/.test(this.password.trim());
    },
    /**
     * Retrieves the validation rules for the password, listing each rule's label and if it's valid.
     * @returns {Array<Object>} An array of password validation rules, where each object contains a label and whether the rule is valid.
     */
    passwordRules() {
        return [
            {
                label: 'validation.min.string',
                valid: this.isPasswordLongEnough()
            },
            {
                label: 'validation.password.mixed',
                valid: this.hasPasswordMixedCase()
            },
            {
                label: 'validation.password.numbers',
                valid: this.hasPasswordNumber()
            },
            {
                label: 'validation.custom.password.special_char',
                valid: this.hasPasswordSpecialChar()
            }
        ];
    },
    /**
     * Checks if the password meets all validation rules (length, mixed case, number, and special character).
     */
    passwordValid() {
        return this.isPasswordLongEnough() &&
               this.hasPasswordMixedCase() &&
               this.hasPasswordNumber() &&
               this.hasPasswordSpecialChar();
    },
    /**
     * Checks if the password confirmation matches the password.
     */
    passwordConfirmValid() {
        return this.password === this.password_confirmation;
    },
    /**
     * Checks if the password or its confirmation is invalid based on the input fields and validation rules.
     */
    isPasswordInvalid() {
        return !this.hasFilledAttribute('password') ||
               !this.hasFilledAttribute('password_confirmation') ||
               !this.passwordValid() ||
               !this.passwordConfirmValid();
    }
});
