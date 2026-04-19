/**
 * This component is responsible for toggling if the project is in the user's favourites.
 */
export default (config = {}) => ({
    // Tracks whether the project is marked as a favourite. It is initialized using the provided config.
    isFav: config.isFav ?? null,

    async toggle(form) {
        // Optimistically toggle the local state before the server request.
        this.isFav = !this.isFav;

        try {
            // Send a POST request to the server using the form's action URL.
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: new FormData(form)
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    this.backendErrors = data.errors || {};
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.isFav = !this.isFav;
        }
    }
});
