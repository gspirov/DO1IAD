import baseForm from "./baseForm.js";
import passwordForm from "./passwordForm.js";

/**
 * This component is used to manage the reset password form.
 */
export default (config = {}) => ({
    ...baseForm(config),
    ...passwordForm(config),
    /**
     * Checks if the password field is invalid.
     */
    invalid() {
        return this.isPasswordInvalid();
    }
});
