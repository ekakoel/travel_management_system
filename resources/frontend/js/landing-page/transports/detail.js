document.addEventListener('DOMContentLoaded', function () {
    var page = document.querySelector('[data-transport-detail-page]');

    if (!page) {
        return;
    }

    var rates = [];
    var bookingDiscount = parseFloat(page.getAttribute('data-transport-booking-discount') || '0');
    var promotionDiscount = parseFloat(page.getAttribute('data-transport-promotion-discount') || '0');
    var defaultRateId = page.getAttribute('data-transport-default-rate-id') || '';
    var shouldOpenModalOnLoad = page.getAttribute('data-transport-booking-open') === 'true';
    var initialErrorStep = parseInt(page.getAttribute('data-transport-booking-error-step') || '1', 10);
    var oldShuttleType = page.getAttribute('data-transport-old-shuttle-type') || 'Arrival';
    var processingLabel = page.getAttribute('data-transport-processing-label') || 'Processing reservation...';
    var submittedWarning = page.getAttribute('data-transport-submitted-warning') || 'This reservation has already been submitted.';
    var flightDateLabel = page.getAttribute('data-transport-flight-date-label') || 'Flight Date';
    var serviceDateLabel = page.getAttribute('data-transport-service-date-label') || 'Service Date';
    var estimatedRentalDurationLabel = page.getAttribute('data-transport-estimated-rental-duration-label') || 'Estimated Duration for Rental Time';
    var estimatedDurationLabel = page.getAttribute('data-transport-estimated-duration-label') || 'Estimated Duration';
    var priceDurationTemplate = page.getAttribute('data-transport-price-duration-template') || 'Transport price duration :hours Hours';
    var guestNameValidationMessage = page.getAttribute('data-transport-validation-guest-name') || 'Please enter at least one guest name.';
    var guestRequiredValidationMessage = page.getAttribute('data-transport-validation-guest-required') || 'Please complete this required guest detail.';
    var flightRequiredValidationMessage = page.getAttribute('data-transport-validation-flight-required') || 'Please complete this required flight detail before continuing to guest details.';
    var serviceRequiredValidationMessage = page.getAttribute('data-transport-validation-service-required') || 'Please complete this required field before continuing to review.';
    var guestLabelTemplate = page.getAttribute('data-transport-guest-label-template') || 'Guest :number';
    var labelName = page.getAttribute('data-transport-label-name') || 'Name';
    var labelAgeCategory = page.getAttribute('data-transport-label-age-category') || 'Age Category';
    var labelGender = page.getAttribute('data-transport-label-gender') || 'Gender';
    var labelPhone = page.getAttribute('data-transport-label-phone') || 'Phone Number';
    var labelOptional = page.getAttribute('data-transport-label-optional') || 'Optional';
    var placeholderGuestName = page.getAttribute('data-transport-placeholder-guest-name') || 'Guest name';
    var placeholderPhone = page.getAttribute('data-transport-placeholder-phone') || 'Phone number';
    var selectGenderLabel = page.getAttribute('data-transport-select-gender') || 'Select gender';
    var adultLabel = page.getAttribute('data-transport-adult-label') || 'Adult';
    var childLabel = page.getAttribute('data-transport-child-label') || 'Child';
    var maleLabel = page.getAttribute('data-transport-male-label') || 'Male';
    var femaleLabel = page.getAttribute('data-transport-female-label') || 'Female';
    var paxLabel = page.getAttribute('data-transport-pax-label') || 'pax';

    var sidebarForm = page.querySelector('[data-transport-reservation-form]');
    var sidebarTypeSelect = page.querySelector('[data-transport-price-type]');
    var sidebarDestinationGroup = page.querySelector('[data-transport-destination-group]');
    var sidebarDestinationSelect = page.querySelector('[data-transport-price-destination]');
    var sidebarPriceTarget = page.querySelector('[data-selected-rate-price]');
    var sidebarRouteTarget = page.querySelector('[data-selected-rate-route]');
    var sidebarDurationTarget = page.querySelector('[data-selected-rate-duration]');
    var cards = Array.from(page.querySelectorAll('[data-rate-card]'));
    var groups = Array.from(page.querySelectorAll('[data-rate-group]'));

    var modal = page.querySelector('[data-transport-reservation-modal]');
    var openModalButton = page.querySelector('[data-open-transport-reservation]');
    var closeModalButtons = Array.from(page.querySelectorAll('[data-close-transport-reservation]'));
    var modalForm = page.querySelector('[data-transport-booking-form]');
    var agentSelect = page.querySelector('#transportAgent');
    var orderNumberInput = page.querySelector('[data-transport-order-number-input]');
    var modalPriceTarget = page.querySelector('[data-modal-selected-rate-price]');
    var modalRouteTarget = page.querySelector('[data-modal-selected-rate-route]');
    var modalRouteCopyTarget = page.querySelector('[data-modal-selected-rate-route-copy]');
    var modalDurationLabelTarget = page.querySelector('[data-modal-selected-rate-duration-label]');
    var modalDurationTarget = page.querySelector('[data-modal-selected-rate-duration]');
    var modalExtraTarget = page.querySelector('[data-modal-selected-rate-extra]');
    var modalTypeTarget = page.querySelector('[data-modal-selected-rate-type]');
    var modalRateNote = page.querySelector('[data-modal-selected-rate-note]');
    var selectedPriceIdInput = page.querySelector('[data-selected-transport-price-id]');
    var durationInput = page.querySelector('[data-transport-duration-input]');
    var durationField = page.querySelector('[data-transport-duration]');
    var serviceDateInput = page.querySelector('[data-transport-service-date]');
    var flightDateInput = page.querySelector('#flight_date');
    var transportDateLabel = page.querySelector('[data-transport-date-label]');
    var shuttleTypeGroup = page.querySelector('[data-modal-shuttle-type-group]');
    var shuttleTypeSelect = page.querySelector('[data-modal-airport-shuttle-type]');
    var dailyRentFields = page.querySelector('[data-modal-daily-rent-fields]');
    var dailyRentLocationFields = Array.from(page.querySelectorAll('[data-modal-daily-rent-location-fields]'));
    var modalFlightGrid = page.querySelector('[data-modal-flight-grid]');
    var airportFlightFields = Array.from(page.querySelectorAll('[data-modal-airport-flight-fields]'));
    var guestList = page.querySelector('[data-transport-guest-list]');
    var addGuestButton = page.querySelector('[data-add-transport-guest]');
    var reviewService = page.querySelector('[data-review-service]');
    var reviewOrderNumber = page.querySelector('[data-review-order-number]');
    var reviewServiceDate = page.querySelector('[data-review-service-date]');
    var reviewRoute = page.querySelector('[data-review-route]');
    var reviewRouteCard = page.querySelector('[data-review-route-card]');
    var reviewFlightTypeCard = page.querySelector('[data-review-flight-type-card]');
    var reviewFlightType = page.querySelector('[data-review-flight-type]');
    var reviewFlightNumberCard = page.querySelector('[data-review-flight-number-card]');
    var reviewFlightNumber = page.querySelector('[data-review-flight-number]');
    var reviewPickupCard = page.querySelector('[data-review-pickup-card]');
    var reviewDropoffCard = page.querySelector('[data-review-dropoff-card]');
    var reviewPickupLocation = page.querySelector('[data-review-pickup-location]');
    var reviewDropoffLocation = page.querySelector('[data-review-dropoff-location]');
    var reviewGuestsTotal = page.querySelector('[data-review-guests-total]');
    var reviewGuestsAdult = page.querySelector('[data-review-guests-adult]');
    var reviewGuestsChild = page.querySelector('[data-review-guests-child]');
    var reviewGuestTableBody = page.querySelector('[data-review-guest-table-body]');
    var reviewGuestEmpty = page.querySelector('[data-review-guest-empty]');
    var reviewTotal = page.querySelector('[data-review-total]');
    var reviewBasePrice = page.querySelector('[data-review-base-price]');
    var reviewFinalPrice = page.querySelector('[data-review-final-price]');
    var reviewBookingDiscountRow = page.querySelector('[data-review-booking-discount-row]');
    var reviewPromotionDiscountRow = page.querySelector('[data-review-promotion-discount-row]');
    var submitOverlay = page.querySelector('[data-transport-submit-overlay]');
    var termsCheckbox = page.querySelector('input[name="terms_accepted"]');
    var wizardPanels = Array.from(page.querySelectorAll('[data-wizard-panel]'));
    var wizardSteps = Array.from(page.querySelectorAll('[data-wizard-step-target]'));
    var wizardNextButtons = Array.from(page.querySelectorAll('[data-wizard-next]'));
    var tripReviewButton = page.querySelector('[data-wizard-next-review]');
    var wizardPrevButtons = Array.from(page.querySelectorAll('[data-wizard-prev]'));
    var submitButtons = modalForm ? Array.from(modalForm.querySelectorAll('button[type="submit"]')) : [];

    var activeStep = 1;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getGuestLabel(index) {
        return guestLabelTemplate.replace(':number', String(index + 1));
    }
    var selectedRate = null;
    var isSubmitting = false;

    try {
        rates = JSON.parse(page.getAttribute('data-transport-rates') || '[]');
    } catch (error) {
        rates = [];
    }

    if (!rates.length || !sidebarForm || !sidebarTypeSelect) {
        return;
    }

    function formatCurrency(amount) {
        return amount.toLocaleString('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function setBodyScrollLock(locked) {
        document.documentElement.classList.toggle('transport-modal-open', locked);
        document.body.classList.toggle('transport-modal-open', locked);
    }

    function getRatesByType(type) {
        return rates.filter(function (rate) {
            return rate.type === type;
        });
    }

    function getUniqueDestinations(type) {
        var typeRates = getRatesByType(type);
        var destinations = [];

        typeRates.forEach(function (rate) {
            if (destinations.indexOf(rate.dst) === -1) {
                destinations.push(rate.dst);
            }
        });

        return destinations;
    }

    function getFirstRateByTypeAndDestination(type, destination) {
        return rates.find(function (rate) {
            return rate.type === type && (type !== 'Airport Shuttle' || rate.dst === destination);
        });
    }

    function getRateById(rateId) {
        return rates.find(function (rate) {
            return String(rate.id) === String(rateId);
        });
    }

    function setSelectOptions(select, options, selectedValue) {
        if (!select) {
            return;
        }

        select.innerHTML = '';

        options.forEach(function (optionValue) {
            var option = document.createElement('option');
            option.value = optionValue;
            option.textContent = optionValue;
            if (String(optionValue) === String(selectedValue)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    function syncDestinationControls(type, destination) {
        var shouldShowDestination = type === 'Airport Shuttle';
        var destinations = getUniqueDestinations(type);
        var resolvedDestination = destination || destinations[0] || '';

        if (sidebarDestinationGroup && sidebarDestinationSelect) {
            sidebarDestinationGroup.hidden = !shouldShowDestination;
            setSelectOptions(sidebarDestinationSelect, destinations, resolvedDestination);
        }

    }

    function setVisibleRateCards(type, destination) {
        cards.forEach(function (card) {
            var matchesType = card.getAttribute('data-rate-type') === type;
            var matchesDestination = type !== 'Airport Shuttle' || card.getAttribute('data-rate-dst') === destination;

            card.hidden = !(matchesType && matchesDestination);
        });

        groups.forEach(function (group) {
            var visibleCards = Array.from(group.querySelectorAll('[data-rate-card]')).filter(function (card) {
                return !card.hidden;
            });

            group.hidden = group.getAttribute('data-rate-group-type') !== type || visibleCards.length === 0;
        });
    }

    function getCurrentDuration() {
        var durationSource = durationField && !durationField.closest('[hidden]')
            ? durationField.value
            : (durationInput ? durationInput.value : '');
        var duration = parseInt(durationSource || '1', 10);

        if (Number.isNaN(duration) || duration < 1) {
            duration = 1;
        }

        return duration;
    }

    function syncDurationInput() {
        if (!durationInput || !selectedRate) {
            return;
        }

        if (selectedRate.type === 'Daily Rent') {
            durationInput.value = String(getCurrentDuration());
            return;
        }

        durationInput.value = String(Math.max(selectedRate.durationValue || 1, 1));
    }

    function getDisplayedDurationLabel(rate) {
        if (!rate) {
            return '-';
        }

        if (rate.type !== 'Daily Rent') {
            return rate.durationLabel || '-';
        }

        if (!rate.durationValue) {
            return '-';
        }

        return priceDurationTemplate.replace(':hours', rate.durationValue);
    }

    function hasSelectedRatePrice() {
        return !!(selectedRate && selectedRate.hasPrice);
    }

    function calculateBaseTotal() {
        if (!selectedRate || !hasSelectedRatePrice()) {
            return 0;
        }

        return selectedRate.type === 'Daily Rent'
            ? selectedRate.priceValue * getCurrentDuration()
            : selectedRate.priceValue;
    }

    function getSelectedRatePriceLabel() {
        if (!selectedRate) {
            return '';
        }

        return selectedRate.price || 'Request';
    }

    function calculateFinalTotal() {
        if (!selectedRate || !hasSelectedRatePrice()) {
            return 0;
        }

        return Math.max(0, calculateBaseTotal() - bookingDiscount - promotionDiscount);
    }

    function updateReviewPricing() {
        if (!selectedRate) {
            return;
        }

        if (!hasSelectedRatePrice()) {
            if (sidebarPriceTarget) {
                sidebarPriceTarget.textContent = getSelectedRatePriceLabel();
            }
            if (reviewBasePrice) {
                reviewBasePrice.textContent = getSelectedRatePriceLabel();
            }
            if (reviewFinalPrice) {
                reviewFinalPrice.textContent = getSelectedRatePriceLabel();
            }
            if (reviewTotal) {
                reviewTotal.textContent = getSelectedRatePriceLabel();
            }
            if (modalPriceTarget) {
                modalPriceTarget.textContent = getSelectedRatePriceLabel();
            }
            return;
        }

        var basePrice = calculateBaseTotal();
        var finalPrice = calculateFinalTotal();

        if (reviewBasePrice) {
            reviewBasePrice.textContent = formatCurrency(basePrice);
        }

        if (reviewFinalPrice) {
            reviewFinalPrice.textContent = formatCurrency(finalPrice);
        }

        if (reviewTotal) {
            reviewTotal.textContent = formatCurrency(finalPrice);
        }

        if (modalPriceTarget) {
            modalPriceTarget.textContent = formatCurrency(finalPrice);
        }
    }

    function getGuestEntries() {
        if (!guestList) {
            return [];
        }

        return Array.from(guestList.querySelectorAll('[data-transport-guest-item]')).map(function (item) {
            var nameInput = item.querySelector('input[name$="[name]"]');
            var ageSelect = item.querySelector('select[name$="[age]"]');
            var sexSelect = item.querySelector('select[name$="[sex]"]');
            var phoneInput = item.querySelector('input[name$="[phone]"]');

            return {
                name: nameInput ? nameInput.value.trim() : '',
                age: ageSelect ? ageSelect.value : 'Adult',
                sex: sexSelect ? sexSelect.value : '',
                phone: phoneInput ? phoneInput.value.trim() : ''
            };
        });
    }

    function getReviewableGuestEntries() {
        return getGuestEntries().filter(function (guest) {
            return guest.name !== '';
        });
    }

    function renderReviewGuestRows(guestEntries) {
        if (!reviewGuestTableBody) {
            return;
        }

        reviewGuestTableBody.innerHTML = '';

        if (reviewGuestEmpty) {
            reviewGuestEmpty.hidden = guestEntries.length > 0;
        }

        guestEntries.forEach(function (guest, index) {
            var row = document.createElement('tr');
            var values = [
                index + 1,
                guest.name || '-',
                guest.age || '-',
                guest.sex || '-',
                guest.phone || '-'
            ];

            values.forEach(function (value) {
                var cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            });

            reviewGuestTableBody.appendChild(row);
        });
    }

    function updateReviewGuestSummary() {
        var guestEntries = getReviewableGuestEntries();
        var adultCount = guestEntries.filter(function (guest) {
            return guest.age === 'Adult';
        }).length;
        var childCount = guestEntries.filter(function (guest) {
            return guest.age === 'Child';
        }).length;

        if (reviewGuestsTotal) {
            reviewGuestsTotal.textContent = guestEntries.length + ' ' + paxLabel;
        }

        if (reviewGuestsAdult) {
            reviewGuestsAdult.textContent = adultCount + ' ' + adultLabel;
        }

        if (reviewGuestsChild) {
            reviewGuestsChild.textContent = childCount + ' ' + childLabel;
        }

        renderReviewGuestRows(guestEntries);
    }

    function clearInlineFieldError(field) {
        if (!field) {
            return;
        }

        field.classList.remove('is-invalid');

        var fieldContainer = field.closest('.transport-reservation-field');
        var fieldError = fieldContainer ? fieldContainer.querySelector('[data-transport-inline-error]') : null;

        if (fieldError) {
            fieldError.remove();
        }
    }

    function showInlineFieldError(field, message) {
        if (!field) {
            return;
        }

        clearInlineFieldError(field);
        field.classList.add('is-invalid');

        var fieldError = document.createElement('div');
        fieldError.className = 'alert-form';
        fieldError.setAttribute('data-transport-inline-error', 'true');
        fieldError.textContent = message;
        field.insertAdjacentElement('afterend', fieldError);
    }

    function focusFirstInvalidField(stepNumber) {
        if (!modalForm) {
            return;
        }

        var panel = modalForm.querySelector('[data-wizard-panel="' + stepNumber + '"]');
        var invalidField = panel ? panel.querySelector('.is-invalid, input:invalid, select:invalid, textarea:invalid') : null;

        if (!invalidField || !isFieldVisible(invalidField)) {
            return;
        }

        invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        invalidField.focus({ preventScroll: true });
    }

    function isFieldVisible(field) {
        if (!field) {
            return false;
        }

        return !field.disabled
            && !field.closest('[hidden]')
            && field.offsetParent !== null;
    }

    function isFieldFilled(field) {
        if (!field) {
            return true;
        }

        var tagName = (field.tagName || '').toLowerCase();
        var fieldType = (field.getAttribute('type') || '').toLowerCase();
        var value = typeof field.value === 'string' ? field.value.trim() : field.value;

        if (fieldType === 'number') {
            var numericValue = parseInt(value || '0', 10);
            var minValue = parseInt(field.getAttribute('min') || '1', 10);

            return !Number.isNaN(numericValue) && numericValue >= minValue;
        }

        if (tagName === 'select') {
            return value !== '';
        }

        return value !== '';
    }

    function validateServiceStep(options) {
        var config = options || {};
        var shouldNotify = config.notify !== false;

        if (!selectedRate) {
            return true;
        }

        if (serviceDateInput && flightDateInput) {
            serviceDateInput.value = flightDateInput.value;
        }

        var requiredFields = [];

        if (agentSelect) {
            requiredFields.push(agentSelect);
        }

        requiredFields.push(flightDateInput);

        if (selectedRate.type === 'Airport Shuttle') {
            requiredFields.push(shuttleTypeSelect, page.querySelector('#flight_number'));
        }

        for (var i = 0; i < requiredFields.length; i += 1) {
            var field = requiredFields[i];

            if (!field || (!isFieldFilled(field) || !field.checkValidity())) {
                if (shouldNotify) {
                    var message = field && field.id === 'flight_date'
                        ? 'Please select the flight date before continuing to guest details.'
                        : (field && field.id === 'transportAgent'
                            ? 'Please select an agent before continuing.'
                            : flightRequiredValidationMessage);

                    showInlineFieldError(field, message);

                    if (field && isFieldVisible(field)) {
                        field.reportValidity();
                        field.focus();
                    }
                }

                return false;
            }
        }

        return true;
    }

    function validateTripAndGuestsStep(options) {
        var config = options || {};
        var shouldNotify = config.notify !== false;
        var includeHidden = config.includeHidden === true;

        if (!selectedRate) {
            return true;
        }

        var pickupLocationInput = page.querySelector('#pickup_location');
        var dropoffLocationInput = page.querySelector('#dropoff_location');
        var requiredFields = [];

        if (selectedRate.type === 'Daily Rent' && durationField) {
            requiredFields.push(durationField);
        }

        if (selectedRate.type === 'Daily Rent') {
            if (pickupLocationInput) {
                requiredFields.push(pickupLocationInput);
            }

            if (dropoffLocationInput) {
                requiredFields.push(dropoffLocationInput);
            }
        }

        for (var i = 0; i < requiredFields.length; i += 1) {
            var field = requiredFields[i];

            if (!field || (!includeHidden && !isFieldVisible(field))) {
                continue;
            }

            if (!isFieldFilled(field) || !field.checkValidity()) {
                if (shouldNotify) {
                    showInlineFieldError(field, serviceRequiredValidationMessage);
                    field.reportValidity();
                    field.focus();
                }
                return false;
            }
        }

        var guestEntries = getGuestEntries();
        var hasValidGuest = guestEntries.some(function (guest) {
            return guest.name !== '';
        });

        if (!hasValidGuest) {
            var guestNameField = guestList ? guestList.querySelector('input[name$="[name]"]') : null;

            if (shouldNotify) {
                if (guestNameField) {
                    showInlineFieldError(guestNameField, guestNameValidationMessage);
                    guestNameField.reportValidity();
                    guestNameField.focus();
                }
            }

            return false;
        }

        if (guestList) {
            var invalidGuestField = guestList.querySelector('input:invalid, select:invalid, textarea:invalid');

            if (invalidGuestField && (includeHidden || isFieldVisible(invalidGuestField))) {
                if (shouldNotify) {
                    showInlineFieldError(invalidGuestField, guestRequiredValidationMessage);
                    invalidGuestField.reportValidity();
                    invalidGuestField.focus();
                }
                return false;
            }
        }

        return true;
    }

    function updateTripReviewButtonState() {
        if (!tripReviewButton) {
            return;
        }

        var isStepValid = validateTripAndGuestsStep({ notify: false });
        tripReviewButton.disabled = !isStepValid;
        tripReviewButton.setAttribute('aria-disabled', isStepValid ? 'false' : 'true');
        tripReviewButton.classList.toggle('is-disabled', !isStepValid);
    }

    function validateTermsAccepted(shouldNotify) {
        if (!termsCheckbox) {
            return true;
        }

        var isValid = termsCheckbox.checkValidity();
        termsCheckbox.classList.toggle('is-invalid', !isValid);

        if (!isValid && shouldNotify) {
            termsCheckbox.reportValidity();
            termsCheckbox.focus();
        }

        return isValid;
    }

    function updateServicePanels() {
        if (!selectedRate) {
            return;
        }

        var isDailyRent = selectedRate.type === 'Daily Rent';
        var isAirportShuttle = selectedRate.type === 'Airport Shuttle';
        var shuttleType = shuttleTypeSelect ? shuttleTypeSelect.value : oldShuttleType;

        if (durationField) {
            if (isDailyRent) {
                if (!durationField.value || parseInt(durationField.value, 10) < 1) {
                    durationField.value = durationInput && parseInt(durationInput.value, 10) > 0
                        ? durationInput.value
                        : '1';
                }
            }
        }

        if (shuttleTypeGroup) {
            shuttleTypeGroup.hidden = !isAirportShuttle;
        }

        if (dailyRentFields) {
            dailyRentFields.hidden = false;
        }

        dailyRentLocationFields.forEach(function (fieldGroup) {
            fieldGroup.hidden = !isDailyRent;
        });

        if (modalFlightGrid) {
            modalFlightGrid.hidden = false;
            modalFlightGrid.classList.toggle('transport-reservation-grid--daily-rent-service', isDailyRent);
        }

        airportFlightFields.forEach(function (field) {
            field.hidden = !isAirportShuttle;
        });

        var pickupLocationInput = page.querySelector('#pickup_location');
        var dropoffLocationInput = page.querySelector('#dropoff_location');
        var flightNumberInput = page.querySelector('#flight_number');

        if (flightDateInput) {
            flightDateInput.required = true;
        }
        if (transportDateLabel) {
            transportDateLabel.firstChild.textContent = (isAirportShuttle ? flightDateLabel : serviceDateLabel) + ' ';
        }
        if (pickupLocationInput) {
            pickupLocationInput.required = isDailyRent;
        }
        if (dropoffLocationInput) {
            dropoffLocationInput.required = isDailyRent;
        }
        if (flightNumberInput) {
            flightNumberInput.required = isAirportShuttle;
        }
        syncDurationInput();
        updateTripReviewButtonState();
    }

    function renderSelectedRate(rate) {
        if (!rate) {
            return;
        }

        selectedRate = rate;

        syncDestinationControls(rate.type, rate.type === 'Airport Shuttle' ? rate.dst : '');
        setVisibleRateCards(rate.type, rate.type === 'Airport Shuttle' ? rate.dst : '');

        if (sidebarTypeSelect.value !== rate.type) {
            sidebarTypeSelect.value = rate.type;
        }
        if (sidebarDestinationSelect && rate.type === 'Airport Shuttle') {
            sidebarDestinationSelect.value = rate.dst;
        }

        if (sidebarRouteTarget) {
            sidebarRouteTarget.textContent = rate.route;
        }
        if (sidebarPriceTarget) {
            sidebarPriceTarget.textContent = getSelectedRatePriceLabel();
        }
        if (sidebarDurationTarget) {
            sidebarDurationTarget.textContent = getDisplayedDurationLabel(rate);
        }

        if (modalRouteTarget) {
            modalRouteTarget.textContent = rate.route;
        }
        if (modalRouteCopyTarget) {
            modalRouteCopyTarget.textContent = rate.route;
        }
        if (modalDurationTarget) {
            modalDurationTarget.textContent = getDisplayedDurationLabel(rate);
        }
        if (modalDurationLabelTarget) {
            modalDurationLabelTarget.textContent = rate.type === 'Daily Rent'
                ? estimatedRentalDurationLabel
                : estimatedDurationLabel;
        }
        if (modalExtraTarget) {
            modalExtraTarget.textContent = rate.extraTime || '-';
        }
        if (modalTypeTarget) {
            modalTypeTarget.textContent = rate.typeLabel;
        }
        if (reviewService) {
            reviewService.textContent = rate.typeLabel;
        }
        if (reviewRoute) {
            reviewRoute.textContent = rate.route;
        }

        var pickupLocationInput = page.querySelector('#pickup_location');
        var dropoffLocationInput = page.querySelector('#dropoff_location');
        var flightNumberInput = page.querySelector('#flight_number');
        var isDailyRent = rate.type === 'Daily Rent';
        var isAirportShuttle = rate.type === 'Airport Shuttle';

        if (reviewRouteCard) {
            reviewRouteCard.hidden = isDailyRent;
        }
        if (reviewFlightTypeCard) {
            reviewFlightTypeCard.hidden = !isAirportShuttle;
        }
        if (reviewFlightNumberCard) {
            reviewFlightNumberCard.hidden = !isAirportShuttle;
        }
        if (reviewPickupCard) {
            reviewPickupCard.hidden = !isDailyRent;
        }
        if (reviewDropoffCard) {
            reviewDropoffCard.hidden = !isDailyRent;
        }
        if (reviewServiceDate) {
            reviewServiceDate.textContent = (flightDateInput && flightDateInput.value.trim()) || '-';
        }
        if (reviewFlightType) {
            reviewFlightType.textContent = isAirportShuttle
                ? ((shuttleTypeSelect && shuttleTypeSelect.value) || oldShuttleType || 'Arrival')
                : '-';
        }
        if (reviewFlightNumber) {
            reviewFlightNumber.textContent = isAirportShuttle
                ? ((flightNumberInput && flightNumberInput.value.trim()) || '-')
                : '-';
        }
        if (reviewPickupLocation) {
            reviewPickupLocation.textContent = isDailyRent
                ? ((pickupLocationInput && pickupLocationInput.value.trim()) || rate.src || '-')
                : '-';
        }
        if (reviewDropoffLocation) {
            reviewDropoffLocation.textContent = isDailyRent
                ? ((dropoffLocationInput && dropoffLocationInput.value.trim()) || rate.dst || '-')
                : '-';
        }

        if (modalRateNote) {
            modalRateNote.textContent = rate.additionalInfo || '';
            modalRateNote.hidden = !rate.additionalInfo;
        }

        if (selectedPriceIdInput) {
            selectedPriceIdInput.value = rate.id;
        }

        if (modalForm) {
            modalForm.setAttribute('action', rate.createAction);
        }

        cards.forEach(function (card) {
            card.classList.toggle('is-selected', String(card.getAttribute('data-rate-id')) === String(rate.id));
        });

        updateServicePanels();
        updateReviewPricing();
        updateTripReviewButtonState();
    }

    function selectFirstRateByType(type, preferredDestination) {
        syncDestinationControls(type, preferredDestination);
        var destination = type === 'Airport Shuttle'
            ? (preferredDestination || (sidebarDestinationSelect ? sidebarDestinationSelect.value : ''))
            : '';
        renderSelectedRate(getFirstRateByTypeAndDestination(type, destination) || getRatesByType(type)[0]);
    }

    function goToStep(stepNumber) {
        activeStep = stepNumber;

        wizardPanels.forEach(function (panel) {
            panel.classList.toggle('is-active', String(panel.getAttribute('data-wizard-panel')) === String(stepNumber));
        });

        wizardSteps.forEach(function (step) {
            var targetStep = parseInt(step.getAttribute('data-wizard-step-target') || '0', 10);
            step.classList.toggle('is-active', targetStep === stepNumber);
            step.classList.toggle('is-complete', targetStep < stepNumber);
        });
    }

    function openModal() {
        if (!modal) {
            return;
        }

        modal.classList.add('is-visible');
        modal.setAttribute('aria-hidden', 'false');
        setBodyScrollLock(true);
    }

    function closeModal() {
        if (!modal || isSubmitting) {
            return;
        }

        modal.classList.remove('is-visible');
        modal.setAttribute('aria-hidden', 'true');
        setBodyScrollLock(false);
    }

    function setSubmittingState(submitting) {
        isSubmitting = submitting;
        if (modalForm) {
            modalForm.setAttribute('aria-busy', submitting ? 'true' : 'false');
            modalForm.toggleAttribute('inert', submitting);
        }
        document.documentElement.classList.toggle('frontend-order-submit-locked', submitting);
        document.body.classList.toggle('frontend-order-submit-locked', submitting);

        if (submitOverlay) {
            if (submitting && submitOverlay.parentElement !== document.body) {
                document.body.appendChild(submitOverlay);
            }

            submitOverlay.style.setProperty('z-index', '2147483647', 'important');
            submitOverlay.classList.toggle('hidden', !submitting);
            submitOverlay.setAttribute('aria-hidden', submitting ? 'false' : 'true');
        }

        submitButtons.forEach(function (button) {
            var originalHtml = button.getAttribute('data-original-html');

            if (!originalHtml) {
                originalHtml = button.innerHTML;
                button.setAttribute('data-original-html', originalHtml);
            }

            button.disabled = submitting;
            button.classList.toggle('is-processing', submitting);
            button.setAttribute('aria-disabled', submitting ? 'true' : 'false');
            button.innerHTML = submitting
                ? '<span class="transport-reservation-submit-overlay__spinner transport-reservation-submit-overlay__spinner--button" aria-hidden="true"></span><span>' + processingLabel + '</span>'
                : originalHtml;
        });
    }

    function getStorageKey() {
        var orderNumber = page.getAttribute('data-transport-booking-order-number') || '';
        return 'transportReservationSubmitted:' + window.location.pathname + ':' + orderNumber;
    }

    function syncTransportOrderNumberFromAgent() {
        if (!agentSelect) {
            return;
        }

        var selectedOption = agentSelect.options[agentSelect.selectedIndex];
        var orderNumber = selectedOption ? selectedOption.getAttribute('data-order-number') : '';

        if (!orderNumber) {
            return;
        }

        page.setAttribute('data-transport-booking-order-number', orderNumber);

        if (orderNumberInput) {
            orderNumberInput.value = orderNumber;
        }

        if (reviewOrderNumber) {
            reviewOrderNumber.textContent = orderNumber;
        }
    }

    function markSubmitted() {
        try {
            window.sessionStorage.setItem(getStorageKey(), String(Date.now()));
        } catch (error) {
            return;
        }
    }

    function clearSubmittedMarker() {
        try {
            window.sessionStorage.removeItem(getStorageKey());
        } catch (error) {
            return;
        }
    }

    function wasSubmitted() {
        try {
            return !!window.sessionStorage.getItem(getStorageKey());
        } catch (error) {
            return false;
        }
    }

    function showSubmittedWarning() {
        if (!modalForm || modalForm.querySelector('[data-transport-submitted-warning]')) {
            return;
        }

        var notice = document.createElement('div');
        notice.className = 'alert alert-warning transport-reservation-warning';
        notice.setAttribute('data-transport-submitted-warning', 'true');
        notice.textContent = submittedWarning;
        modalForm.prepend(notice);

        submitButtons.forEach(function (button) {
            button.disabled = true;
        });
    }

    function updateGuestControls() {
        if (!guestList) {
            return;
        }

        var items = Array.from(guestList.querySelectorAll('[data-transport-guest-item]'));
        var capacity = parseInt(guestList.getAttribute('data-capacity') || '0', 10) || items.length;

        items.forEach(function (item, index) {
            var label = item.querySelector('[data-transport-guest-label]');
            var removeButton = item.querySelector('[data-remove-transport-guest]');

            if (label) {
                label.textContent = getGuestLabel(index);
            }

            item.querySelectorAll('input, select').forEach(function (field) {
                var fieldName = field.getAttribute('name') || '';
                field.setAttribute('name', fieldName.replace(/guest_entries\[\d+\]/, 'guest_entries[' + index + ']'));
            });

            if (removeButton) {
                removeButton.hidden = items.length === 1;
            }
        });

        if (addGuestButton) {
            addGuestButton.disabled = items.length >= capacity;
            addGuestButton.classList.toggle('is-disabled', items.length >= capacity);
        }
    }

    function createGuestItem(index) {
        var wrapper = document.createElement('div');
        wrapper.className = 'transport-reservation-guest-item';
        wrapper.setAttribute('data-transport-guest-item', 'true');
        wrapper.innerHTML =
            '<div class="transport-reservation-guest-item__index">' +
                '<strong data-transport-guest-label>' + escapeHtml(getGuestLabel(index)) + '</strong>' +
            '</div>' +
            '<div class="transport-reservation-guest-item__content">' +
                '<div class="transport-reservation-field transport-reservation-field--compact">' +
                    '<label>' + escapeHtml(labelName) + ' <span class="transport-reservation-required" aria-hidden="true">*</span></label>' +
                    '<input type="text" name="guest_entries[' + index + '][name]" class="form-control" placeholder="' + escapeHtml(placeholderGuestName) + '" required>' +
                '</div>' +
                '<div class="transport-reservation-field transport-reservation-field--compact">' +
                    '<label>' + escapeHtml(labelAgeCategory) + ' <span class="transport-reservation-required" aria-hidden="true">*</span></label>' +
                    '<select name="guest_entries[' + index + '][age]" class="form-control" required>' +
                        '<option value="Adult" selected>' + escapeHtml(adultLabel) + '</option>' +
                        '<option value="Child">' + escapeHtml(childLabel) + '</option>' +
                    '</select>' +
                '</div>' +
                '<div class="transport-reservation-field transport-reservation-field--compact">' +
                    '<label>' + escapeHtml(labelGender) + ' <span class="transport-reservation-required" aria-hidden="true">*</span></label>' +
                    '<select name="guest_entries[' + index + '][sex]" class="form-control" required>' +
                        '<option value="">' + escapeHtml(selectGenderLabel) + '</option>' +
                        '<option value="Male">' + escapeHtml(maleLabel) + '</option>' +
                        '<option value="Female">' + escapeHtml(femaleLabel) + '</option>' +
                    '</select>' +
                '</div>' +
                '<div class="transport-reservation-field transport-reservation-field--compact">' +
                    '<label>' + escapeHtml(labelPhone) + ' <span class="transport-reservation-optional">(' + escapeHtml(labelOptional) + ')</span></label>' +
                    '<input type="text" name="guest_entries[' + index + '][phone]" class="form-control" placeholder="' + escapeHtml(placeholderPhone) + '">' +
                '</div>' +
                '<div class="transport-reservation-guest-item__action">' +
                    '<label class="transport-reservation-guest-item__action-label">&nbsp;</label>' +
                    '<button type="button" class="transport-reservation-guest-item__remove" data-remove-transport-guest>' +
                        '<span aria-hidden="true">X</span>' +
                    '</button>' +
                '</div>' +
            '</div>';

        return wrapper;
    }

    page.querySelectorAll('[data-transport-datetime]').forEach(function (input) {
        try {
            if (window.FrontendPickerSystem) {
                input.dataset.uiPicker = input.dataset.uiPicker || 'datetime';
                input.dataset.uiPickerFormat = input.dataset.uiPickerFormat || 'YYYY-MM-DD HH:mm';
                window.FrontendPickerSystem.initPicker(input);
                return;
            }

            if (window.jQuery && typeof window.jQuery.fn.daterangepicker === 'function' && typeof window.moment === 'function') {
                var minimumTransportDate = window.moment().startOf('day').add(1, 'day');
                var resolveTransportPickerDrops = function (picker) {
                    if (!picker || !picker.container || !picker.container.length) {
                        return 'down';
                    }

                    var inputRect = input.getBoundingClientRect();
                    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                    var panelHeight = picker.container.outerHeight() || 360;
                    var spaceBelow = viewportHeight - inputRect.bottom;
                    var spaceAbove = inputRect.top;

                    return spaceBelow < panelHeight + 12 && spaceAbove > spaceBelow ? 'up' : 'down';
                };
                var pickerInput = window.jQuery(input);

                pickerInput.daterangepicker({
                    autoApply: true,
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    timePickerIncrement: 5,
                    showDropdowns: true,
                    parentEl: 'body',
                    opens: 'center',
                    drops: 'auto',
                    minDate: minimumTransportDate,
                    startDate: minimumTransportDate.clone(),
                    locale: {
                        format: 'YYYY-MM-DD HH:mm',
                    },
                }).on('apply.daterangepicker', function (_event, picker) {
                    input.value = picker.startDate.format('YYYY-MM-DD HH:mm');
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }).on('show.daterangepicker', function (_event, picker) {
                    picker.drops = resolveTransportPickerDrops(picker);
                    picker.container
                        .toggleClass('drop-up', picker.drops === 'up')
                        .toggleClass('drop-down', picker.drops !== 'up');

                    if (typeof picker.move === 'function') {
                        picker.move();
                    }
                });
                return;
            }

            if (typeof window.flatpickr === 'function') {
                window.flatpickr(input, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    time_24hr: true,
                    minuteIncrement: 5,
                    allowInput: true
                });
            }
        } catch (error) {
            if (window.console && typeof window.console.warn === 'function') {
                window.console.warn('Transport datetime picker init failed.', error);
            }
        }
    });

    if (guestList) {
        guestList.addEventListener('click', function (event) {
            var removeButton = event.target.closest('[data-remove-transport-guest]');

            if (!removeButton) {
                return;
            }

            var items = guestList.querySelectorAll('[data-transport-guest-item]');

            if (items.length <= 1) {
                return;
            }

            removeButton.closest('[data-transport-guest-item]').remove();
            updateGuestControls();
            updateReviewGuestSummary();
            updateTripReviewButtonState();
        });

        guestList.addEventListener('input', function (event) {
            clearInlineFieldError(event.target);
            updateReviewGuestSummary();
            updateTripReviewButtonState();
        });
        guestList.addEventListener('change', function (event) {
            clearInlineFieldError(event.target);
            updateReviewGuestSummary();
            updateTripReviewButtonState();
        });

        updateGuestControls();
        updateReviewGuestSummary();
        updateTripReviewButtonState();
    }

    ['#flight_date', '#flight_number', '#pickup_location', '#dropoff_location'].forEach(function (selector) {
        var field = page.querySelector(selector);

        if (!field) {
            return;
        }

        field.addEventListener('input', function () {
            clearInlineFieldError(field);

            if (selector === '#flight_date' || selector === '#flight_number') {
            } else {
                updateTripReviewButtonState();
            }

            if (!selectedRate) {
                return;
            }

            if (selector === '#flight_date' && serviceDateInput) {
                serviceDateInput.value = field.value;
            }

            if (reviewServiceDate && selector === '#flight_date') {
                reviewServiceDate.textContent = field.value.trim() || '-';
            }

            if (reviewFlightNumber && selector === '#flight_number' && selectedRate.type === 'Airport Shuttle') {
                reviewFlightNumber.textContent = field.value.trim() || '-';
            }

            if (reviewPickupLocation && selector === '#pickup_location' && selectedRate.type === 'Daily Rent') {
                reviewPickupLocation.textContent = field.value.trim() || selectedRate.src || '-';
            }

            if (reviewDropoffLocation && selector === '#dropoff_location' && selectedRate.type === 'Daily Rent') {
                reviewDropoffLocation.textContent = field.value.trim() || selectedRate.dst || '-';
            }
        });
    });

    if (durationField) {
        durationField.addEventListener('input', function () {
            clearInlineFieldError(durationField);
            updateTripReviewButtonState();

            if (!selectedRate || selectedRate.type !== 'Daily Rent') {
                return;
            }

            syncDurationInput();
            updateReviewPricing();
            if (sidebarDurationTarget) {
                sidebarDurationTarget.textContent = getDisplayedDurationLabel(selectedRate);
            }
            if (modalDurationTarget) {
                modalDurationTarget.textContent = getDisplayedDurationLabel(selectedRate);
            }
        });

        durationField.addEventListener('change', function () {
            clearInlineFieldError(durationField);
            updateTripReviewButtonState();

            if (!selectedRate || selectedRate.type !== 'Daily Rent') {
                return;
            }

            syncDurationInput();
            updateReviewPricing();
            if (sidebarDurationTarget) {
                sidebarDurationTarget.textContent = getDisplayedDurationLabel(selectedRate);
            }
            if (modalDurationTarget) {
                modalDurationTarget.textContent = getDisplayedDurationLabel(selectedRate);
            }
        });
    }

    if (addGuestButton && guestList) {
        addGuestButton.addEventListener('click', function () {
            var items = guestList.querySelectorAll('[data-transport-guest-item]');
            var capacity = parseInt(guestList.getAttribute('data-capacity') || '0', 10) || items.length;

            if (items.length >= capacity) {
                return;
            }

            guestList.appendChild(createGuestItem(items.length));
            updateGuestControls();
            updateReviewGuestSummary();
            updateTripReviewButtonState();
        });
    }

    sidebarTypeSelect.addEventListener('change', function () {
        selectFirstRateByType(sidebarTypeSelect.value, '');
    });

    if (sidebarDestinationSelect) {
        sidebarDestinationSelect.addEventListener('change', function () {
            renderSelectedRate(getFirstRateByTypeAndDestination(sidebarTypeSelect.value, sidebarDestinationSelect.value));
        });
    }

    if (shuttleTypeSelect) {
        shuttleTypeSelect.addEventListener('change', function () {
            clearInlineFieldError(shuttleTypeSelect);
            updateServicePanels();

            if (reviewFlightType && selectedRate && selectedRate.type === 'Airport Shuttle') {
                reviewFlightType.textContent = shuttleTypeSelect.value || 'Arrival';
            }
        });
        shuttleTypeSelect.value = oldShuttleType;
    }

    if (agentSelect) {
        agentSelect.addEventListener('change', syncTransportOrderNumberFromAgent);
        syncTransportOrderNumberFromAgent();
    }

    wizardNextButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var currentPanel = button.closest('[data-wizard-panel]');
            var currentStep = currentPanel ? parseInt(currentPanel.getAttribute('data-wizard-panel') || '1', 10) : activeStep;

            if (currentStep === 1 && !validateServiceStep()) {
                return;
            }

            if (currentStep === 2 && !validateTripAndGuestsStep()) {
                return;
            }

            goToStep(Math.min(activeStep + 1, wizardPanels.length));
        });
    });

    wizardPrevButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            goToStep(Math.max(activeStep - 1, 1));
        });
    });

    wizardSteps.forEach(function (stepButton) {
        stepButton.addEventListener('click', function () {
            var targetStep = parseInt(stepButton.getAttribute('data-wizard-step-target') || '1', 10);

            if (activeStep === 1 && targetStep > 1) {
                if (!validateServiceStep()) {
                    return;
                }

                if (targetStep > 2) {
                    goToStep(2);
                    return;
                }
            }

            if (activeStep === 2 && targetStep > 2 && !validateTripAndGuestsStep()) {
                return;
            }

            goToStep(targetStep);
        });
    });

    if (openModalButton) {
        openModalButton.addEventListener('click', function () {
            openModal();
        });
    }

    closeModalButtons.forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    page.addEventListener('click', function (event) {
        var card = event.target.closest('[data-rate-card]');

        if (!card) {
            return;
        }

        var rate = getRateById(card.getAttribute('data-rate-id'));
        renderSelectedRate(rate);
    });

    if (modalForm) {
        var initialNavigation = window.performance && window.performance.getEntriesByType
            ? window.performance.getEntriesByType('navigation')[0]
            : null;

        if (!initialNavigation || initialNavigation.type !== 'back_forward') {
            clearSubmittedMarker();
        }

        modalForm.addEventListener('submit', function (event) {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }

            if (!validateServiceStep({ notify: false })) {
                event.preventDefault();
                goToStep(1);
                window.setTimeout(function () {
                    validateServiceStep({ notify: true });
                }, 0);
                return;
            }

            if (!validateTripAndGuestsStep({ notify: false, includeHidden: true })) {
                event.preventDefault();
                goToStep(2);
                window.setTimeout(function () {
                    validateTripAndGuestsStep({ notify: true });
                }, 0);
                return;
            }

            if (!validateTermsAccepted(false)) {
                event.preventDefault();
                goToStep(3);
                window.setTimeout(function () {
                    validateTermsAccepted(true);
                }, 0);
                return;
            }

            setSubmittingState(true);
            markSubmitted();
        });

        window.addEventListener('pageshow', function (event) {
            var navigation = window.performance && window.performance.getEntriesByType
                ? window.performance.getEntriesByType('navigation')[0]
                : null;
            var isHistoryRestore = !!event.persisted || !!(navigation && navigation.type === 'back_forward');

            if (isHistoryRestore && wasSubmitted()) {
                clearSubmittedMarker();
                window.location.reload();
            }
        });
    }

    renderSelectedRate(getRateById(defaultRateId) || rates[0]);
    updateReviewGuestSummary();
    goToStep(shouldOpenModalOnLoad ? initialErrorStep : 1);

    if (shouldOpenModalOnLoad) {
        openModal();
        window.setTimeout(function () {
            focusFirstInvalidField(initialErrorStep);
        }, 120);
    }
});
