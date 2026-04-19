/**
 * A base form handler providing default state and methods for form submission and validation.
 */
export default (config = {}) => ({
    submitted: false,
    backendErrors: config.backendErrors ?? {},

    /**
     * Checks if a form attribute is filled.
     */
    hasFilledAttribute(attribute) {
        const value = this[attribute];

        if (typeof value === 'number') {
            return true;
        }

        if (typeof value === 'undefined') {
            return false;
        }

        return (this[attribute] ?? '').trim().length > 0;
    },

    /**
     * Determines whether the field should show validation feedback or not.
     */
    shouldShow(field) {
        return this.submitted || this.hasFilledAttribute(field);
    },

    /**
     * Checks if a backend validation error exists for a given field.
     */
    hasBackendError(field) {
        return !!this.backendErrors[field];
    },

    /**
     * Clears the backend validation error for a given field.
     */
    clearBackendError(field) {
        if (this.backendErrors[field]) {
            this.backendErrors[field] = false;
        }
    },

    /**
     * A placeholder for form invalidation logic. Always returns `true` in its base implementation.
     */
    invalid() {
        return true;
    },

    /**
     * Handles the form submission process. Marks the form as submitted and submits
     * it if the form is valid.
     */
    handleSubmit() {
        this.submitted = true;
        if (this.invalid()) return;
        this.$el.submit();
    }
});
