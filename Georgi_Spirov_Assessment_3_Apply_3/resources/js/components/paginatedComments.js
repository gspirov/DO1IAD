/**
 * Component to manage paginated comments for a specific project.
 * Allows adding, deleting, and fetching more comments.
 */
export default (config = {}) => ({
    // ID of the project this component is associated with.
    projectId: config.projectId ?? null,

    // List of comments currently loaded.
    comments: config.comments ?? [],

    // Offset used for paginated loading, starts at the count of existing comments.
    offset: config.offset ?? 0,

    // Array of comment IDs the user has permissions to delete.
    ableToDeleteCommentIds: config.ableToDeleteCommentIds ?? [],

    // Indicates whether a comment loading request is in progress.
    loading: false,

    /**
     * Add a new comment to the list and update the offset.
     */
    addComment(comment) {
        // Add the comment ID to the deletable list if the user can delete it.
        this.ableToDeleteCommentIds.push(comment.id);

        // Prepend the comment at the top of the comments array.
        this.comments.unshift(comment);

        // Increment the offset to reflect the new total number of comments.
        this.offset++;
    },

    /**
     * Delete a comment by its ID and update the comment list and offset.
     */
    async deleteComment(commentId) {
        try {
            // Send a request to delete the comment.
            const response = await fetch(`/project-comments/${commentId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            // Parse the response JSON data.
            const json = await response.json();

            // Filter out the deleted comment from the comments array.
            this.comments = this.comments.filter(comment => comment.id !== commentId);

            // Decrease the offset to reflect the deletion.
            this.offset--;

            // Update the global comment count stored in Alpine.js.
            Alpine.store('comments').count = json.totalCount;
        } catch (error) {
            console.error(error);
        }
    },

    /**
     * Load more comments for the project using pagination.
     */
    async loadMore() {
        // Exit if a loading process is already active or projectId is missing.
        if (this.loading || !this.projectId) {
            return;
        }

        this.loading = true;

        try {
            // Send a request to fetch more comments starting from the current offset.
            const response = await fetch(`/projects/${this.projectId}/comments?offset=${this.offset}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            // Throw an error for failed responses.
            if (!response.ok) {
                throw new Error('Failed to load comments.');
            }

            // Parse the data and append the new comments.
            const data = await response.json();
            this.comments.push(...data.comments);

            // Add the IDs of the new comments to the deletable list.
            this.ableToDeleteCommentIds.push(...data.ableToDeleteCommentsIds);

            // Update the offset based on the number of fetched comments.
            this.offset += data.comments.length;
        } catch (error) {
            console.error(error);
        } finally {
            // Ensure the loading state is reset once processing is complete.
            this.loading = false;
        }
    }
});
