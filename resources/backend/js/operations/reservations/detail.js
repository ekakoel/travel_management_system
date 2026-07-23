(function () {
    'use strict';

    var page = document.querySelector('.main-container');

    if (!page) {
        return;
    }

    page.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-confirm-delete]');

        if (!trigger) {
            return;
        }

        var message = trigger.getAttribute('data-confirm-delete') || 'Are you sure?';

        if (!window.confirm(message)) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
}());
