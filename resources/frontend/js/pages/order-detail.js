(function () {
    'use strict';

    function initDokuButtons() {
        document.querySelectorAll('[data-doku-checkout-url]').forEach(function (button) {
            button.addEventListener('click', function () {
                var paymentUrl = button.getAttribute('data-doku-checkout-url');

                if (!paymentUrl) {
                    return;
                }

                if (typeof window.loadJokulCheckout === 'function') {
                    window.loadJokulCheckout(paymentUrl);
                    return;
                }

                window.location.href = paymentUrl;
            });
        });
    }

    function initReceiptPreview() {
        document.querySelectorAll('[data-receipt-input]').forEach(function (input) {
            var targetSelector = input.getAttribute('data-receipt-input');
            var target = targetSelector ? document.querySelector(targetSelector) : null;

            if (!target) {
                return;
            }

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];

                if (!file || !file.type || !file.type.match(/^image\//)) {
                    target.innerHTML = '<span>' + (input.getAttribute('data-receipt-empty') || 'No preview available') + '</span>';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    target.innerHTML = '<img src="' + event.target.result + '" alt="Receipt preview">';
                };
                reader.readAsDataURL(file);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDokuButtons();
        initReceiptPreview();
    });
}());
