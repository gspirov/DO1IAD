import baseForm from "./baseForm.js";

/**
 * This component represents a project rating form.
 * It enables users to submit or update their ratings for a project.
 * It also dispatches an event when the rating is successfully updated to inform other components in the application.
 */
export default (config = {}) => ({
    ...baseForm(config),
    /**
     * The current selected rating.
     * This value represents the user's rating input, which can be either a new value
     * or modified from the initial rating set by `previousRating`.
     */
    rating: config.rating ?? null,

    /**
     * The previously submitted rating for the project.
     * This value is used to determine whether the rating is updated or left unchanged.
     */
    previousRating: config.rating ?? null,

    /**
     * A predefined array of possible ratings, each represented by a star count,
     * with an associated label for display purposes in the UI.
     */
    stars: [
        {'id': 1, label: '1 Star'},
        {'id': 2, label: '2 Stars'},
        {'id': 3, label: '3 Stars'},
        {'id': 4, label: '4 Stars'},
        {'id': 5, label: '5 Stars'},
    ],

    /**
     * Validates the rating input. A valid rating is either unchanged from its previous value or
     * within the allowed range of 1 to 5 inclusive.
     */
    isRatingValid() {
        return (this.previousRating && !this.rating) || (this.rating >= 1 && this.rating <= 5);
    },

    /**
     * Handles form submission for the rating.
     * If the submission is successful, dispatches a custom event with the updated rating statistics.
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

            this.previousRating = this.rating;

            this.$dispatch('project-rating-updated', {
                average_rating: data.averageRating,
                ratings_count: data.numberOfRatings
            });
        } catch (error) {
            console.error('Error:', error);
        } finally {
            this.submitted = false;
        }
    },

    /**
     * Determines whether the form is in an invalid state based on specific criteria.
     * The form is invalid if
     * - The user does not submit any rating (null) and the previous rating is not set.
     * - The rating is not valid (less than 1 or greater than 5).
     * User can unset already applied rating by setting it to null (empty).
     */
    invalid() {
        return (!this.previousRating && !this.hasFilledAttribute('rating')) ||
               !this.isRatingValid();
    }
});
