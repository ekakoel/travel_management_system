const { createFormSubmissionGuard } = require('../../components/form-submission-guard');

document.addEventListener('DOMContentLoaded', () => {
    const orderForm = document.querySelector('[data-activity-order-form]');

    if (!orderForm) {
        return;
    }

    const modalElement = document.getElementById('activityOrderModal');
    const guestInput = orderForm.querySelector('input[name="number_of_guests"]');
    const travelDateInput = orderForm.querySelector('input[name="travel_date"]');
    const pickupLocationInput = orderForm.querySelector('input[name="pickup_location"]');
    const dropoffLocationInput = orderForm.querySelector('input[name="dropoff_location"]');
    const reviewTargets = [...orderForm.querySelectorAll('[data-activity-order-review]')];
    const guestManifestTableTarget = orderForm.querySelector('[data-activity-order-review-guest-table]');
    const stepPanels = [...orderForm.querySelectorAll('[data-activity-order-step]')];
    const stepNavItems = [...orderForm.querySelectorAll('[data-activity-order-nav]')];
    const previousButtons = [...orderForm.querySelectorAll('[data-activity-order-prev]')];
    const nextButtons = [...orderForm.querySelectorAll('[data-activity-order-next]')];
    const submitButton = orderForm.querySelector('[data-activity-order-submit]');
    const submitOverlay = orderForm.querySelector('[data-activity-order-overlay]');
    const termsCheckbox = orderForm.querySelector('input[name="terms_accepted"]');
    const guestError = orderForm.querySelector('[data-activity-guest-error]');
    const guestListTarget = orderForm.querySelector('[data-activity-guest-list]');
    const manualGuestsTarget = orderForm.querySelector('[data-activity-manual-guests]');
    const uploadPanel = orderForm.querySelector('[data-activity-upload-panel]');
    const guestListInput = orderForm.querySelector('[data-activity-guest-list-input]');
    const guestListStatus = orderForm.querySelector('[data-activity-guest-list-status]');
    const guestModeLabelTarget = orderForm.querySelector('[data-activity-guest-mode-label]');
    const addGuestButton = orderForm.querySelector('[data-activity-add-guest]');
    const guestProgressTarget = orderForm.querySelector('[data-activity-guest-progress]');
    const pricePerPaxTarget = orderForm.querySelector('[data-activity-order-price="per_pax"]');
    const guestCountPriceTarget = orderForm.querySelector('[data-activity-order-price="guest_count"]');
    const promotionDiscountTarget = orderForm.querySelector('[data-activity-order-price="promotion_discount"]');
    const finalPriceTargets = [...orderForm.querySelectorAll('[data-activity-order-price="final_total"]')];
    const priceStatusTarget = orderForm.querySelector('[data-activity-order-price-status]');
    const promotionRow = orderForm.querySelector('[data-activity-order-promotion-row]');

    const quoteUrl = orderForm.dataset.quoteUrl || '';
    const csrfToken = orderForm.querySelector('input[name="_token"]')?.value || '';
    const capacity = Number(orderForm.dataset.capacity || 0);
    const manualGuestThreshold = Number(orderForm.dataset.manualGuestThreshold || 10);
    const currencyCode = orderForm.dataset.currencyCode || 'USD';
    const locale = (orderForm.dataset.locale || document.documentElement.lang || 'en-US').replace('_', '-');
    const submissionGuard = createFormSubmissionGuard(orderForm, {
        storageKey: `activity-order:${window.location.pathname}`,
    });

    const guestLabel = orderForm.dataset.guestLabel || 'Guest';
    const paxLabel = orderForm.dataset.paxLabel || 'pax';
    const adultLabel = orderForm.dataset.adultLabel || 'Adult';
    const childLabel = orderForm.dataset.childLabel || 'Child';
    const maleLabel = orderForm.dataset.maleLabel || 'Male';
    const femaleLabel = orderForm.dataset.femaleLabel || 'Female';
    const phoneLabel = orderForm.dataset.phoneLabel || 'Phone';
    const reviewEmptyLabel = orderForm.dataset.reviewEmptyLabel || 'No guest details added yet.';
    const tableNoLabel = orderForm.dataset.tableNoLabel || 'No';
    const tableNameLabel = orderForm.dataset.tableNameLabel || 'Name';
    const tableAgeCategoryLabel = orderForm.dataset.tableAgeCategoryLabel || 'Age Category';
    const tableGenderLabel = orderForm.dataset.tableGenderLabel || 'Gender';
    const tablePhoneNumberLabel = orderForm.dataset.tablePhoneNumberLabel || 'Phone Number';
    const guestProgressLabel = orderForm.dataset.guestProgressLabel || ':count guest details added for this booking of :total pax';
    const guestCountMismatchLabel = orderForm.dataset.guestCountMismatchLabel || 'Please provide at least 1 guest detail and no more than the selected pax.';
    const guestListRequiredLabel = orderForm.dataset.guestListRequiredLabel || 'Please upload a guest list for bookings above 10 pax.';
    const guestModeManualLabel = orderForm.dataset.guestModeManualLabel || 'Manual guest details';
    const guestModeUploadLabel = orderForm.dataset.guestModeUploadLabel || 'Guest list upload';
    const guestListSelectedLabel = orderForm.dataset.guestListSelectedLabel || 'Selected: :file';
    const guestListReadyLabel = orderForm.dataset.guestListReadyLabel || 'Ready for review';
    const fileSizeLabel = orderForm.dataset.fileSizeLabel || 'File size';
    const priceUnavailableLabel = orderForm.dataset.priceUnavailableLabel || 'Activity pricing is not available.';
    const priceLoadingLabel = orderForm.dataset.priceLoadingLabel || 'Processing';
    const guestListFormatsLabel = guestListStatus?.textContent || '';
    const initialStep = Number(orderForm.dataset.initialStep || 0);
    const currencySymbols = {
        USD: '$',
        IDR: 'Rp',
        TWD: 'NT$',
        CNY: 'CNY ',
    };

    let activeStep = 0;
    let isSubmitting = false;
    let guests = [];
    let quoteRequestController = null;
    let quoteRequestTimer = null;
    let quoteReady = false;

    try {
        guests = JSON.parse(orderForm.dataset.initialGuests || '[]')
            .filter((guest) => Object.values(guest || {}).some((value) => value !== null && value !== ''))
            .map((guest) => ({
                name: String(guest.name || '').trim(),
                age: String(guest.age || '').trim(),
                sex: String(guest.sex || '').trim(),
                phone: String(guest.phone || '').trim(),
            }));
    } catch (error) {
        guests = [];
    }

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

    const formatCurrency = (value) => {
        const amount = Math.max(Number(value) || 0, 0);
        const symbol = currencySymbols[currencyCode];

        if (!symbol) {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currencyCode,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount);
        }

        return `${symbol}${amount.toLocaleString('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    };

    const formatDateTime = (value) => {
        if (!value) {
            return '-';
        }

        const parsed = new Date(value);

        if (Number.isNaN(parsed.getTime())) {
            return value;
        }

        return new Intl.DateTimeFormat(locale, {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        }).format(parsed);
    };

    const getRequestedGuestCount = ({ allowIncomplete = false } = {}) => {
        const minGuests = Number(guestInput?.getAttribute('min') || 1);
        const maxGuests = Number(guestInput?.getAttribute('max') || capacity || 200);
        const rawGuestValue = String(guestInput?.value || '').trim();

        if (allowIncomplete && rawGuestValue === '') {
            return null;
        }

        const parsedGuests = Number(rawGuestValue || minGuests);

        if (!Number.isFinite(parsedGuests)) {
            return allowIncomplete ? null : minGuests;
        }

        return Math.min(Math.max(Math.trunc(parsedGuests), minGuests), maxGuests);
    };

    const isUploadMode = () => getRequestedGuestCount() > manualGuestThreshold;

    const clearGuestErrors = () => {
        guestListTarget?.querySelectorAll('.is-invalid').forEach((field) => {
            field.classList.remove('is-invalid');
        });
        guestListInput?.classList.remove('is-invalid');
    };

    const setGuestErrorMessage = (message = '', visible = false) => {
        if (!guestError) {
            return;
        }

        guestError.hidden = !visible;
        if (visible) {
            guestError.textContent = message;
        }
    };

    const syncGuestStateFromInputs = () => {
        if (!guestListTarget) {
            return;
        }

        guests = [...guestListTarget.querySelectorAll('[data-activity-guest-card]')].map((card) => ({
            name: String(card.querySelector('[data-activity-guest-field="name"]')?.value || '').trim(),
            age: String(card.querySelector('[data-activity-guest-field="age"]')?.value || '').trim(),
            sex: String(card.querySelector('[data-activity-guest-field="sex"]')?.value || '').trim(),
            phone: String(card.querySelector('[data-activity-guest-field="phone"]')?.value || '').trim(),
        }));
    };

    const ensureManualGuestRows = () => {
        const requestedGuestCount = getRequestedGuestCount();

        if (guests.length > requestedGuestCount) {
            guests = guests.slice(0, requestedGuestCount);
        }

        if (guests.length === 0) {
            guests.push({
                name: '',
                age: '',
                sex: '',
                phone: '',
            });
        }
    };

    const formatFileSize = (bytes) => {
        const size = Number(bytes || 0);

        if (size >= 1024 * 1024) {
            return `${(size / (1024 * 1024)).toFixed(2)} MB`;
        }

        return `${Math.max(Math.round(size / 1024), 1)} KB`;
    };

    const setModeFieldsDisabled = () => {
        const uploadMode = isUploadMode();

        manualGuestsTarget?.querySelectorAll('input, select, textarea, button').forEach((field) => {
            field.disabled = uploadMode;
        });

        uploadPanel?.querySelectorAll('input, select, textarea, button, a').forEach((field) => {
            if (field.tagName === 'A') {
                field.setAttribute('aria-disabled', uploadMode ? 'false' : 'true');
                return;
            }

            field.disabled = !uploadMode;
        });

        if (!uploadMode && guestListInput?.value) {
            guestListInput.value = '';
            if (guestListStatus) guestListStatus.textContent = guestListFormatsLabel;
        }
    };

    const updateGuestMode = () => {
        const uploadMode = isUploadMode();

        if (manualGuestsTarget) {
            manualGuestsTarget.hidden = uploadMode;
        }

        if (uploadPanel) {
            uploadPanel.hidden = !uploadMode;
        }

        if (guestModeLabelTarget) {
            guestModeLabelTarget.textContent = uploadMode ? guestModeUploadLabel : guestModeManualLabel;
        }

        setModeFieldsDisabled();
    };

    const renderGuestProgress = () => {
        if (!guestProgressTarget) {
            return;
        }

        const requestedGuestCount = getRequestedGuestCount({ allowIncomplete: true }) ?? 0;

        if (isUploadMode()) {
            guestProgressTarget.textContent = guestListInput?.files?.length
                ? `${guestListInput.files[0].name} · ${fileSizeLabel}: ${formatFileSize(guestListInput.files[0].size)} · ${guestListReadyLabel}`
                : guestListRequiredLabel;
            return;
        }

        const completedGuests = guests.filter((guest) => guest.name && guest.age && guest.sex).length;
        guestProgressTarget.textContent = guestProgressLabel
            .replace(':count', String(completedGuests))
            .replace(':total', String(requestedGuestCount));
    };

    const renderGuestFields = () => {
        if (!guestListTarget) {
            return;
        }

        ensureManualGuestRows();

        guestListTarget.innerHTML = guests.map((guest, index) => {
            const guestNumber = index + 1;

            return `
                <section class="activity-reservation-guest-card" data-activity-guest-card data-activity-guest-index="${index}">
                    <h4>${escapeHtml(guestLabel)} ${guestNumber}</h4>
                    <div class="activity-reservation-guest-card__grid">
                        <div class="activity-reservation-field activity-reservation-field--compact">
                            <label for="activityGuestName${guestNumber}">${escapeHtml(tableNameLabel)} <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                            <input id="activityGuestName${guestNumber}" type="text" name="guests[${index}][name]" class="form-control" value="${escapeHtml(guest.name)}" data-activity-guest-field="name" autocomplete="off" required>
                        </div>
                        <div class="activity-reservation-field activity-reservation-field--compact">
                            <label for="activityGuestAge${guestNumber}">${escapeHtml(tableAgeCategoryLabel)} <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                            <select id="activityGuestAge${guestNumber}" name="guests[${index}][age]" class="form-control" data-activity-guest-field="age" required>
                                <option value="">${escapeHtml(orderForm.dataset.selectLabel || 'Select')}</option>
                                <option value="Adult"${guest.age === 'Adult' ? ' selected' : ''}>${escapeHtml(adultLabel)}</option>
                                <option value="Child"${guest.age === 'Child' ? ' selected' : ''}>${escapeHtml(childLabel)}</option>
                            </select>
                        </div>
                        <div class="activity-reservation-field activity-reservation-field--compact">
                            <label for="activityGuestSex${guestNumber}">${escapeHtml(tableGenderLabel)} <span class="activity-reservation-required" aria-hidden="true">*</span></label>
                            <select id="activityGuestSex${guestNumber}" name="guests[${index}][sex]" class="form-control" data-activity-guest-field="sex" required>
                                <option value="">${escapeHtml(orderForm.dataset.selectLabel || 'Select')}</option>
                                <option value="Male"${guest.sex === 'Male' ? ' selected' : ''}>${escapeHtml(maleLabel)}</option>
                                <option value="Female"${guest.sex === 'Female' ? ' selected' : ''}>${escapeHtml(femaleLabel)}</option>
                            </select>
                        </div>
                        <div class="activity-reservation-field activity-reservation-field--compact">
                            <label for="activityGuestPhone${guestNumber}">${escapeHtml(phoneLabel)}</label>
                            <input id="activityGuestPhone${guestNumber}" type="text" name="guests[${index}][phone]" class="form-control" value="${escapeHtml(guest.phone)}" data-activity-guest-field="phone" autocomplete="off">
                        </div>
                    </div>
                </section>
            `;
        }).join('');

        const requestedGuestCount = getRequestedGuestCount();
        if (addGuestButton) {
            addGuestButton.disabled = guests.length >= requestedGuestCount || isUploadMode();
        }

        updateGuestMode();
        renderGuestProgress();
    };

    const validateGuestManifest = (showMessage = false) => {
        clearGuestErrors();

        if (isUploadMode()) {
            const hasFile = Boolean(guestListInput?.files?.length);
            if (!hasFile) {
                guestListInput?.classList.add('is-invalid');
                setGuestErrorMessage(guestListRequiredLabel, showMessage);
                if (showMessage) guestListInput?.focus();
                return false;
            }

            setGuestErrorMessage('', false);
            return true;
        }

        syncGuestStateFromInputs();

        const requestedGuestCount = getRequestedGuestCount();
        const filledGuests = guests.filter((guest) => guest.name || guest.age || guest.sex || guest.phone);
        let firstInvalidField = null;
        let isValid = filledGuests.length >= 1 && filledGuests.length <= requestedGuestCount;

        [...guestListTarget?.querySelectorAll('[data-activity-guest-card]') || []].forEach((card) => {
            const hasAnyValue = [...card.querySelectorAll('[data-activity-guest-field]')]
                .some((field) => String(field.value || '').trim());

            ['name', 'age', 'sex'].forEach((fieldName) => {
                const field = card.querySelector(`[data-activity-guest-field="${fieldName}"]`);
                if (hasAnyValue && field && !String(field.value || '').trim()) {
                    field.classList.add('is-invalid');
                    firstInvalidField = firstInvalidField || field;
                    isValid = false;
                }
            });
        });

        setGuestErrorMessage(guestCountMismatchLabel, showMessage && !isValid);

        if (showMessage && firstInvalidField) {
            firstInvalidField.focus();
        }

        return isValid;
    };

    const renderUnavailablePrice = (message = priceUnavailableLabel) => {
        quoteReady = false;
        if (pricePerPaxTarget) pricePerPaxTarget.textContent = '-';
        finalPriceTargets.forEach((target) => {
            target.textContent = '-';
        });
        if (promotionRow) promotionRow.hidden = true;
        if (priceStatusTarget) priceStatusTarget.textContent = message;
        if (submitButton) submitButton.disabled = true;
    };

    const requestPriceSummary = async () => {
        const guestCount = Math.max(Number(guestInput?.value || 0), 0);

        if (!quoteUrl || !travelDateInput?.value || guestCount < Number(guestInput?.min || 1) || (capacity > 0 && guestCount > capacity)) {
            renderUnavailablePrice();
            return false;
        }

        quoteRequestController?.abort();
        quoteRequestController = new AbortController();
        renderUnavailablePrice(priceLoadingLabel);

        try {
            const response = await fetch(quoteUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: new URLSearchParams({
                    number_of_guests: String(guestCount),
                    travel_date: travelDateInput.value || '',
                }).toString(),
                signal: quoteRequestController.signal,
            });
            const payload = await response.json();

            if (!response.ok || payload.price_available !== true || !payload.display) {
                renderUnavailablePrice(payload.message || priceUnavailableLabel);
                return false;
            }

            quoteReady = true;
            if (pricePerPaxTarget) pricePerPaxTarget.textContent = formatCurrency(payload.display.unit_price_usd);
            if (promotionDiscountTarget) promotionDiscountTarget.textContent = `- ${formatCurrency(payload.display.discount_total_usd)}`;
            if (promotionRow) promotionRow.hidden = Number(payload.quote?.discount_total_usd_minor || 0) <= 0;
            finalPriceTargets.forEach((target) => {
                target.textContent = formatCurrency(payload.display.final_total_usd);
            });
            if (priceStatusTarget) priceStatusTarget.textContent = '';
            if (submitButton && !isSubmitting) submitButton.disabled = false;
            return true;
        } catch (error) {
            if (error.name !== 'AbortError') {
                renderUnavailablePrice();
            }
            return false;
        }
    };

    const updatePriceSummary = () => {
        const guestCount = Math.max(Number(guestInput?.value || 0), 0);

        quoteReady = false;
        if (guestCountPriceTarget) {
            guestCountPriceTarget.textContent = `${guestCount} ${paxLabel}`;
        }

        window.clearTimeout(quoteRequestTimer);
        quoteRequestTimer = window.setTimeout(requestPriceSummary, 250);
    };

    const renderGuestManifestTable = () => {
        if (!guestManifestTableTarget) {
            return;
        }

        if (isUploadMode()) {
            const fileName = guestListInput?.files?.[0]?.name || '';
            guestManifestTableTarget.innerHTML = fileName
                ? `<div class="activity-reservation-guest-summary__empty">${escapeHtml(getRequestedGuestCount())} ${escapeHtml(paxLabel)} · ${escapeHtml(guestListSelectedLabel.replace(':file', fileName))}</div>`
                : `<div class="activity-reservation-guest-summary__empty">${escapeHtml(guestListRequiredLabel)}</div>`;
            return;
        }

        syncGuestStateFromInputs();

        const filledGuests = guests.filter((guest) => guest.name || guest.age || guest.sex || guest.phone);
        if (!filledGuests.length) {
            guestManifestTableTarget.innerHTML = `<div class="activity-reservation-guest-summary__empty">${escapeHtml(reviewEmptyLabel)}</div>`;
            return;
        }

        const rows = filledGuests.map((guest, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHtml(guest.name || `${guestLabel} ${index + 1}`)}</td>
                <td>${escapeHtml(guest.age || '-')}</td>
                <td>${escapeHtml(guest.sex || '-')}</td>
                <td>${escapeHtml(guest.phone || '-')}</td>
            </tr>
        `).join('');

        guestManifestTableTarget.innerHTML = `
            <div class="activity-reservation-guest-summary__table-wrap">
                <table class="activity-reservation-guest-summary__table">
                    <thead>
                        <tr>
                            <th>${escapeHtml(tableNoLabel)}</th>
                            <th>${escapeHtml(tableNameLabel)}</th>
                            <th>${escapeHtml(tableAgeCategoryLabel)}</th>
                            <th>${escapeHtml(tableGenderLabel)}</th>
                            <th>${escapeHtml(tablePhoneNumberLabel)}</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    };

    const updateReview = () => {
        syncGuestStateFromInputs();

        const valueMap = {
            activity: modalElement?.dataset.activityName || document.title,
            supplier: modalElement?.dataset.activitySupplier || '-',
            travel_date: formatDateTime(travelDateInput?.value || ''),
            number_of_guests: `${guestInput?.value || 0} ${paxLabel}`,
            pickup_location: pickupLocationInput?.value?.trim() || '-',
            dropoff_location: dropoffLocationInput?.value?.trim() || '-',
            guest_information: isUploadMode()
                ? guestModeUploadLabel
                : `${guests.filter((guest) => guest.name || guest.age || guest.sex || guest.phone).length} ${guestLabel}`,
        };

        reviewTargets.forEach((target) => {
            const key = target.dataset.activityOrderReview;
            target.textContent = valueMap[key] || '-';
        });

        renderGuestManifestTable();
        renderGuestProgress();
    };

    const focusFirstInvalidField = (container) => {
        const invalidField = container?.querySelector('.is-invalid, :invalid');

        if (invalidField && typeof invalidField.focus === 'function') {
            invalidField.focus({ preventScroll: false });
        }
    };

    const validateField = (field) => {
        if (!field) {
            return true;
        }

        const isValid = field.checkValidity();
        field.classList.toggle('is-invalid', !isValid);
        return isValid;
    };

    const validateStep = async (stepIndex, focusInvalid = true) => {
        const panel = stepPanels[stepIndex];

        if (!panel) {
            return true;
        }

        let isValid = true;
        const fields = [...panel.querySelectorAll('input, textarea, select')].filter((field) => {
            return field.type !== 'hidden' && !field.disabled;
        });

        fields.forEach((field) => {
            isValid = validateField(field) && isValid;
        });

        if (stepIndex === 0 && isValid && !quoteReady) {
            isValid = await requestPriceSummary();
        }

        if (panel.querySelector('[data-activity-guest-list], [data-activity-upload-panel]') && !validateGuestManifest(true)) {
            isValid = false;
        }

        if (!isValid && focusInvalid) {
            focusFirstInvalidField(panel);
        }

        return isValid;
    };

    const showStep = (stepIndex) => {
        activeStep = Math.min(Math.max(stepIndex, 0), stepPanels.length - 1);

        stepPanels.forEach((panel, index) => {
            const isActive = index === activeStep;
            panel.hidden = !isActive;
            panel.classList.toggle('is-active', isActive);
        });

        stepNavItems.forEach((item, index) => {
            item.classList.toggle('is-active', index === activeStep);
            item.classList.toggle('is-complete', index < activeStep);
        });

        updateGuestMode();

        if (activeStep === 1) {
            renderGuestFields();
        }

        if (activeStep === stepPanels.length - 1) {
            updateReview();
        }
    };

    const setSubmittingState = (submitting) => {
        const processingLabel = submitButton?.dataset.processingLabel || 'Processing...';
        isSubmitting = Boolean(submitting);
        orderForm.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');
        orderForm.toggleAttribute('inert', isSubmitting);
        document.documentElement.classList.toggle('activity-submit-locked', isSubmitting);
        document.body.classList.toggle('activity-submit-locked', isSubmitting);
        document.documentElement.classList.toggle('frontend-order-submit-locked', isSubmitting);
        document.body.classList.toggle('frontend-order-submit-locked', isSubmitting);

        if (submitOverlay) {
            if (isSubmitting && submitOverlay.parentElement !== document.body) {
                document.body.appendChild(submitOverlay);
            }

            submitOverlay.style.setProperty('z-index', '2147483647', 'important');
            submitOverlay.classList.toggle('hidden', !isSubmitting);
            submitOverlay.setAttribute('aria-hidden', isSubmitting ? 'false' : 'true');
        }

        [...previousButtons, ...nextButtons, submitButton]
            .filter(Boolean)
            .forEach((button) => {
                const originalLabel = button.dataset.originalLabel || button.innerHTML;
                button.dataset.originalLabel = originalLabel;
                button.disabled = isSubmitting;
                button.classList.toggle('is-processing', isSubmitting && button === submitButton);
                button.setAttribute('aria-disabled', isSubmitting ? 'true' : 'false');

                if (button === submitButton) {
                    button.innerHTML = isSubmitting
                        ? `<span class="frontend-action-spinner" aria-hidden="true"></span><span>${processingLabel}</span>`
                        : originalLabel;
                }
            });
    };

    const attemptSubmit = async () => {
        if (isSubmitting) {
            return;
        }

        syncGuestStateFromInputs();

        for (let index = 0; index < stepPanels.length; index += 1) {
            const isStepValid = await validateStep(index, false);
            if (!isStepValid) {
                showStep(index);
                focusFirstInvalidField(stepPanels[index]);
                return;
            }
        }

        if (!validateField(termsCheckbox)) {
            showStep(stepPanels.length - 1);
            termsCheckbox?.focus();
            return;
        }

        setSubmittingState(true);
        submissionGuard.markSubmitted();
        HTMLFormElement.prototype.submit.call(orderForm);
    };

    orderForm.addEventListener('input', (event) => {
        if (event.target.matches('input, textarea, select')) {
            event.target.classList.remove('is-invalid');
            syncGuestStateFromInputs();
            updateReview();
        }
    });

    guestInput?.addEventListener('input', () => {
        syncGuestStateFromInputs();
        ensureManualGuestRows();
        renderGuestFields();
        updateReview();
        updatePriceSummary();
        validateGuestManifest(false);
    });

    guestInput?.addEventListener('change', () => {
        syncGuestStateFromInputs();
        ensureManualGuestRows();
        renderGuestFields();
        updateReview();
        updatePriceSummary();
        validateGuestManifest(false);
    });

    addGuestButton?.addEventListener('click', () => {
        syncGuestStateFromInputs();

        if (guests.length >= getRequestedGuestCount() || isUploadMode()) {
            return;
        }

        guests.push({
            name: '',
            age: '',
            sex: '',
            phone: '',
        });
        renderGuestFields();
        updateReview();
    });

    guestListInput?.addEventListener('change', () => {
        guestListInput.classList.remove('is-invalid');

        if (guestListStatus) {
            guestListStatus.textContent = guestListInput.files?.length
                ? `${guestListInput.files[0].name} · ${fileSizeLabel}: ${formatFileSize(guestListInput.files[0].size)} · ${guestListReadyLabel}`
                : guestListFormatsLabel;
        }

        renderGuestProgress();
        updateReview();
    });

    travelDateInput?.addEventListener('change', () => {
        updateReview();
        updatePriceSummary();
    });

    nextButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            if (!await validateStep(activeStep)) {
                return;
            }

            showStep(activeStep + 1);
        });
    });

    previousButtons.forEach((button) => {
        button.addEventListener('click', () => {
            showStep(activeStep - 1);
        });
    });

    stepNavItems.forEach((item) => {
        item.addEventListener('click', async () => {
            const targetStep = Number(item.dataset.activityOrderNav || 0);

            if (targetStep <= activeStep) {
                showStep(targetStep);
                return;
            }

            for (let index = activeStep; index < targetStep; index += 1) {
                if (!await validateStep(index, false)) {
                    showStep(index);
                    focusFirstInvalidField(stepPanels[index]);
                    return;
                }
            }

            showStep(targetStep);
        });
    });

    submitButton?.addEventListener('click', (event) => {
        event.preventDefault();
        attemptSubmit();
    });

    orderForm.addEventListener('submit', (event) => {
        event.preventDefault();
        attemptSubmit();
    });

    modalElement?.addEventListener('hide.bs.modal', (event) => {
        if (isSubmitting) {
            event.preventDefault();
        }
    });

    modalElement?.setAttribute('data-activity-name', modalElement?.dataset.activityName || document.title);
    modalElement?.setAttribute('data-activity-supplier', modalElement?.dataset.activitySupplier || '-');

    if (orderForm.dataset.openOnLoad === 'true' && modalElement && window.bootstrap?.Modal) {
        window.setTimeout(() => {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }, 120);
    }

    submissionGuard.bindHistoryRestore(() => {
        setSubmittingState(false);
        window.location.reload();
    });

    ensureManualGuestRows();
    renderGuestFields();
    updateGuestMode();
    updateReview();
    updatePriceSummary();
    showStep(Number.isFinite(initialStep) ? initialStep : 0);
});
