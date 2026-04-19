import baseForm from './baseForm.js';
import profileForm from './profileForm.js';
import passwordForm from './passwordForm.js';

/**
 * This component is used to manage the registration form.
 * The component includes methods to check the validity of profile and password details.
 */
export default (config = {}) => ({
    ...baseForm(config),
    ...profileForm(config),
    ...passwordForm(config),

    invalid() {
        return this.isProfileInvalid() ||
               this.isPasswordInvalid();
    }
});
