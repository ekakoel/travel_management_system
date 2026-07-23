document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-terms-delete-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('Delete this policy?')) {
                event.preventDefault();
            }
        });
    });
});
