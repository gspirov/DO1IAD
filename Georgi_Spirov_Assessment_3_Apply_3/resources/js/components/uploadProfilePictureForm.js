import uploadForm from "./uploadForm.js";

/**
 * This component is responsible for handling the profile picture upload process.
 * It extends a base upload form to include additional behavior specific to profile picture uploads.
 */
export default (config = {}) => ({
    ...uploadForm(config),

    /**
     * Handles the profile picture upload process by validating the files
     * and dispatching a form submission event.
     */
    uploadProfilePicture(event) {
        this.handleFiles(event);

        if (this.hasClientErrors()) {
            return;
        }

        this.$refs.form.dispatchEvent(new Event('submit', {cancelable: true}));
    },

    /**
     * Reloads the current page upon a successful form submission.
     */
    onSuccess() {
        location.reload();
    }
});
