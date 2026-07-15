const { createFormSubmissionGuard } = require('../../components/form-submission-guard');

(function () {
    'use strict';

    function formatUsd(value) {
        return '$' + Number(value || 0).toLocaleString('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('[data-order-edit-form]');

        if (!form) {
            return;
        }

        var rates = [];

        try {
            rates = JSON.parse(form.dataset.rates || '[]');
        } catch (error) {
            rates = [];
        }

        var guestInput = form.querySelector('[data-order-edit-guests]');
        var submitButton = form.querySelector('[data-order-edit-submit]');
        var submitOverlay = form.querySelector('[data-form-submit-overlay]');
        var processingLabel = form.dataset.processingLabel || 'Processing...';
        var submissionGuard = createFormSubmissionGuard(form, {
            storageKey: form.dataset.submissionKey || ('tour-order-edit:' + window.location.pathname),
        });
        var reviewMap = {
            travelDate: document.querySelector('[data-order-edit-summary-date]'),
            guestCount: document.querySelector('[data-order-edit-summary-guests]'),
            pickup: document.querySelector('[data-order-edit-summary-pickup]'),
            dropoff: document.querySelector('[data-order-edit-summary-dropoff]'),
            total: document.querySelector('[data-order-edit-summary-total]'),
        };
        var pricePerPax = form.querySelector('[data-order-edit-price-per-pax]');
        var priceGuests = form.querySelector('[data-order-edit-price-guests]');
        var priceTotal = form.querySelector('[data-order-edit-price-total]');
        var priceNote = form.querySelector('[data-order-edit-price-note]');
        var isSubmitting = false;

        function setSubmittingState(submitting) {
            isSubmitting = Boolean(submitting);
            form.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');
            document.documentElement.classList.toggle('tour-submit-locked', isSubmitting);
            document.body.classList.toggle('tour-submit-locked', isSubmitting);

            if (submitOverlay) {
                if (isSubmitting && submitOverlay.parentElement !== document.body) {
                    document.body.appendChild(submitOverlay);
                }

                submitOverlay.classList.toggle('hidden', !isSubmitting);
                submitOverlay.setAttribute('aria-hidden', isSubmitting ? 'false' : 'true');
            }

            if (submitButton) {
                submitButton.disabled = isSubmitting;
                submitButton.classList.toggle('is-processing', isSubmitting);

                if (!submitButton.dataset.originalHtml) {
                    submitButton.dataset.originalHtml = submitButton.innerHTML;
                }

                submitButton.innerHTML = isSubmitting
                    ? '<span class="booking-submit-button__spinner" aria-hidden="true"></span><span>' + processingLabel + '</span>'
                    : submitButton.dataset.originalHtml;
            }
        }

        function updateReviewSummary() {
            var travelDateField = form.querySelector('[data-order-edit-field="travelDate"]');
            var guestField = form.querySelector('[data-order-edit-field="guestCount"]');
            var pickupField = form.querySelector('[data-order-edit-field="pickup"]');
            var dropoffField = form.querySelector('[data-order-edit-field="dropoff"]');

            if (reviewMap.travelDate) reviewMap.travelDate.textContent = travelDateField && travelDateField.value ? travelDateField.value : '-';
            if (reviewMap.guestCount) reviewMap.guestCount.textContent = guestField && guestField.value ? guestField.value : '-';
            if (reviewMap.pickup) reviewMap.pickup.textContent = pickupField && pickupField.value ? pickupField.value : '-';
            if (reviewMap.dropoff) reviewMap.dropoff.textContent = dropoffField && dropoffField.value ? dropoffField.value : '-';
        }

        function updatePricePreview() {
            var guestCount = Number(guestInput && guestInput.value ? guestInput.value : 0);
            var matchedRate = rates.find(function (rate) {
                return guestCount >= Number(rate.min_qty) && guestCount <= Number(rate.max_qty);
            });

            if (!matchedRate && guestCount >= 2 && rates.length) {
                matchedRate = rates.slice().sort(function (left, right) {
                    return Number(right.max_qty) - Number(left.max_qty);
                })[0];
            }

            if (!matchedRate || guestCount < 2 || guestCount > 200) {
                if (pricePerPax) pricePerPax.textContent = '-';
                if (priceGuests) priceGuests.textContent = guestCount > 0 ? String(guestCount) : '-';
                if (priceTotal) priceTotal.textContent = '-';
                if (reviewMap.total) reviewMap.total.textContent = '-';
                if (priceNote) priceNote.textContent = form.dataset.noRateLabel || 'No matching active rate for this guest count yet.';
                if (submitButton) submitButton.disabled = true;
                return;
            }

            var unitPrice = Number(matchedRate.price || 0);
            var total = unitPrice * guestCount;

            if (pricePerPax) pricePerPax.textContent = formatUsd(unitPrice);
            if (priceGuests) priceGuests.textContent = String(guestCount);
            if (priceTotal) priceTotal.textContent = formatUsd(total);
            if (reviewMap.total) reviewMap.total.textContent = formatUsd(total);
            if (priceNote) priceNote.textContent = '';
            if (submitButton) submitButton.disabled = false;
        }

        form.addEventListener('input', function (event) {
            if (event.target.matches('input, textarea, select')) {
                event.target.classList.remove('is-invalid');
            }

            updateReviewSummary();
            updatePricePreview();
        });

        form.addEventListener('submit', function () {
            if (isSubmitting) {
                return;
            }

            submissionGuard.markSubmitted();
            setSubmittingState(true);
        });

        submissionGuard.bindHistoryRestore(function () {
            setSubmittingState(false);
            window.location.reload();
        });

        updateReviewSummary();
        updatePricePreview();
    });
}());
