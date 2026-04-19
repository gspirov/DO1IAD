import baseForm from './baseForm.js';

/**
 * The `loginForm` component is responsible for managing the state and validation
 * logic for a login form, including fields for email and password.
 */
export default (config = {}) => ({
    ...baseForm(config),

    email: "",
    password: "",

    /**
     * Validates the email address string against a standard email format.
     */
    isEmailValid() {
        return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/.test(this.email.trim());
    },

    /**
     * Determines whether the login form has valid input values.
     * Returns true in case of
     * - missing required fields
     * - or invalid email
     * Otherwise returns false.
     */
    invalid() {
        return !this.hasFilledAttribute('email') ||
               !this.hasFilledAttribute('password') ||
               !this.isEmailValid();
    }
});
