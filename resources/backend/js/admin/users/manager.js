document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm-delete') || 'Remove this user?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
