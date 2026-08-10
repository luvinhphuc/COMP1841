/** Removes empty and default filters so submitted discussion URLs remain canonical. */
const discussionFilterForm = document.querySelector('[data-discussion-filter-form]');

discussionFilterForm?.addEventListener('formdata', (event) => {
    Array.from(discussionFilterForm.elements).forEach((field) => {
        if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement)) {
            return;
        }

        const value = field.value.trim();
        const defaultValue = field.dataset.defaultValue;

        if (value === '' || (defaultValue !== undefined && value === defaultValue)) {
            event.formData.delete(field.name);
        }
    });
});
