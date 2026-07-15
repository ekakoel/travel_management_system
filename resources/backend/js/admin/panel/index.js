document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-admin-panel]');

    if (!page) {
        return;
    }

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm') || 'Continue this action?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
