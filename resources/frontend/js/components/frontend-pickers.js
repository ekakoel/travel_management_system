(() => {
    const hasPickerLibrary = () => (
        window.jQuery
        && typeof window.jQuery.fn.daterangepicker === 'function'
        && typeof window.moment === 'function'
    );

    const $ = (target) => window.jQuery(target);
    const moment = () => window.moment;

    const DEFAULTS = {
        date: 'YYYY-MM-DD',
        datetime: 'YYYY-MM-DD HH:mm',
        time: 'HH:mm',
        month: 'YYYY-MM',
        year: 'YYYY',
        range: 'YYYY-MM-DD',
    };

    const normalizeMode = (input) => {
        const mode = input.dataset.uiPicker || input.dataset.picker;

        if (mode) {
            return {
                daterange: 'range',
                'date-range': 'range',
                single: 'date',
                'single-date': 'date',
                'date-time': 'datetime',
            }[mode] || mode;
        }

        if (input.classList.contains('datetimepicker') || input.dataset.bookingDatetime !== undefined || input.dataset.transportDatetime !== undefined) {
            return 'datetime';
        }

        if (input.classList.contains('date-picker')) {
            return 'date';
        }

        if (input.name === 'checkincout') {
            return 'range';
        }

        if (input.type === 'time' || input.type === 'month') {
            return input.type;
        }

        if (input.type === 'date') {
            return 'date';
        }

        if (input.type === 'datetime-local') {
            return 'native-datetime';
        }

        return 'date';
    };

    const getConfigValue = (input, uiName, legacyName, fallback = '') => (
        input.dataset[uiName] ?? input.dataset[legacyName] ?? fallback
    );

    const getBooleanConfig = (input, uiName, legacyName, fallback = false) => {
        const value = getConfigValue(input, uiName, legacyName, '');

        if (value === '') {
            return fallback;
        }

        return value === 'true' || value === '1';
    };

    const getFormat = (input, mode) => (
        input.dataset.uiPickerFormat
        || input.dataset.format
        || input.dataset.timeFormat
        || DEFAULTS[mode]
        || DEFAULTS.date
    );

    const parseMoment = (value, format) => {
        if (!value) {
            return null;
        }

        const parsed = moment()(value, [
            format,
            'YYYY-MM-DDTHH:mm',
            'YYYY-MM-DD HH:mm',
            'YYYY-MM-DD H:mm',
            'YYYY-MM-DD',
            'DD MMM YYYY HH.mm',
            'DD MMM YYYY HH:mm',
            'DD MMM YYYY',
            'D MMM YYYY HH.mm',
            'D MMM YYYY HH:mm',
            'D MMM YYYY',
            'd MMMM YYYY h:mm a',
            'D MMMM YYYY h:mm a',
            'DD MMMM YYYY h:mm a',
            'MM/DD/YYYY',
        ], true);

        return parsed.isValid() ? parsed : null;
    };

    const tomorrowStart = () => moment()().startOf('day').add(1, 'day');

    const formatNativeMin = (date, mode) => {
        if (mode === 'native-datetime') {
            return date.format('YYYY-MM-DDTHH:mm');
        }

        return date.format('YYYY-MM-DD');
    };

    const resolveMinDate = (input, mode, format) => {
        if (mode === 'time' || mode === 'month' || mode === 'year') {
            return null;
        }

        if (typeof window.moment !== 'function') {
            return null;
        }

        const tomorrow = tomorrowStart();
        const minSource = getConfigValue(input, 'uiPickerMin', 'minDate', input.getAttribute('min'));
        const configuredMin = parseMoment(minSource, format);

        if (configuredMin && configuredMin.isAfter(tomorrow)) {
            return configuredMin;
        }

        return tomorrow;
    };

    const setNativePickerState = (input, mode) => {
        const format = getFormat(input, mode);
        const minDate = resolveMinDate(input, mode, format);

        if (minDate) {
            input.setAttribute('min', formatNativeMin(minDate, mode));
        }

        input.classList.add('ui-picker-input', `ui-picker-input--${mode}`);
        input.dataset.uiPickerInitialized = 'native';
        wrapPickerInput(input, mode);
    };

    const applyReadonlyDefault = (input, mode) => {
        if (input.dataset.uiPickerReadonly === 'false') {
            return;
        }

        if (['date', 'datetime', 'time', 'range'].includes(mode)) {
            input.setAttribute('readonly', 'readonly');
        }
    };

    const wrapPickerInput = (input, mode) => {
        if (input.dataset.uiPickerIcon === 'false' || input.parentElement?.classList.contains('ui-picker-field')) {
            return;
        }

        if (input.closest('.input-group, .input-group-icon')) {
            input.classList.add('ui-picker-input--has-native-icon');
            return;
        }

        const wrapper = document.createElement('span');
        wrapper.className = `ui-picker-field ui-picker-field--${mode}`;
        const icon = document.createElement('span');
        icon.className = 'ui-picker-field__icon';
        icon.setAttribute('aria-hidden', 'true');

        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(icon);
        input.dataset.uiPickerWrapped = 'true';

        icon.addEventListener('click', () => {
            if (hasPickerLibrary()) {
                let picker = $(input).data('daterangepicker');

                if (!picker && input.dataset.uiPickerInitialized === 'true') {
                    delete input.dataset.uiPickerInitialized;
                    initPicker(input);
                    picker = $(input).data('daterangepicker');
                }

                if (picker) {
                    picker.show();
                    return;
                }
            }

            if (input.disabled || input.readOnly) {
                input.focus();
                return;
            }

            input.focus();
            if (typeof input.showPicker === 'function') {
                try {
                    input.showPicker();
                } catch (error) {
                    if (window.console && typeof window.console.warn === 'function') {
                        window.console.warn('Native picker could not be opened for this input.', input, error);
                    }
                }
            }
        });
    };

    const resolveParent = (input) => {
        if (input.dataset.uiPickerParent || input.dataset.parentEl) {
            return input.dataset.uiPickerParent || input.dataset.parentEl;
        }

        return input.closest('.modal') ? `#${input.closest('.modal').id}` : 'body';
    };

    const isInsideFloatingLayer = (input) => (
        input.closest('.modal, .frontend-order-modal, [data-transport-reservation-modal], [data-tour-order-modal], [data-activity-order-modal]')
    );

    const resolveDrops = (input, picker, configuredDrops) => {
        if (configuredDrops && configuredDrops !== 'auto') {
            return configuredDrops;
        }

        const inputRect = input.getBoundingClientRect();
        const panelHeight = picker?.container?.outerHeight?.() || 360;
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const spaceBelow = viewportHeight - inputRect.bottom;
        const spaceAbove = inputRect.top;

        return spaceBelow < panelHeight + 12 && spaceAbove > spaceBelow ? 'up' : 'down';
    };

    const syncNamedRangeInputs = (input, picker) => {
        const form = input.closest('form') || document;
        const startName = input.dataset.uiPickerStartName || input.dataset.startName;
        const endName = input.dataset.uiPickerEndName || input.dataset.endName;
        const startTarget = input.dataset.uiPickerStartTarget;
        const endTarget = input.dataset.uiPickerEndTarget;
        const format = getFormat(input, 'range');
        const write = (target, value) => {
            if (!target) {
                return;
            }

            target.value = value;
            target.dispatchEvent(new Event('input', { bubbles: true }));
            target.dispatchEvent(new Event('change', { bubbles: true }));
        };

        write(startTarget ? document.querySelector(startTarget) : (startName ? form.querySelector(`[name="${startName}"]`) : null), picker.startDate.format(format));
        write(endTarget ? document.querySelector(endTarget) : (endName ? form.querySelector(`[name="${endName}"]`) : null), picker.endDate.format(format));
    };

    const initRangePicker = (input, mode) => {
        if (!hasPickerLibrary()) {
            setNativePickerState(input, mode);
            return;
        }

        if ($(input).data('daterangepicker')) {
            input.classList.add('ui-picker-input', `ui-picker-input--${mode}`);
            return;
        }

        const format = getFormat(input, mode);
        const isRange = mode === 'range';
        const isTimeOnly = mode === 'time';
        const isDateTime = mode === 'datetime';
        const showButtons = getBooleanConfig(input, 'uiPickerShowButtons', 'showButtons', isDateTime || isTimeOnly);
        const configuredDrops = getConfigValue(input, 'uiPickerDrops', 'drops', 'auto');
        const startSource = getConfigValue(input, 'uiPickerStart', 'startDate', input.dataset.initialCheckin || input.value);
        const endSource = getConfigValue(input, 'uiPickerEnd', 'endDate', input.dataset.initialCheckout || input.value);
        const maxSource = getConfigValue(input, 'uiPickerMax', 'maxDate', input.getAttribute('max'));
        const minDate = resolveMinDate(input, mode, format);
        const maxDate = getBooleanConfig(input, 'uiPickerDisableFuture', 'disableFuture')
            ? moment()().endOf('day')
            : parseMoment(maxSource, format);
        const parsedStartDate = parseMoment(startSource, format);
        const startDate = parsedStartDate && (!minDate || parsedStartDate.isSameOrAfter(minDate))
            ? parsedStartDate
            : (minDate ? minDate.clone() : moment()());
        const parsedEndDate = parseMoment(endSource, format);
        const endDate = isRange
            ? (parsedEndDate && parsedEndDate.isAfter(startDate) ? parsedEndDate : startDate.clone().add(1, 'day'))
            : startDate.clone();

        input.classList.add('ui-picker-input', `ui-picker-input--${mode}`);
        applyReadonlyDefault(input, mode);
        wrapPickerInput(input, mode);

        $(input).daterangepicker({
            autoApply: !showButtons && getConfigValue(input, 'uiPickerAutoApply', 'autoApply', 'true') !== 'false',
            autoUpdateInput: false,
            singleDatePicker: !isRange || getBooleanConfig(input, 'uiPickerSingleDate', 'singleDate'),
            timePicker: isDateTime || isTimeOnly,
            timePicker24Hour: input.dataset.uiPickerClock !== '12',
            timePickerIncrement: Number.parseInt(getConfigValue(input, 'uiPickerMinuteStep', 'minuteStep', '5'), 10),
            timePickerSeconds: getBooleanConfig(input, 'uiPickerSeconds', 'seconds'),
            showDropdowns: getConfigValue(input, 'uiPickerShowDropdowns', 'showDropdowns', 'true') !== 'false',
            parentEl: resolveParent(input),
            opens: getConfigValue(input, 'uiPickerOpens', 'opens', isInsideFloatingLayer(input) ? 'center' : 'left'),
            drops: configuredDrops === 'auto' ? 'down' : configuredDrops,
            startDate,
            endDate,
            minDate: minDate || false,
            maxDate: maxDate || false,
            locale: {
                format,
                separator: getConfigValue(input, 'uiPickerSeparator', 'separator', ' - '),
                applyLabel: input.dataset.uiPickerApplyLabel || 'Apply',
                cancelLabel: input.dataset.uiPickerCancelLabel || 'Cancel',
            },
        });

        const syncValue = (picker) => {
            if (isRange) {
                input.value = `${picker.startDate.format(format)} - ${picker.endDate.format(format)}`;
                syncNamedRangeInputs(input, picker);
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }

            input.value = picker.startDate.format(format);
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        $(input)
            .off('apply.daterangepicker.frontendPicker show.daterangepicker.frontendPicker')
            .on('apply.daterangepicker.frontendPicker', (_event, picker) => syncValue(picker))
            .on('show.daterangepicker.frontendPicker', () => {
            const picker = $(input).data('daterangepicker');

            if (picker) {
                picker.container.addClass(`ui-picker-panel ui-picker-panel--${mode}`);
                picker.container.css('z-index', isInsideFloatingLayer(input) ? '3000' : '2000');
                picker.drops = resolveDrops(input, picker, configuredDrops);
                picker.container
                    .toggleClass('drop-up', picker.drops === 'up')
                    .toggleClass('drop-down', picker.drops !== 'up');

                if (typeof picker.move === 'function') {
                    picker.move();
                }
            }
        });

        input._frontendPickerSetDate = (value) => {
            const nextDate = parseMoment(value, format);
            const picker = $(input).data('daterangepicker');

            if (!nextDate || !picker) {
                input.value = value || '';
                return;
            }

            picker.setStartDate(nextDate);

            if (isRange) {
                picker.setEndDate(nextDate.clone().add(1, 'day'));
            } else {
                picker.setEndDate(nextDate);
            }

            syncValue(picker);
        };
    };

    const initPicker = (input) => {
        if (input.dataset.uiPickerInitialized) {
            return;
        }

        const mode = normalizeMode(input);

        if (['month', 'year', 'native-datetime'].includes(mode)) {
            setNativePickerState(input, mode);
            return;
        }

        initRangePicker(input, mode);
        input.dataset.uiPickerInitialized = 'true';
    };

    const selector = [
        '[data-ui-picker]',
        '[data-picker]',
        '[data-booking-datetime]',
        '[data-transport-datetime]',
        '.frontend-datepicker',
        '.frontend-datetimepicker',
        '.frontend-timepicker',
        '.daterangepicker-input',
        'input.datetimepicker',
        'input.date-picker',
        'input[name="checkincout"]',
        'input[type="date"]',
        'input[type="datetime-local"]',
        'input[type="time"]',
        'input[type="month"]',
    ].join(',');

    const safelyInitPicker = (input) => {
        try {
            initPicker(input);
        } catch (error) {
            if (window.console && typeof window.console.warn === 'function') {
                window.console.warn('Frontend picker init failed.', input, error);
            }
        }
    };

    const initAll = (root = document) => {
        if (root.matches?.(selector)) {
            safelyInitPicker(root);
        }

        root.querySelectorAll(selector).forEach(safelyInitPicker);
    };

    const destroyPicker = (input) => {
        if (!input) {
            return;
        }

        if ($(input).data('daterangepicker')) {
            $(input).off('.frontendPicker');
            $(input).data('daterangepicker').remove();
            $(input).removeData('daterangepicker');
        }

        delete input._frontendPickerSetDate;
        delete input.dataset.uiPickerInitialized;
        input.classList.remove(
            'ui-picker-input',
            'ui-picker-input--date',
            'ui-picker-input--datetime',
            'ui-picker-input--native-datetime',
            'ui-picker-input--time',
            'ui-picker-input--month',
            'ui-picker-input--year',
            'ui-picker-input--range',
            'ui-picker-input--has-native-icon',
        );

        if (input.dataset.uiPickerWrapped === 'true' && input.parentElement?.classList.contains('ui-picker-field')) {
            const wrapper = input.parentElement;
            wrapper.parentNode.insertBefore(input, wrapper);
            wrapper.remove();
            delete input.dataset.uiPickerWrapped;
        }
    };

    const refreshPicker = (input) => {
        destroyPicker(input);
        initPicker(input);
    };

    const boot = () => {
        initAll();

        document.addEventListener('shown.bs.modal', (event) => initAll(event.target));

        if (!document.body) {
            return;
        }

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        initAll(node);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    };

    window.FrontendPickerSystem = {
        init: initAll,
        initPicker,
        destroy: destroyPicker,
        refresh: refreshPicker,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
