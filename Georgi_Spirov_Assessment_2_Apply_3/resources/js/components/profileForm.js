import baseForm from './baseForm.js';

/**
 * Profile form component used for editing user profile information.
 */
export default (config = {}) => ({
    ...baseForm(config),
    username: config.username ?? "",
    email: config.email ?? "",

    /**
     * Checks if the username is at least 6 characters long.
     */
    isUsernameLongEnough() {
        return this.username.trim().length >= 6;
    },
    /**
     * Validates the email format using a regular expression.
     */
    isEmailValid() {
        return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/.test(this.email.trim());
    },
    /**
     * Determines if the profile form has invalid inputs.
     */
    isProfileInvalid() {
        return !this.hasFilledAttribute('username') ||
               !this.hasFilledAttribute('email') ||
               !this.isUsernameLongEnough() ||
               !this.isEmailValid();
    },

    invalid() {
        return this.isProfileInvalid();
    }
});
