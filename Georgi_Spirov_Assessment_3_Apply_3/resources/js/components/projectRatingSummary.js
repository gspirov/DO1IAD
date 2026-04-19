/**
 * This component provides functionalities to display and manage project rating summaries.
 */
export default (config = {}) => ({
    // Initialize averageRating and ratingsCount from the config or default to 0 if not provided.
    averageRating: Number(config.averageRating ?? 0),
    ratingsCount: Number(config.ratingsCount ?? 0),

    /**
     * Getter for formatted average rating.
     * It returns the average rating with one decimal point.
     */
    get formattedRating() {
        return this.averageRating.toFixed(1);
    },

    /**
     * Method to update the ratings data with new information.
     */
    updateRating(payload) {
        // Update the averageRating if it's provided in the payload.
        if (payload.average_rating !== undefined) {
            this.averageRating = Number(payload.average_rating);
        }

        // Update the ratingsCount if it's provided in the payload.
        if (payload.ratings_count !== undefined) {
            this.ratingsCount = Number(payload.ratings_count);
        }
    }
});
