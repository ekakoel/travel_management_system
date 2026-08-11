const { createFormSubmissionGuard } = require('../../components/form-submission-guard');

document.addEventListener('DOMContentLoaded', () => {
    const orderForm = document.querySelector('[data-activity-order-form]');

    if (!orderForm) {
        return;
    }

    const modalElement = document.getElementById('activityOrderModal');
    const guestInput = orderForm.querySelector('input[name="number_of_guests"]');
    const travelDateInput = orderForm.querySelector('input[name="travel_date"]');
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
    const guestTableBody = orderForm.querySelector('[data-activity-guest-table-body]');
    const guestEmptyRow = orderForm.querySelector('[data-activity-guest-empty-row]');
    const guestInputsTarget = orderForm.querySelector('[data-activity-guest-inputs]');
    const guestProgressTarget = orderForm.querySelector('[data-activity-guest-progress]');
    const guestEditIndexInput = orderForm.querySelector('[data-activity-guest-edit-index]');
    const guestSaveButton = orderForm.querySelector('[data-activity-guest-save]');
    const guestCancelButton = orderForm.querySelector('[data-activity-guest-cancel]');
    const guestFieldElements = {
        name: orderForm.querySelector('[data-activity-guest-field="name"]'),
        phone: orderForm.querySelector('[data-activity-guest-field="phone"]'),
        age: orderForm.querySelector('[data-activity-guest-field="age"]'),
        sex: orderForm.querySelector('[data-activity-guest-field="sex"]'),
        is_leader: orderForm.querySelector('[data-activity-guest-field="is_leader"]'),
    };
    const pricePerPaxTarget = orderForm.querySelector('[data-activity-order-price="per_pax"]');
    const guestCountPriceTarget = orderForm.querySelector('[data-activity-order-price="guest_count"]');
    const promotionDiscountTarget = orderForm.querySelector('[data-activity-order-price="promotion_discount"]');
    const finalPriceTargets = [...orderForm.querySelectorAll('[data-activity-order-price="final_total"]')];
    const priceStatusTarget = orderForm.querySelector('[data-activity-order-price-status]');
    const promotionRow = orderForm.querySelector('[data-activity-order-promotion-row]');

    const quoteUrl = orderForm.dataset.quoteUrl || '';
    const csrfToken = orderForm.querySelector('input[name="_token"]')?.value || '';
    const capacity = Number(orderForm.dataset.capacity || 0);
    const currencyCode = orderForm.dataset.currencyCode || 'USD';
    const locale = (orderForm.dataset.locale || document.documentElement.lang || 'en-US').replace('_', '-');
    const currencySymbols = {
        USD: '$',
        IDR: 'Rp',
        TWD: 'NT$',
        CNY: '¥',
    };
    const submissionGuard = createFormSubmissionGuard(orderForm, {
        storageKey: `activity-order:${window.location.pathname}`,
    });

    const leaderLabel = orderForm.dataset.leaderLabel || 'Leader';
    const setLeaderLabel = orderForm.dataset.setLeaderLabel || 'Set leader';
    const leaderPhoneRequiredLabel = orderForm.dataset.leaderPhoneRequiredLabel || 'Phone required';
    const guestLabel = orderForm.dataset.guestLabel || 'Guest';
    const paxLabel = orderForm.dataset.paxLabel || 'pax';
    const adultLabel = orderForm.dataset.adultLabel || 'Adult';
    const childLabel = orderForm.dataset.childLabel || 'Child';
    const maleLabel = orderForm.dataset.maleLabel || 'Male';
    const femaleLabel = orderForm.dataset.femaleLabel || 'Female';
    const phoneLabel = orderForm.dataset.phoneLabel || 'Phone';
    const guestSingularLabel = orderForm.dataset.guestSingularLabel || 'Guest';
    const guestPluralLabel = orderForm.dataset.guestPluralLabel || 'Guests';
    const reviewEmptyLabel = orderForm.dataset.reviewEmptyLabel || 'No guest details added yet.';
    const tableNoLabel = orderForm.dataset.tableNoLabel || 'No';
    const tableNameLabel = orderForm.dataset.tableNameLabel || 'Name';
    const tableAgeCategoryLabel = orderForm.dataset.tableAgeCategoryLabel || 'Age Category';
    const tableGenderLabel = orderForm.dataset.tableGenderLabel || 'Gender';
    const tablePhoneNumberLabel = orderForm.dataset.tablePhoneNumberLabel || 'Phone Number';
    const tableLeaderLabel = orderForm.dataset.tableLeaderLabel || leaderLabel;
    const guestProgressLabel = orderForm.dataset.guestProgressLabel || ':count / :total guests added';
    const guestCountMismatchLabel = orderForm.dataset.guestCountMismatchLabel || 'Number of guests does not match the guest detail rows.';
    const guestLimitReachedLabel = orderForm.dataset.guestLimitReachedLabel || 'You have reached the selected guest count.';
    const guestTableEmptyLabel = orderForm.dataset.guestTableEmptyLabel || reviewEmptyLabel;
    const editLabel = orderForm.dataset.editLabel || 'Edit';
    const removeLabel = orderForm.dataset.removeLabel || 'Remove';
    const addGuestLabel = orderForm.dataset.addGuestLabel || 'Add';
    const updateGuestLabel = orderForm.dataset.updateGuestLabel || 'Update';
    const cancelEditLabel = orderForm.dataset.cancelEditLabel || 'Cancel';
    const priceUnavailableLabel = orderForm.dataset.priceUnavailableLabel || 'Activity pricing is not available.';
    const priceLoadingLabel = orderForm.dataset.priceLoadingLabel || 'Processing';
    const initialStep = Number(orderForm.dataset.initialStep || 0);

    let activeStep = 0;
    let isSubmitting = false;
    let guests = [];
    let quoteRequestController = null;
    let quoteRequestTimer = null;

    try {
        guests = JSON.parse(orderForm.dataset.initialGuests || '[]')
            .filter((guest) => Object.values(guest || {}).some((value) => value !== null && value !== ''))
            .map((guest) => ({
                name: String(guest.name || '').trim(),
                phone: String(guest.phone || '').trim(),
                age: String(guest.age || '').trim(),
                sex: String(guest.sex || '').trim(),
                is_leader: Boolean(Number(guest.is_leader || 0)),
            }));
    } catch (error) {
        guests = [];
    }

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
    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

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

    const focusFirstInvalidField = (container) => {
        const invalidField = container?.querySelector('.is-invalid, :invalid');

        if (invalidField && typeof invalidField.focus === 'function') {
            invalidField.focus({ preventScroll: false });
        }
    };

    const createEmptyGuest = (overrides = {}) => ({
        name: '',
        phone: '',
        age: '',
        sex: '',
        is_leader: false,
        ...overrides,
    });

    const localizeGuestAge = (value) => {
        if (value === 'Adult') {
            return adultLabel;
        }

        if (value === 'Child') {
            return childLabel;
        }

        return value;
    };

    const localizeGuestSex = (value) => {
        if (value === 'Male') {
            return maleLabel;
        }

        if (value === 'Female') {
            return femaleLabel;
        }

        return value;
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

    const getEditingIndex = () => {
        const rawValue = guestEditIndexInput?.value || '';
        return rawValue === '' ? null : Number(rawValue);
    };

    const setEditingIndex = (index) => {
        if (guestEditIndexInput) {
            guestEditIndexInput.value = Number.isInteger(index) ? String(index) : '';
        }
    };

    const clearGuestFormErrors = () => {
        Object.values(guestFieldElements).forEach((field) => {
            field?.classList.remove('is-invalid');
        });
    };

    const resetGuestForm = () => {
        clearGuestFormErrors();
        Object.entries(guestFieldElements).forEach(([key, field]) => {
            if (!field) {
                return;
            }

            if (key === 'is_leader') {
                field.checked = false;
                return;
            }

            field.value = '';
        });

        setEditingIndex(null);

        if (guestSaveButton) {
            guestSaveButton.textContent = addGuestLabel;
        }

        if (guestCancelButton) {
            guestCancelButton.textContent = cancelEditLabel;
            guestCancelButton.hidden = true;
        }
    };

    const fillGuestForm = (guest = {}) => {
        if (guestFieldElements.name) guestFieldElements.name.value = guest.name || '';
        if (guestFieldElements.phone) guestFieldElements.phone.value = guest.phone || '';
        if (guestFieldElements.age) guestFieldElements.age.value = guest.age || '';
        if (guestFieldElements.sex) guestFieldElements.sex.value = guest.sex || '';
        if (guestFieldElements.is_leader) guestFieldElements.is_leader.checked = Boolean(guest.is_leader);
    };

    const getGuestDraft = () => ({
        name: String(guestFieldElements.name?.value || '').trim(),
        phone: String(guestFieldElements.phone?.value || '').trim(),
        age: String(guestFieldElements.age?.value || '').trim(),
        sex: String(guestFieldElements.sex?.value || '').trim(),
        is_leader: Boolean(guestFieldElements.is_leader?.checked),
    });

    const validateGuestDraft = (showFocus = false) => {
        const draft = getGuestDraft();
        const requiredFields = ['name', 'age', 'sex'];
        let isValid = true;
        let firstInvalidField = null;

        clearGuestFormErrors();

        requiredFields.forEach((fieldName) => {
            const field = guestFieldElements[fieldName];
            const hasValue = Boolean(draft[fieldName]);

            if (!hasValue && field) {
                field.classList.add('is-invalid');
                firstInvalidField = firstInvalidField || field;
                isValid = false;
            }
        });

        if (draft.is_leader && !draft.phone && guestFieldElements.phone) {
            guestFieldElements.phone.classList.add('is-invalid');
            firstInvalidField = firstInvalidField || guestFieldElements.phone;
            isValid = false;
        }

        if (!isValid && showFocus && firstInvalidField) {
            firstInvalidField.focus();
        }

        return isValid;
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

    const renderGuestHiddenInputs = () => {
        if (!guestInputsTarget) {
            return;
        }

        guestInputsTarget.innerHTML = guests.map((guest, index) => `
            <input type="hidden" name="guests[${index}][name]" value="${escapeHtml(guest.name)}">
            <input type="hidden" name="guests[${index}][phone]" value="${escapeHtml(guest.phone)}">
            <input type="hidden" name="guests[${index}][age]" value="${escapeHtml(guest.age)}">
            <input type="hidden" name="guests[${index}][sex]" value="${escapeHtml(guest.sex)}">
            <input type="hidden" name="guests[${index}][is_leader]" value="${guest.is_leader ? '1' : '0'}">
        `).join('');
    };

    const renderGuestProgress = () => {
        if (!guestProgressTarget) {
            return;
        }

        const requestedGuestCount = getRequestedGuestCount({ allowIncomplete: true }) ?? 0;
        guestProgressTarget.textContent = guestProgressLabel
            .replace(':count', String(guests.length))
            .replace(':total', String(requestedGuestCount));
    };

    const renderGuestTable = () => {
        if (!guestTableBody) {
            return;
        }

        if (guestEmptyRow) {
            guestEmptyRow.hidden = guests.length > 0;
        }

        guestTableBody.querySelectorAll('[data-activity-guest-row]').forEach((row) => {
            row.remove();
        });

        guests.forEach((guest, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-activity-guest-row', 'true');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${escapeHtml(guest.name || `${guestLabel} ${index + 1}`)}</td>
                <td>${escapeHtml(localizeGuestAge(guest.age) || '-')}</td>
                <td>${escapeHtml(localizeGuestSex(guest.sex) || '-')}</td>
                <td>${escapeHtml(guest.phone || '-')}</td>
                <td>${guest.is_leader ? escapeHtml(leaderLabel) : '-'}</td>
                <td>
                    <div class="activity-reservation-guest-table__actions">
                        <button type="button" class="activity-reservation-guest-table__action" data-activity-guest-edit="${index}">
                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            <span>${escapeHtml(editLabel)}</span>
                        </button>
                        <button type="button" class="activity-reservation-guest-table__action activity-reservation-guest-table__action--danger" data-activity-guest-remove="${index}">
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            <span>${escapeHtml(removeLabel)}</span>
                        </button>
                    </div>
                </td>
            `;
            guestTableBody.appendChild(row);
        });

        if (guestEmptyRow) {
            const emptyCell = guestEmptyRow.querySelector('td');
            if (emptyCell) {
                emptyCell.textContent = guestTableEmptyLabel;
            }
        }

        renderGuestHiddenInputs();
        renderGuestProgress();
    };

    const renderUnavailablePrice = (message = priceUnavailableLabel) => {
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

        if (!quoteUrl || guestCount < Number(guestInput?.min || 1) || (capacity > 0 && guestCount > capacity)) {
            renderUnavailablePrice();
            return;
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
                    travel_date: travelDateInput?.value || '',
                }).toString(),
                signal: quoteRequestController.signal,
            });
            const payload = await response.json();

            if (!response.ok || payload.price_available !== true || !payload.display) {
                renderUnavailablePrice(payload.message || priceUnavailableLabel);
                return;
            }

            if (pricePerPaxTarget) pricePerPaxTarget.textContent = formatCurrency(payload.display.unit_price_usd);
            if (promotionDiscountTarget) promotionDiscountTarget.textContent = `- ${formatCurrency(payload.display.discount_total_usd)}`;
            if (promotionRow) promotionRow.hidden = Number(payload.quote?.discount_total_usd_minor || 0) <= 0;
            finalPriceTargets.forEach((target) => {
                target.textContent = formatCurrency(payload.display.final_total_usd);
            });
            if (priceStatusTarget) priceStatusTarget.textContent = '';
            if (submitButton && !isSubmitting) submitButton.disabled = false;
        } catch (error) {
            if (error.name !== 'AbortError') {
                renderUnavailablePrice();
            }
        }
    };

    const updatePriceSummary = () => {
        const guestCount = Math.max(Number(guestInput?.value || 0), 0);

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

        if (!guests.length) {
            guestManifestTableTarget.innerHTML = `<div class="activity-reservation-guest-summary__empty">${escapeHtml(reviewEmptyLabel)}</div>`;
            return;
        }

        const rows = guests.map((guest, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHtml(guest.name || `${guestLabel} ${index + 1}`)}</td>
                <td>${escapeHtml(localizeGuestAge(guest.age) || '-')}</td>
                <td>${escapeHtml(localizeGuestSex(guest.sex) || '-')}</td>
                <td>${escapeHtml(guest.phone || '-')}</td>
                <td>${guest.is_leader ? escapeHtml(leaderLabel) : '-'}</td>
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
                            <th>${escapeHtml(tableLeaderLabel)}</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    };

    const updateReview = () => {
        const adultCount = guests.filter((guest) => guest.age === 'Adult').length;
        const childCount = guests.filter((guest) => guest.age === 'Child').length;
        const leader = guests.find((guest) => guest.is_leader);
        const guestManifest = guests.length
            ? `${guests.length} ${guests.length > 1 ? guestPluralLabel : guestSingularLabel}: ` + guests.map((guest, index) => {
                const name = guest.name || `${guestLabel} ${index + 1}`;
                const parts = [localizeGuestAge(guest.age), localizeGuestSex(guest.sex), guest.phone ? `${phoneLabel}: ${guest.phone}` : null, guest.is_leader ? leaderLabel : null]
                    .filter(Boolean)
                    .join(' - ');
                return parts ? `${name} (${parts})` : name;
            }).join(', ')
            : '-';

        const valueMap = {
            activity: modalElement?.dataset.activityName || document.title,
            supplier: modalElement?.dataset.activitySupplier || '-',
            travel_date: formatDateTime(travelDateInput?.value || ''),
            number_of_guests: `${guestInput?.value || 0} ${paxLabel}`,
            leader: leader ? `${leader.name}${leader.phone ? ` (${leader.phone})` : ''}` : '-',
            adult_count: `${adultCount} ${adultLabel}`,
            child_count: `${childCount} ${childLabel}`,
            guest_manifest: guestManifest,
        };

        reviewTargets.forEach((target) => {
            const key = target.dataset.activityOrderReview;
            target.textContent = valueMap[key] || '-';
        });

        renderGuestManifestTable();
        renderGuestProgress();
    };

    const validateGuestManifest = (showMessage = false) => {
        const leader = guests.find((guest) => guest.is_leader && guest.phone);
        let message = '';

        if (!guests.length) {
            message = guestCountMismatchLabel;
        } else if (!leader) {
            message = leaderPhoneRequiredLabel;
        }

        setGuestErrorMessage(message, showMessage && Boolean(message));

        return message === '';
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

        if (activeStep === stepPanels.length - 1) {
            updateReview();
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

    const validateStep = (stepIndex, focusInvalid = true) => {
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

        if (panel.querySelector('[data-activity-guest-table-body]') && !validateGuestManifest(true)) {
            isValid = false;
        }

        if (!isValid && focusInvalid) {
            focusFirstInvalidField(panel);
        }

        return isValid;
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
            // Keep the overlay outside the transformed modal so it covers the viewport.
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

    const attemptSubmit = () => {
        if (isSubmitting) {
            return;
        }

        for (let index = 0; index < stepPanels.length; index += 1) {
            if (!validateStep(index, false)) {
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

    const persistGuestDraft = () => {
        if (!validateGuestDraft(true)) {
            return;
        }

        const editingIndex = getEditingIndex();

        const draft = getGuestDraft();

        if (draft.is_leader) {
            guests = guests.map((guest) => ({ ...guest, is_leader: false }));
        }

        if (editingIndex !== null && guests[editingIndex]) {
            guests[editingIndex] = draft;
        } else {
            guests.push(draft);
        }

        resetGuestForm();
        setGuestErrorMessage('', false);
        renderGuestTable();
        updateReview();
        validateGuestManifest(false);
    };

    orderForm.addEventListener('input', (event) => {
        if (event.target.matches('input, textarea, select')) {
            event.target.classList.remove('is-invalid');
            updateReview();
        }
    });

    guestSaveButton?.addEventListener('click', persistGuestDraft);

    guestCancelButton?.addEventListener('click', () => {
        resetGuestForm();
        setGuestErrorMessage('', false);
    });

    guestTableBody?.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-activity-guest-edit]');
        const removeButton = event.target.closest('[data-activity-guest-remove]');

        if (editButton) {
            const index = Number(editButton.dataset.activityGuestEdit || -1);

            if (!Number.isInteger(index) || !guests[index]) {
                return;
            }

            setEditingIndex(index);
            fillGuestForm(guests[index]);
            clearGuestFormErrors();

            if (guestSaveButton) {
                guestSaveButton.textContent = updateGuestLabel;
            }

            if (guestCancelButton) {
                guestCancelButton.hidden = false;
                guestCancelButton.textContent = cancelEditLabel;
            }

            guestFieldElements.name?.focus();
            return;
        }

        if (removeButton) {
            const index = Number(removeButton.dataset.activityGuestRemove || -1);

            if (!Number.isInteger(index) || !guests[index]) {
                return;
            }

            guests.splice(index, 1);

            if (getEditingIndex() === index) {
                resetGuestForm();
            }

            renderGuestTable();
            updateReview();
            validateGuestManifest(false);
        }
    });

    guestInput?.addEventListener('input', () => {
        renderGuestProgress();
        updateReview();
        updatePriceSummary();
        validateGuestManifest(false);
    });

    guestInput?.addEventListener('change', () => {
        renderGuestProgress();
        updateReview();
        updatePriceSummary();
        validateGuestManifest(false);
    });

    travelDateInput?.addEventListener('change', () => {
        updateReview();
        updatePriceSummary();
    });

    nextButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!validateStep(activeStep)) {
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
        item.addEventListener('click', () => {
            const targetStep = Number(item.dataset.activityOrderNav || 0);

            if (targetStep <= activeStep) {
                showStep(targetStep);
                return;
            }

            for (let index = activeStep; index < targetStep; index += 1) {
                if (!validateStep(index, false)) {
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

    resetGuestForm();
    renderGuestTable();
    updateReview();
    updatePriceSummary();
    showStep(Number.isFinite(initialStep) ? initialStep : 0);
});
