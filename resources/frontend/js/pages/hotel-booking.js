(function ($) {
    'use strict';

    window.goBack = window.goBack || function () {
        window.history.back();
    };

    if (!$) {
        return;
    }

    function toNumber(value) {
        var parsed = parseFloat(value);
        return isNaN(parsed) ? 0 : parsed;
    }

    function formatCurrency(amount, digits) {
        return '$ ' + toNumber(amount).toLocaleString('en-US', {
            minimumFractionDigits: digits,
            maximumFractionDigits: digits
        });
    }

    function setText($element, value) {
        if ($element.length) {
            $element.text(value);
        }
    }

    function setValue($element, value) {
        if ($element.length) {
            $element.val(value);
        }
    }

    function setVisibility($element, visible) {
        if ($element.length) {
            $element.toggle(!!visible);
        }
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatNativeDate(value) {
        if (!value) {
            return '';
        }

        if (typeof moment !== 'undefined') {
            var parsed = moment(value, ['YYYY-MM-DD', 'MM/DD/YYYY', 'DD MMM YYYY', 'MMMM D, YYYY', moment.ISO_8601], true);
            if (parsed.isValid()) {
                return parsed.format('YYYY-MM-DD');
            }
        }

        return value;
    }

    function formatNativeDateTime(value) {
        if (!value) {
            return '';
        }

        if (typeof moment !== 'undefined') {
            var parsed = moment(value, [moment.ISO_8601, 'YYYY-MM-DDTHH:mm', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD HH:mm:ss', 'MM/DD/YYYY hh:mm A', 'MMMM D, YYYY hh:mm A'], true);
            if (parsed.isValid()) {
                return parsed.format('YYYY-MM-DD HH:mm');
            }
        }

        return value;
    }

    function getSubmitButtons($form) {
        var $buttons = $form.find('button[type="submit"], input[type="submit"]');
        var formId = $form.attr('id');

        if (formId) {
            $buttons = $buttons.add($('[form="' + formId + '"]'));
        }

        return $buttons;
    }

    function setPageScrollLock(locked) {
        var $html = $('html');
        var $body = $('body');
        var lockClass = 'booking-submit-scroll-locked';

        if (locked) {
            if ($body.data('bookingScrollLocked')) {
                return;
            }

            var scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;

            $body.data('bookingScrollLocked', true);
            $body.data('bookingScrollTop', scrollTop);
            $html.addClass(lockClass);
            $body.addClass(lockClass).css({
                position: 'fixed',
                top: '-' + scrollTop + 'px',
                left: 0,
                right: 0,
                width: '100%',
                overflow: 'hidden'
            });
            return;
        }

        if (!$body.data('bookingScrollLocked')) {
            return;
        }

        var previousScrollTop = parseInt($body.data('bookingScrollTop'), 10) || 0;

        $html.removeClass(lockClass);
        $body.removeClass(lockClass)
            .css({
                position: '',
                top: '',
                left: '',
                right: '',
                width: '',
                overflow: ''
            })
            .removeData('bookingScrollLocked')
            .removeData('bookingScrollTop');

        window.scrollTo(0, previousScrollTop);
    }

    function getSafeSessionStorage() {
        try {
            return window.sessionStorage;
        } catch (error) {
            return null;
        }
    }

    function getBookingSubmissionKey($form) {
        var orderNo = $form.find('input[name="orderno"]').first().val() || '';
        var action = $form.attr('action') || '';

        return 'hotelBookingSubmitted:' + window.location.pathname + ':' + action + ':' + orderNo;
    }

    function markBookingFormSubmitted($form) {
        var storage = getSafeSessionStorage();

        if (!storage) {
            return;
        }

        storage.setItem(getBookingSubmissionKey($form), String(Date.now()));
    }

    function wasBookingFormSubmitted($form) {
        var storage = getSafeSessionStorage();

        return !!(storage && storage.getItem(getBookingSubmissionKey($form)));
    }

    function isHistoryRestore(event) {
        var navigation = window.performance && window.performance.getEntriesByType
            ? window.performance.getEntriesByType('navigation')[0]
            : null;

        return !!(event && event.persisted) || !!(navigation && navigation.type === 'back_forward');
    }

    function resetRestoredBookingForm($form) {
        var form = $form.get(0);
        var message = 'This order has already been submitted. Please start a new booking from the hotel availability page to create another order.';

        if (form && typeof form.reset === 'function') {
            form.reset();
        }

        $form.find('input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="reset"]), textarea').val('');
        $form.find('select').prop('selectedIndex', 0);
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback, .alert-danger').remove();

        if (!$form.find('[data-booking-submitted-warning]').length) {
            $form.prepend(
                '<div class="alert alert-warning" data-booking-submitted-warning role="alert">' +
                    escapeHtml(message) +
                '</div>'
            );
        }

        getSubmitButtons($form).prop('disabled', true)
            .attr('aria-disabled', 'true')
            .addClass('is-disabled');
    }

    function setSubmittingState($form, submitting) {
        var processingLabel = $form.data('processingLabel') || 'Processing...';
        var $overlay = $form.data('submitOverlay');

        if (!$overlay || !$overlay.length) {
            $overlay = $form.find('[data-form-submit-overlay]').first();
            $form.data('submitOverlay', $overlay);
        }

        $form.data('isSubmitting', !!submitting);
        $form.attr('aria-busy', submitting ? 'true' : 'false');
        setPageScrollLock(!!submitting);

        if ($overlay.length) {
            if (submitting && !$overlay.parent().is('body')) {
                $overlay.appendTo('body');
            }

            $overlay.toggleClass('hidden', !submitting);
            $overlay.attr('aria-hidden', submitting ? 'false' : 'true');
        }

        getSubmitButtons($form).each(function () {
            var $button = $(this);
            var originalHtml = $button.data('originalHtml');
            var buttonProcessingLabel = $button.data('processingLabel') || processingLabel;

            if (typeof originalHtml === 'undefined') {
                originalHtml = $button.is('input') ? $button.val() : $button.html();
                $button.data('originalHtml', originalHtml);
            }

            $button.prop('disabled', !!submitting)
                .toggleClass('is-processing', !!submitting)
                .attr('aria-disabled', submitting ? 'true' : 'false');

            if ($button.is('input')) {
                $button.val(submitting ? buttonProcessingLabel : originalHtml);
                return;
            }

            $button.html(
                submitting
                    ? '<span class="booking-submit-button__spinner" aria-hidden="true"></span><span>' + escapeHtml(buttonProcessingLabel) + '</span>'
                    : originalHtml
            );
        });
    }

    function initWizard($wizard) {
        var $panels = $wizard.find('[data-wizard-panel]');
        var $steps = $wizard.find('[data-wizard-step-target]');
        var $nav = $wizard.find('.booking-wizard__nav').first();
        var activeStep = 1;

        if (!$panels.length || !$steps.length) {
            return;
        }

        $panels.addClass('booking-wizard__panel');
        $nav.attr('role', 'tablist');

        $steps.each(function () {
            var $step = $(this);
            var target = parseInt($step.data('wizard-step-target'), 10);
            var panelId = 'booking-wizard-panel-' + target;

            $step.attr({
                role: 'tab',
                'aria-controls': panelId
            });
        });

        $panels.each(function () {
            var $panel = $(this);
            var stepNumber = parseInt($panel.data('wizard-panel'), 10);

            $panel.attr({
                id: 'booking-wizard-panel-' + stepNumber,
                role: 'tabpanel'
            });
        });

        function findInitialStep() {
            var $panelWithErrors = $panels.filter(function () {
                return $(this).find('.is-invalid, .alert-danger, .invalid-feedback').length > 0;
            }).first();
            var $activePanel = $panels.filter('.is-active').first();
            var $activeStep = $steps.filter('.is-active').first();
            var candidate = parseInt($panelWithErrors.data('wizard-panel'), 10)
                || parseInt($activePanel.data('wizard-panel'), 10)
                || parseInt($activeStep.data('wizard-step-target'), 10);

            return candidate > 0 ? candidate : 1;
        }

        function showStep(stepNumber, options) {
            var nextStep = Math.max(1, Math.min(stepNumber, $panels.length));
            var shouldScroll = !options || options.skipScroll !== true;

            activeStep = nextStep;

            $panels.each(function () {
                var $panel = $(this);
                var isActive = parseInt($panel.data('wizard-panel'), 10) === nextStep;

                $panel.toggleClass('is-active', isActive);
                $panel.attr('aria-hidden', isActive ? 'false' : 'true');
                $panel.prop('hidden', !isActive);
            });

            $steps.each(function () {
                var $step = $(this);
                var target = parseInt($step.data('wizard-step-target'), 10);
                var isActive = target === nextStep;

                $step.toggleClass('is-active', isActive);
                $step.toggleClass('is-complete', target < nextStep);
                $step.attr('aria-selected', isActive ? 'true' : 'false');
                $step.attr('tabindex', isActive ? '0' : '-1');
            });

            var $currentPanel = $wizard.find('[data-wizard-panel="' + nextStep + '"]');
            if (shouldScroll && $currentPanel.length) {
                window.requestAnimationFrame(function () {
                    $currentPanel.get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        }

        function validateStep(stepNumber) {
            var panel = $wizard.find('[data-wizard-panel="' + stepNumber + '"]').get(0);
            if (!panel) {
                return true;
            }

            var requiredFields = panel.querySelectorAll('input[required], select[required], textarea[required]');
            for (var i = 0; i < requiredFields.length; i += 1) {
                var field = requiredFields[i];
                if (field.disabled || field.offsetParent === null) {
                    continue;
                }

                if (!field.checkValidity()) {
                    showStep(stepNumber, { skipScroll: true });
                    field.reportValidity();
                    field.focus();
                    return false;
                }
            }

            return true;
        }

        $wizard.on('click', '[data-wizard-next]', function () {
            if (validateStep(activeStep) && activeStep < $panels.length) {
                showStep(activeStep + 1);
            }
        });

        $wizard.on('click', '[data-wizard-prev]', function () {
            if (activeStep > 1) {
                showStep(activeStep - 1);
            }
        });

        $wizard.on('click', '[data-wizard-step-target]', function () {
            var targetStep = parseInt($(this).data('wizard-step-target'), 10);
            if (targetStep <= activeStep || validateStep(activeStep)) {
                showStep(targetStep);
            }
        });

        $wizard.on('keydown', '[data-wizard-step-target]', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                $(this).trigger('click');
            }
        });

        activeStep = findInitialStep();
        showStep(activeStep, { skipScroll: true });
    }

    function initDateInputs(scope) {
        var $scope = scope ? $(scope) : $(document);
        var defaultDate = typeof moment !== 'undefined'
            ? moment().add(7, 'days').format('YYYY-MM-DD')
            : '';
        var defaultDateTime = typeof moment !== 'undefined'
            ? moment().format('YYYY-MM-DD HH:mm')
            : '';
        var minDateTime = typeof moment !== 'undefined'
            ? moment().startOf('day').format('YYYY-MM-DD HH:mm')
            : 'today';

        function createFlatpickrConfig(options) {
            return $.extend({
                allowInput: false,
                clickOpens: true,
                disableMobile: true,
                static: false
            }, options);
        }

        function initFlatpickrInstance(element, options) {
            if (typeof window.flatpickr !== 'function' || !element) {
                return;
            }

            if (element._flatpickr) {
                element._flatpickr.destroy();
            }

            window.flatpickr(element, createFlatpickrConfig(options));
        }

        $scope.find('input[name="special_date[]"]').each(function () {
            initFlatpickrInstance(this, {
                dateFormat: 'Y-m-d',
                defaultDate: this.value || defaultDate
            });
        });

        $scope.find('input[name="arrival_time"], input[name="departure_time"], input[name="flight_time[]"]').each(function () {
            initFlatpickrInstance(this, {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                minuteIncrement: 5,
                minDate: minDateTime,
                defaultDate: this.value || defaultDateTime
            });
        });
    }

    function cleanupClonedFlatpickr($scope) {
        $scope.find('.flatpickr-input').each(function () {
            if (!this.name) {
                $(this).remove();
            }
        });

        $scope.find('input[name="special_date[]"], input[name="arrival_time"], input[name="departure_time"], input[name="flight_time[]"]').each(function () {
            var input = this;
            var $input = $(input);
            var isSpecialDate = input.name === 'special_date[]';

            if (input._flatpickr) {
                input._flatpickr.destroy();
            }

            input.type = 'text';
            $input.removeClass('flatpickr-input');
            $input.removeAttr('data-has-flatpickr');
            $input.attr('readonly', true);
            $input.css('display', '');
            $input.val('');

            if (isSpecialDate) {
                $input.attr('placeholder', '');
            }

            $input.siblings('.flatpickr-input').filter(function () {
                return !this.name;
            }).remove();
        });
    }

    function initBookingForm($form) {
        var variant = $form.data('booking-variant') || 'standard';
        var maxRooms = parseInt($form.data('room-max'), 10) || 8;
        var quoteMaxRooms = parseInt($form.data('quote-room-max'), 10) || Math.max(maxRooms, 30);
        var currencyDigits = parseInt($form.data('currency-digits'), 10) || 0;
        var toBeAdvisedLabel = $form.data('label-to-be-advised') || 'To be advised';
        var roomLabel = $form.data('label-room') || 'Room';
        var leadRoomLabel = $form.data('label-lead-room') || 'Lead room';
        var additionalRoomLabel = $form.data('label-additional-room') || 'Additional room';
        var selectTransportLabel = $form.data('label-select-transport') || 'Select Transport';
        var notAddedLabel = $form.data('label-not-added') || 'Not added';
        var flightPrefixLabel = $form.data('label-flight-prefix') || 'Flight';
        var guestSingularLabel = $form.data('label-guest-singular') || 'Guest';
        var guestPluralLabel = $form.data('label-guest-plural') || 'Guests';
        var extraBedLabel = $form.data('label-extra-bed') || 'Extra Bed';
        var noneLabel = $form.data('label-none') || 'None';
        var noExtraBedLabel = $form.data('label-no-extra-bed') || 'No extra bed available';
        var specialDayLabel = $form.data('label-special-day') || 'Special Day';
        var guestDetailsPendingLabel = $form.data('label-guest-details-pending') || 'Guest details pending';
        var guestNamesMissingLabel = $form.data('label-guest-names-missing') || 'Guest names not filled yet';
        var reviewEmptyLabel = $form.data('label-review-empty') || 'Add guest names and rooming details to review them here.';
        var noRemarkLabel = $form.data('label-no-remark') || 'No remark added.';
        var quoteRequestLabel = $form.data('label-quote-request') || 'Quote request';
        var quoteReviewLabel = $form.data('label-quote-review') || 'This order will be handled as a quote request because it contains more than 8 rooms.';
        var processingLabel = $form.find('button[type="submit"]').first().data('processingLabel') || 'Processing...';
        var $roomList = $form.find('#dynamic_field');
        var $addButton = $form.find('#add');
        var $quoteCheckbox = $form.find('[data-quote-checkbox]');
        var $quoteCard = $form.find('[data-quote-card]');
        var $transportIn = $form.find('#airportShuttleIn');
        var $transportOut = $form.find('#airportShuttleOut');
        var $duration = $form.find('#duration');
        var $transferList = $form.find('[data-transfer-list]');
        var $transferTemplate = $form.find('[data-transfer-template]').first();
        var stayCheckinDate = $form.data('review-checkin') || '';
        var stayCheckoutDate = $form.data('review-checkout') || '';

        if (!$roomList.length) {
            return;
        }

        function getRoomCount() {
            return $roomList.find('[data-room-item]').length || 1;
        }

        function isQuoteRequested() {
            return $quoteCheckbox.length && $quoteCheckbox.is(':checked');
        }

        function getCurrentRoomLimit() {
            return isQuoteRequested() ? quoteMaxRooms : maxRooms;
        }

        function syncQuotationState() {
            var roomCount = getRoomCount();
            var shouldForceQuote = roomCount > maxRooms;
            var quoteRequested = isQuoteRequested() || shouldForceQuote;

            if ($quoteCheckbox.length) {
                $quoteCheckbox.prop('checked', quoteRequested);
                $quoteCheckbox.prop('disabled', shouldForceQuote);
            }

            if ($quoteCard.length) {
                $quoteCard.toggleClass('is-active', quoteRequested);
                $quoteCard.toggleClass('is-locked', shouldForceQuote);
            }

            $addButton.prop('disabled', roomCount >= getCurrentRoomLimit());
        }

        function getGuestTotal() {
            var guestTotal = 0;
            $roomList.find('input[name="number_of_guests[]"]').each(function () {
                guestTotal += toNumber($(this).val());
            });
            return guestTotal;
        }

        function renumberRooms() {
            $roomList.find('[data-room-item]').each(function (index) {
                var roomNumber = index + 1;
                var badgeNumber = ('0' + roomNumber).slice(-2);
                var $item = $(this);

                $item.find('.room-card-head__badge').text(badgeNumber);
                $item.find('.room-card-head__title').text(roomLabel + ' ' + roomNumber);
                $item.find('.room-card-head__subtitle').text(roomNumber === 1 ? leadRoomLabel : additionalRoomLabel);

                var $removeButton = $item.find('.room-card-head__remove');
                if (roomNumber === 1) {
                    $removeButton.attr('hidden', true);
                } else {
                    $removeButton.removeAttr('hidden');
                }
            });

            syncQuotationState();
        }

        function toggleExtraBedSelection() {
            $roomList.find('[data-room-item]').each(function () {
                var $item = $(this);
                var $guestInput = $item.find('input[name="number_of_guests[]"]');
                var guestCount = toNumber($guestInput.val());
                var guestLimit = toNumber($guestInput.data('extra-bed-trigger'));
                var $extraBed = $item.find('[data-extra-bed-select]');
                var hasOptions = $extraBed.find('option').length > 1;
                var shouldEnable = hasOptions && guestLimit > 0 && guestCount > guestLimit;
                var selectedValue = $extraBed.val();

                if (shouldEnable) {
                    if (!selectedValue) {
                        $extraBed.prop('selectedIndex', 1).trigger('change.selectExtraBed');
                    }
                } else {
                    $extraBed.val('');
                }
            });
        }

        function getTransportSelection($select, mode) {
            var $selected = $select.find('option:selected');
            if (!$selected.length) {
                return { active: false, price: 0, priceId: 0 };
            }

            return {
                active: toNumber($selected.data(mode === 'in' ? 'transportin' : 'transportout')) === 1,
                price: toNumber($selected.data(mode === 'in' ? 'transportpricein' : 'transportpriceout')),
                priceId: toNumber($selected.data(mode === 'in' ? 'transportinpriceid' : 'transportoutpriceid'))
            };
        }

        function getTransferItems() {
            return $transferList.find('[data-transfer-item]');
        }

        function getTransferDefaultDateTime(type, currentValue) {
            var date = type === 'departure' ? stayCheckoutDate : stayCheckinDate;
            var time = '11:00';

            if (!date || (type !== 'arrival' && type !== 'departure')) {
                return '';
            }

            if (currentValue && typeof moment !== 'undefined') {
                var parsed = moment(currentValue, [moment.ISO_8601, 'YYYY-MM-DD HH:mm', 'YYYY-MM-DDTHH:mm'], true);

                if (parsed.isValid()) {
                    time = parsed.format('HH:mm');
                }
            }

            return date + ' ' + time;
        }

        function applyTransferDateDefault($item, force) {
            var type = $item.find('select[name="flight_type[]"]').val();
            var $timeInput = $item.find('input[name="flight_time[]"]');
            var currentValue = $.trim($timeInput.val());
            var defaultDateTime = getTransferDefaultDateTime(type, currentValue);

            if (!defaultDateTime || (!force && currentValue)) {
                return;
            }

            if ($timeInput.get(0) && $timeInput.get(0)._flatpickr) {
                $timeInput.get(0)._flatpickr.setDate(defaultDateTime, false, 'Y-m-d H:i');
            } else {
                $timeInput.val(defaultDateTime);
            }
        }

        function applyInitialTransferDateDefaults() {
            getTransferItems().each(function () {
                applyTransferDateDefault($(this), false);
            });
        }

        function syncLegacyTransferFields() {
            var primaryTransfer = {
                arrival: null,
                departure: null
            };

            setValue($form.find('input[name="arrival_flight"]'), '');
            setValue($form.find('input[name="departure_flight"]'), '');
            setValue($form.find('input[name="arrival_time"]'), '');
            setValue($form.find('input[name="departure_time"]'), '');
            setValue($transportIn, '');
            setValue($transportOut, '');

            getTransferItems().each(function () {
                var $item = $(this);
                var type = $item.find('select[name="flight_type[]"]').val();
                var flightNumber = $.trim($item.find('input[name="flight_number[]"]').val());
                var time = $.trim($item.find('input[name="flight_time[]"]').val());
                var $transportSelect = $item.find('select[name="flight_transport_id[]"]');
                var transportValue = $transportSelect.val();
                var transportLabel = $.trim($transportSelect.find('option:selected').text());

                setValue(
                    $item.find('input[name="flight_transport_label[]"]'),
                    transportValue && transportLabel !== selectTransportLabel ? transportLabel : ''
                );

                if (type === 'arrival' || type === 'departure') {
                    var hasCurrentValue = !!flightNumber || !!time || !!transportValue;
                    var existingTransfer = primaryTransfer[type];
                    var existingHasValue = existingTransfer && (existingTransfer.flightNumber || existingTransfer.time || existingTransfer.transport);

                    if (!existingTransfer || (!existingHasValue && hasCurrentValue)) {
                        primaryTransfer[type] = {
                            flightNumber: flightNumber,
                            time: time,
                            transport: transportValue || ''
                        };
                    }
                }
            });

            if (primaryTransfer.arrival) {
                setValue($form.find('input[name="arrival_flight"]'), primaryTransfer.arrival.flightNumber || '');
                setValue($form.find('input[name="arrival_time"]'), primaryTransfer.arrival.time || '');
                setValue($transportIn, primaryTransfer.arrival.transport || '');
            }

            if (primaryTransfer.departure) {
                setValue($form.find('input[name="departure_flight"]'), primaryTransfer.departure.flightNumber || '');
                setValue($form.find('input[name="departure_time"]'), primaryTransfer.departure.time || '');
                setValue($transportOut, primaryTransfer.departure.transport || '');
            }
        }

        function getTransferReviewItems() {
            var items = [];

            getTransferItems().each(function () {
                var $item = $(this);
                var typeValue = $item.find('select[name="flight_type[]"]').val();
                var typeText = $.trim($item.find('select[name="flight_type[]"] option:selected').text());
                var flightNumber = $.trim($item.find('input[name="flight_number[]"]').val());
                var flightTime = $.trim($item.find('input[name="flight_time[]"]').val());
                var transportValue = $item.find('select[name="flight_transport_id[]"]').val();
                var transportText = $.trim($item.find('select[name="flight_transport_id[]"] option:selected').text());
                var parts = [];

                if (!typeValue && !flightNumber && !flightTime && !transportValue) {
                    return;
                }

                if (typeValue && typeText) {
                    parts.push(typeText);
                }

                if (flightNumber) {
                    parts.push(flightNumber);
                }

                if (flightTime) {
                    parts.push(formatNativeDateTime(flightTime));
                }

                if (transportValue && transportText && transportText !== selectTransportLabel) {
                    parts.push(transportText);
                }

                if (parts.length > 0) {
                    items.push({
                        title: flightPrefixLabel + ' ' + (items.length + 1),
                        meta: parts.join(' | ')
                    });
                }
            });

            return items;
        }

        function hasSelectedTransferType() {
            var hasSelectedType = false;

            getTransferItems().each(function () {
                if ($(this).find('select[name="flight_type[]"]').val()) {
                    hasSelectedType = true;
                    return false;
                }
            });

            return hasSelectedType;
        }

        function updateOptionalCharges() {
            var guestTotal = getGuestTotal();
            var optionalTotal = 0;

            $form.find('.optional-rate-row').each(function () {
                var $row = $(this);
                var ratePerPax = toNumber($row.find('.price-per-pax').data('price-pax'));
                var total = guestTotal * ratePerPax;

                optionalTotal += total;
                setText($row.find('.guest-total-display'), guestTotal);
                setText($row.find('.total-price-optional-rate'), formatCurrency(total, currencyDigits));
            });

            setText($form.find('#totalAdditionalCharge'), formatCurrency(optionalTotal, currencyDigits));
            setText($form.find('#totalAdditionalCargePrice'), formatCurrency(optionalTotal, currencyDigits));

            return optionalTotal;
        }

        function updatePriceSummary() {
            syncLegacyTransferFields();

            var roomCount = getRoomCount();
            var duration = Math.max(toNumber($duration.val()), 1);
            var extraBedTotal = 0;

            $roomList.find('select[name="extra_bed_id[]"]').each(function () {
                var price = toNumber($(this).find('option:selected').data('ebprice'));
                if (variant === 'standard') {
                    extraBedTotal += price;
                } else {
                    extraBedTotal += price * duration;
                }
            });

            var transportIn = getTransportSelection($transportIn, 'in');
            var transportOut = getTransportSelection($transportOut, 'out');
            var airportShuttleTotal = transportIn.price + transportOut.price;
            var airportNeedsAdvice = (transportIn.active && transportIn.price === 0) || (transportOut.active && transportOut.price === 0);
            var quoteRequested = isQuoteRequested();
            var optionalChargeTotal = updateOptionalCharges();
            var suitesAndVillasTotal = 0;
            var promotionsDiscount = 0;
            var kickBackTotal = 0;
            var finalTotal = 0;

            if (variant === 'standard') {
                suitesAndVillasTotal = toNumber($form.find('#var_normal_price').val()) * roomCount;
                promotionsDiscount = toNumber($form.find('#var_promotions_discount').val());
                kickBackTotal = toNumber($form.find('#var_kick_back_per_room').val()) * roomCount;
                finalTotal = suitesAndVillasTotal + extraBedTotal + airportShuttleTotal - promotionsDiscount - kickBackTotal;
                setValue($form.find('#var_kick_back_total'), kickBackTotal);
                setText($form.find('#promotionsDiscountTotal'), '- ' + formatCurrency(promotionsDiscount, currencyDigits));
                setText($form.find('#kickBackDiscount'), '- ' + formatCurrency(kickBackTotal, currencyDigits));
            } else if (variant === 'promo') {
                suitesAndVillasTotal = toNumber($form.find('#var_promo_price').val()) * roomCount;
                finalTotal = suitesAndVillasTotal + extraBedTotal + airportShuttleTotal + optionalChargeTotal;
            } else {
                suitesAndVillasTotal = toNumber($form.find('#var_package_price').val()) * roomCount;
                finalTotal = suitesAndVillasTotal + extraBedTotal + airportShuttleTotal;
            }

            setText(
                $form.find('#suitesAndVillasPriceLable'),
                quoteRequested ? toBeAdvisedLabel : formatCurrency(suitesAndVillasTotal, currencyDigits)
            );
            setText($form.find('#extraBedPriceTotal'), formatCurrency(extraBedTotal, currencyDigits));
            setText(
                $form.find('#airportShuttleText'),
                airportNeedsAdvice ? toBeAdvisedLabel : formatCurrency(airportShuttleTotal, currencyDigits)
            );

            setVisibility($form.find('#extraBedText, #extraBedPrice'), extraBedTotal > 0);
            setVisibility($form.find('#airportShuttle, #airportShuttlePrice'), transportIn.active || transportOut.active);
            setVisibility($form.find('#promotionsText, #promotionsDiscount'), variant === 'standard' && promotionsDiscount > 0);
            setVisibility($form.find('#kickBackText, #kickBackAmount'), variant === 'standard' && kickBackTotal > 0);
            setVisibility($form.find('#totalAdditionalCargeText, #totalAdditionalCarge'), variant === 'promo' && optionalChargeTotal > 0);

            setValue($form.find('#airport_shuttle_in_price_id'), transportIn.priceId || '');
            setValue($form.find('#airport_shuttle_out_price_id'), transportOut.priceId || '');

            setValue($form.find('#final_price'), finalTotal);
            setText(
                $form.find('#finalprice'),
                quoteRequested || airportNeedsAdvice ? toBeAdvisedLabel : formatCurrency(finalTotal, currencyDigits)
            );
        }

        function updateReviewSummary() {
            var roomCount = getRoomCount();
            var guestTotal = getGuestTotal();
            var hotelName = $form.data('review-hotel') || '-';
            var roomName = $form.data('review-room') || '-';
            var checkin = $form.data('review-checkin') || '';
            var checkout = $form.data('review-checkout') || '';
            var durationLabel = $form.data('review-duration') || '-';
            var $roomListReview = $form.find('[data-review-room-list]');
            var $quoteReview = $form.find('[data-review-quote-status]');
            var roomItemsMarkup = '';

            setText($form.find('[data-review-hotel]'), hotelName);
            setText($form.find('[data-review-room]'), roomName);
            setText($form.find('[data-review-dates]'), checkin && checkout ? checkin + ' - ' + checkout : '-');
            setText($form.find('[data-review-duration]'), durationLabel);
            setText($form.find('[data-review-room-count]'), roomCount);
            setText($form.find('[data-review-guest-count]'), guestTotal);
            setVisibility($quoteReview, isQuoteRequested());
            if (isQuoteRequested()) {
                setText($quoteReview.find('span'), quoteRequestLabel);
                setText($quoteReview.find('strong'), quoteReviewLabel);
            }

            $roomList.find('[data-room-item]').each(function (index) {
                var $item = $(this);
                var guestCount = toNumber($item.find('input[name="number_of_guests[]"]').val());
                var guestDetail = $.trim($item.find('input[name="guest_detail[]"]').val());
                var specialDay = $.trim($item.find('input[name="special_day[]"]').val());
                var specialDate = $.trim($item.find('input[name="special_date[]"]').val());
                var formattedSpecialDate = formatNativeDate(specialDate);
                var selectedExtraBedLabel = $.trim($item.find('[data-extra-bed-select] option:selected').text());
                var roomMeta = [];

                if (guestCount > 0) {
                    roomMeta.push(guestCount + ' ' + (guestCount > 1 ? guestPluralLabel : guestSingularLabel));
                }

                if (selectedExtraBedLabel && selectedExtraBedLabel !== noneLabel && selectedExtraBedLabel !== noExtraBedLabel) {
                    roomMeta.push(extraBedLabel + ': ' + selectedExtraBedLabel);
                }

                if (specialDay) {
                    roomMeta.push(specialDayLabel + ': ' + specialDay + (formattedSpecialDate ? ' (' + formattedSpecialDate + ')' : ''));
                }

                roomItemsMarkup += ''
                    + '<article class="booking-review__room-item">'
                    + '<div class="booking-review__room-top">'
                    + '<strong>' + escapeHtml(roomLabel + ' ' + (index + 1)) + '</strong>'
                    + '<span>' + escapeHtml(roomMeta.join(' | ') || guestDetailsPendingLabel) + '</span>'
                    + '</div>'
                    + '<div class="booking-review__room-body">' + escapeHtml(guestDetail || guestNamesMissingLabel) + '</div>'
                    + '</article>';
            });

            if (!$roomListReview.length) {
                return;
            }

            $roomListReview.html(roomItemsMarkup || '<div class="booking-review__empty">' + escapeHtml(reviewEmptyLabel) + '</div>');
            var transferItems = getTransferReviewItems();
            var $transferListReview = $form.find('[data-review-transfer-list]');
            var transferItemsMarkup = '';
            var showAirportShuttleCard = hasSelectedTransferType();

            $.each(transferItems, function (index, transferItem) {
                transferItemsMarkup += ''
                    + '<article class="booking-review__room-item">'
                    + '<div class="booking-review__room-top">'
                    + '<strong>' + escapeHtml(transferItem.title || (flightPrefixLabel + ' ' + (index + 1))) + '</strong>'
                    + '<span>' + escapeHtml(transferItem.meta || notAddedLabel) + '</span>'
                    + '</div>'
                    + '</article>';
            });

            setVisibility($form.find('[data-review-airport-shuttle-card]'), showAirportShuttleCard);
            if ($transferListReview.length) {
                $transferListReview.html(transferItemsMarkup || '<div class="booking-review__empty">' + escapeHtml(notAddedLabel) + '</div>');
            }
        }

        function resetClonedRoom($item) {
            $item.find('input').each(function () {
                var $input = $(this);
                if ($input.attr('type') === 'number') {
                    $input.val('');
                } else {
                    $input.val('');
                }
                $input.removeClass('is-invalid');
            });

            $item.find('textarea').each(function () {
                $(this).val('').removeClass('is-invalid');
            });

            $item.find('select').each(function () {
                this.selectedIndex = 0;
                $(this).removeClass('is-invalid').prop('disabled', false);
            });

            $item.find('.alert, .invalid-feedback').remove();
        }

        function cloneRoom() {
            var roomCount = getRoomCount();
            if (roomCount >= getCurrentRoomLimit()) {
                return;
            }

            var $template = $roomList.find('[data-room-item]').first();
            if (!$template.length) {
                return;
            }

            var $clone = $template.clone(false, false);
            cleanupClonedFlatpickr($clone);
            resetClonedRoom($clone);
            $clone.find('.room-card-head__remove').removeAttr('hidden');
            $roomList.append($clone);

            initDateInputs($clone);
            renumberRooms();
            syncQuotationState();
            toggleExtraBedSelection();
            updatePriceSummary();
            updateReviewSummary();
        }

        function getDefaultTransferType() {
            var hasArrival = false;
            var hasDeparture = false;

            getTransferItems().each(function () {
                var type = $(this).find('select[name="flight_type[]"]').val();
                hasArrival = hasArrival || type === 'arrival';
                hasDeparture = hasDeparture || type === 'departure';
            });

            if (!hasArrival) {
                return 'arrival';
            }

            if (!hasDeparture) {
                return 'departure';
            }

            return '';
        }

        function createAdditionalFlight() {
            if (!$transferTemplate.length || !$transferList.length) {
                return;
            }

            var markup = $.trim($transferTemplate.html());
            if (!markup) {
                return;
            }

            var $item = $(markup);
            $item.find('select[name="flight_type[]"]').val(getDefaultTransferType());
            $transferList.append($item);
            initDateInputs($item);
            applyTransferDateDefault($item, true);
            syncLegacyTransferFields();
            updateReviewSummary();
            updatePriceSummary();
        }

        $addButton.on('click', function (event) {
            event.preventDefault();
            cloneRoom();
        });

        $form.on('click', '[data-add-flight]', function (event) {
            event.preventDefault();
            createAdditionalFlight();
        });

        $transferList.on('click', '[data-remove-flight]', function (event) {
            event.preventDefault();
            $(this).closest('[data-transfer-item]').remove();
            syncLegacyTransferFields();
            updatePriceSummary();
            updateReviewSummary();
        });

        $roomList.on('click', '.room-card-head__remove', function (event) {
            event.preventDefault();

            if (getRoomCount() <= 1) {
                return;
            }

            $(this).closest('[data-room-item]').remove();
            renumberRooms();
            syncQuotationState();
            toggleExtraBedSelection();
            updatePriceSummary();
            updateReviewSummary();
        });

        $quoteCheckbox.on('change', function () {
            syncQuotationState();
            updatePriceSummary();
            updateReviewSummary();
        });

        $transferList.on('change', 'select[name="flight_type[]"]', function () {
            applyTransferDateDefault($(this).closest('[data-transfer-item]'), true);
            syncLegacyTransferFields();
            updatePriceSummary();
            updateReviewSummary();
        });

        $form.on('input change', 'input[name="number_of_guests[]"], input[name="guest_detail[]"], input[name="special_day[]"], input[name="special_date[]"], input[name="flight_number[]"], input[name="flight_time[]"], select[name="flight_type[]"], select[name="flight_transport_id[]"], select[name="extra_bed_id[]"], textarea[name="note"]', function () {
            toggleExtraBedSelection();
            updatePriceSummary();
            updateReviewSummary();
        });

        $form.data('processingLabel', processingLabel);
        $form.on('submit', function (event) {
            if ($form.data('isSubmitting')) {
                event.preventDefault();
                return false;
            }

            markBookingFormSubmitted($form);
            setSubmittingState($form, true);
            return true;
        });

        applyInitialTransferDateDefaults();
        syncLegacyTransferFields();
        renumberRooms();
        syncQuotationState();
        toggleExtraBedSelection();
        updatePriceSummary();
        updateReviewSummary();
    }

    $(function () {
        if (typeof $.fn.tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        $('[data-booking-wizard]').each(function () {
            initWizard($(this));
        });

        initDateInputs();

        $('[data-booking-form]').each(function () {
            initBookingForm($(this));
        });

        window.addEventListener('pageshow', function (event) {
            setPageScrollLock(false);
            $('[data-booking-form]').each(function () {
                var $form = $(this);

                setSubmittingState($form, false);

                if (isHistoryRestore(event) && wasBookingFormSubmitted($form)) {
                    resetRestoredBookingForm($form);
                }
            });
        });
    });
}(window.jQuery));
