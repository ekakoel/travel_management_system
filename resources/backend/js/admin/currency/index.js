document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm-delete') || 'Delete this record?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
