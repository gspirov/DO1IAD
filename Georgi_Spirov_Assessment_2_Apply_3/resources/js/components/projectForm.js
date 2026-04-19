import baseForm from "./baseForm.js";

/**
 * Project form component responsible for managing the state and validation logic for a project form.
 */
export default (config = {}) => ({
    ...baseForm(config),
    title: config.title ?? "",
    short_description: config.short_description ?? "",
    start_date: config.start_date ?? "",
    end_date: config.end_date ?? "",
    phase: config.phase ?? "",

    /**
     * Checks if the title length is within the valid range (<= 255 characters).
     */
    hasTitleValidLength() {
        return this.title.trim().length <= 255;
    },

    /**
     * Validates whether the provided start date is earlier than the end date.
     */
    areDatesInvalid() {
        return this.start_date >= this.end_date;
    },

    /**
     * Checks the overall validity of the form by verifying all required fields are filled,
     * the title has a valid length, and the date range is valid.
     */
    invalid() {
        return !this.hasFilledAttribute('title') ||
               !this.hasFilledAttribute('short_description') ||
               !this.hasFilledAttribute('start_date') ||
               !this.hasFilledAttribute('end_date') ||
               !this.hasFilledAttribute('phase') ||
               !this.hasTitleValidLength() ||
               this.areDatesInvalid();
    }
});
