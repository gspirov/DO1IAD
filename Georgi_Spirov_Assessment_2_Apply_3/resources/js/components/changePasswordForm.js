import baseForm from "./baseForm.js";
import passwordForm from "./passwordForm.js";

/**
 * This module provides a JavaScript component for handling password change forms,
 * extending base form functionalities with specific behaviors for validating
 * and managing password-related inputs.
 *
 * Key Features:
 * - Inherits validations and behaviors from `baseForm` and `passwordForm`.
 * - Adds specific validation for `current_password` input.
 * - Provides an `invalid` method to determine if the form is valid.
 */

export default (config = {}) => ({
    ...baseForm(config),
    ...passwordForm(config),

    // Holds the value of the current password input field.
    current_password: "",

    /**
     * Validates if the current password field is filled.
     */
    isCurrentPasswordInvalid() {
        return !this.hasFilledAttribute('current_password');
    },

    /**
     * Determines if the form is invalid.
     */
    invalid() {
        return this.isCurrentPasswordInvalid() || this.isPasswordInvalid();
    },
});
