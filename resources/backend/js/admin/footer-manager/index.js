document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-footer-delete-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('Remove this footer link?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('[data-footer-submit]').forEach(function (button) {
                button.disabled = true;
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving';
            });
        });
    });
});
