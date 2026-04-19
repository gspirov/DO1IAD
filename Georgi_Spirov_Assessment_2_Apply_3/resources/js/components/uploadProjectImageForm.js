import uploadForm from "./uploadForm.js";

/**
 * This component is responsible for handling the upload of project images.
 */
export default (config = {}) => ({
    ...uploadForm(config),

    /**
     * Handles the successful submission of the form by reloading the page.
     */
    onSuccess() {
        location.reload();
    }
});
