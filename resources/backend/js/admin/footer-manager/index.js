document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-footer-delete-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('Remove this footer link?')) {
                event.preventDefault();
            }
        });
    });
});
