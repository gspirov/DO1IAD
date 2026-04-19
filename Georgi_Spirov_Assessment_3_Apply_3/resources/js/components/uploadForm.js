import baseForm from "./baseForm.js";

/**
 * Upload form component that handles file uploads with validation.
 */
export default (config = {}) => ({
    ...baseForm(config),

    /**
     * Array to store information about selected files, including validation errors.
     */
    files: [],

    /**
     * Maximum number of files allowed based on configuration.
     */
    maxFiles: config.maxFiles ?? 5,
    /**
     * Maximum file size allowed per file in megabytes, set from configuration or defaults.
     */
    maxFileSizeMb: config.maxFileSizeMb ?? 5,
    /**
     * Array of allowed file MIME types, based on configuration.
     */
    allowedTypes: config.allowedTypes ?? [],

    /**
     * Array of general error messages for the upload process.
     */
    generalErrors: [],

    /**
     * Array of file validation error messages.
     */
    fileErrors: [],

    /**
     * Translations for general error messages.
     */
    generalErrorTranslations: config.generalErrorTranslations ?? {},

    /**
     * Translations for file-specific error messages.
     */
    fileErrorTranslations: config.fileErrorTranslations ?? {},

    /**
     * Handles the file input change event, validates and stores selected files.
     */
    handleFiles(event) {
        this.generalErrors = [];
        this.backendErrors = {};

        const selectedFiles = Array.from(event.target.files || []);
        const validatedFiles = [];

        if (!selectedFiles.length) {
            this.files = [];
            return;
        }

        if (selectedFiles.length > this.maxFiles) {
            this.generalErrors.push(this.generalErrors['validation.max.array']);
        }

        selectedFiles.slice(0, this.maxFiles).forEach((file, index) => {
            const fileObj = {
                id: `${file.name}-${file.size}-${index}-${Date.now()}`,
                file,
                errors: []
            };

            this.validateFile(fileObj);
            validatedFiles.push(fileObj);
        });

        this.files = validatedFiles;
    },

    /**
     * Validates an individual file object for size and allowed types.
     */
    validateFile(fileObj) {
        const maxBytes = this.maxFileSizeMb * 1024 * 1024;

        if (this.allowedTypes.length && !this.allowedTypes.includes(fileObj.file.type)) {
            fileObj.errors.push(this.fileErrorTranslations['validation.mimes']);
        }

        if (fileObj.file.size > maxBytes) {
            fileObj.errors.push(this.fileErrorTranslations['validation.max.file']);
        }
    },

    /**
     * Prepares validated files for submission by updating the input element's file list.
     */
    prepareValidatedFiles() {
        const dataTransfer = new DataTransfer();

        this.files.forEach(item => {
            if (item.errors.length > 0) {
                return;
            }

            dataTransfer.items.add(item.file);
        });

        this.$refs.fileInput.files = dataTransfer.files;
    },

    /**
     * Checks for any client-side errors in the form or file validation process.
     */
    hasClientErrors() {
        return this.generalErrors.length > 0 ||
               (this.files.length > 0 && this.files.some(file => file.errors.length > 0));
    },

    /**
     * Handles the form submission by validating files and sending them to the server.
     * Includes error handling for both client and server-side.
     */
    async handleSubmit() {
        if (this.submitted) {
            return;
        }

        this.backendErrors = {};

        if (!this.files.length) {
            this.generalErrors = [this.generalErrorTranslations['validation.min.array']];
            return;
        }

        this.submitted = true;

        this.prepareValidatedFiles();

        try {
            const formData = new FormData(this.$el);

            const response = await fetch(this.$el.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    this.backendErrors = data.errors || {};
                    return;
                }

                throw new Error('Upload failed.');
            }

            this.files = [];
            this.generalErrors = [];
            this.backendErrors = {};

            this.$refs.fileInput.value = '';

            this.onSuccess();
        } catch (error) {
            console.log(error)
            this.generalErrors = [error.message || 'Unexpected error.'];
        } finally {
            this.submitted = false;
        }
    },

    /**
     * Checks if the form is invalid due to either client or backend errors.
     */
    invalid() {
        return this.hasClientErrors() || Object.keys(this.backendErrors).length > 0;
    },

    /**
     * Callback triggered after a successful file upload process.
     * Can be overridden or extended as needed.
     */
    onSuccess() {}
});
