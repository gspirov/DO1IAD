import baseForm from "./baseForm.js";

export default (config = {}) => ({
    ...baseForm(config),

    /**
     * Stores the user-input comment text.
     */
    comment: '',

    /**
     * Validates if the comment text is at least 3 characters long.
     */
    isCommentLongEnough() {
        return this.comment.trim().length >= 3;
    },

    /**
     * Validates if the comment text is no more than 1000 characters long.
     */
    isCommentNotTooLong() {
        return this.comment.trim().length <= 1000;
    },

    /**
     * Handles the submission of the comment form.
     * Sends the input data to a backend API, updates the comment state,
     * broadcasts a 'comment-added' event, and synchronizes the comment count store.
     * Handles validation errors from the backend and displays them as needed.
     */
    async handleSubmit() {
        if (this.submitted) {
            return;
        }

        this.submitted = true;
        this.backendErrors = {};

        const formData = new FormData(this.$el);

        try {
            const response = await fetch(this.$el.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    this.backendErrors = data.errors || {};
                    return;
                }
            }

            this.comment = '';
            this.$dispatch('comment-added', data);
            Alpine.store('comments').count = data.totalCount;
        } catch (error) {
            console.error('Error:', error);
        } finally {
            this.submitted = false;
        }
    },

    invalid() {
        return !this.hasFilledAttribute('comment') ||
               !this.isCommentLongEnough() ||
               !this.isCommentNotTooLong();
    }
});
