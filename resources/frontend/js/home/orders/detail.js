(function () {
    'use strict';

    var pageRoot = document.querySelector('.order-detail-page');
    var countdownExpiredText = pageRoot ? (pageRoot.getAttribute('data-countdown-expired') || 'Payment window expired') : 'Payment window expired';
    var countdownRemainingTemplate = pageRoot ? (pageRoot.getAttribute('data-countdown-remaining-template') || ':days d :hours h :minutes m remaining') : ':days d :hours h :minutes m remaining';
    var receiptPreviewAlt = pageRoot ? (pageRoot.getAttribute('data-receipt-preview-alt') || 'Receipt preview') : 'Receipt preview';

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
                    target.innerHTML = '<img src="' + event.target.result + '" alt="' + receiptPreviewAlt + '">';
                };
                reader.readAsDataURL(file);
            });
        });
    }

    function formatCountdown(distance) {
        if (distance <= 0) {
            return countdownExpiredText;
        }

        var totalSeconds = Math.floor(distance / 1000);
        var days = Math.floor(totalSeconds / 86400);
        var hours = Math.floor((totalSeconds % 86400) / 3600);
        var minutes = Math.floor((totalSeconds % 3600) / 60);

        return countdownRemainingTemplate
            .replace(':days', String(days))
            .replace(':hours', String(hours))
            .replace(':minutes', String(minutes));
    }

    function initPaymentCountdown() {
        document.querySelectorAll('[data-payment-countdown]').forEach(function (element) {
            var deadline = element.getAttribute('data-payment-countdown');
            var output = element.querySelector('[data-payment-countdown-output]');

            if (!deadline || !output) {
                return;
            }

            function tick() {
                var distance = new Date(deadline).getTime() - Date.now();
                output.textContent = formatCountdown(distance);

                if (distance <= 0) {
                    element.classList.add('order-detail-payment-deadline--expired');
                }
            }

            tick();
            window.setInterval(tick, 60000);
        });
    }

    function initInvoicePreviewModal() {
        function cleanupStaleTourGalleryState() {
            if (!document.querySelector('.tour-gallery-modal.show')) {
                document.body.classList.remove('tour-gallery-modal-open');
                document.documentElement.classList.remove('tour-gallery-modal-open');
            }
        }

        var activeModal = null;

        document.querySelectorAll('[data-invoice-preview-modal]').forEach(function (modal) {
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
        });

        function setPageLock(locked) {
            document.body.classList.toggle('invoice-preview-modal-open', locked);
            document.documentElement.classList.toggle('invoice-preview-modal-open', locked);
        }

        function closeModal(modal) {
            if (!modal) {
                return;
            }

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            modal.hidden = true;
            setPageLock(false);
            activeModal = null;
        }

        function openModal(modal) {
            if (!modal) {
                return;
            }

            cleanupStaleTourGalleryState();
            if (activeModal && activeModal !== modal) {
                closeModal(activeModal);
            }

            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            modal.classList.add('is-open');
            setPageLock(true);
            activeModal = modal;
            modal.scrollTop = 0;
            window.requestAnimationFrame(function () {
                var closeButton = modal.querySelector('[data-invoice-preview-close]');
                if (closeButton) {
                    closeButton.focus();
                }
            });
        }

        document.querySelectorAll('[data-invoice-preview-modal]').forEach(function (modal) {
            modal.querySelectorAll('[data-invoice-preview-close]').forEach(function (button) {
                button.addEventListener('click', function () {
                    closeModal(modal);
                });
            });
        });

        document.querySelectorAll('[data-invoice-preview-trigger]').forEach(function (trigger) {
            trigger.addEventListener('click', function (event) {
                var targetSelector = trigger.getAttribute('data-invoice-preview-target');
                var target = targetSelector ? document.querySelector(targetSelector) : null;

                if (!target) {
                    return;
                }

                event.preventDefault();
                openModal(target);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && activeModal) {
                closeModal(activeModal);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initReceiptPreview();
        initPaymentCountdown();
        initInvoicePreviewModal();
    });
}());
