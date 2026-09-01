/******/ (() => { // webpackBootstrap
/*!**************************************************************!*\
  !*** ./resources/frontend/js/components/frontend-pickers.js ***!
  \**************************************************************/
(function () {
  var hasPickerLibrary = function hasPickerLibrary() {
    return window.jQuery && typeof window.jQuery.fn.daterangepicker === 'function' && typeof window.moment === 'function';
  };
  var $ = function $(target) {
    return window.jQuery(target);
  };
  var moment = function moment() {
    return window.moment;
  };
  var DEFAULTS = {
    date: 'YYYY-MM-DD',
    datetime: 'YYYY-MM-DD HH:mm',
    time: 'HH:mm',
    month: 'YYYY-MM',
    year: 'YYYY',
    range: 'YYYY-MM-DD'
  };
  var normalizeMode = function normalizeMode(input) {
    var mode = input.dataset.uiPicker || input.dataset.picker;
    if (mode) {
      return {
        daterange: 'range',
        'date-range': 'range',
        single: 'date',
        'single-date': 'date',
        'date-time': 'datetime'
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
  var getConfigValue = function getConfigValue(input, uiName, legacyName) {
    var _ref, _input$dataset$uiName;
    var fallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
    return (_ref = (_input$dataset$uiName = input.dataset[uiName]) !== null && _input$dataset$uiName !== void 0 ? _input$dataset$uiName : input.dataset[legacyName]) !== null && _ref !== void 0 ? _ref : fallback;
  };
  var getBooleanConfig = function getBooleanConfig(input, uiName, legacyName) {
    var fallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
    var value = getConfigValue(input, uiName, legacyName, '');
    if (value === '') {
      return fallback;
    }
    return value === 'true' || value === '1';
  };
  var getFormat = function getFormat(input, mode) {
    return input.dataset.uiPickerFormat || input.dataset.format || input.dataset.timeFormat || DEFAULTS[mode] || DEFAULTS.date;
  };
  var parseMoment = function parseMoment(value, format) {
    if (!value) {
      return null;
    }
    var parsed = moment()(value, [format, 'YYYY-MM-DDTHH:mm', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD H:mm', 'YYYY-MM-DD', 'DD MMM YYYY HH.mm', 'DD MMM YYYY HH:mm', 'DD MMM YYYY', 'D MMM YYYY HH.mm', 'D MMM YYYY HH:mm', 'D MMM YYYY', 'd MMMM YYYY h:mm a', 'D MMMM YYYY h:mm a', 'DD MMMM YYYY h:mm a', 'MM/DD/YYYY'], true);
    return parsed.isValid() ? parsed : null;
  };
  var tomorrowStart = function tomorrowStart() {
    return moment()().startOf('day').add(1, 'day');
  };
  var formatNativeMin = function formatNativeMin(date, mode) {
    if (mode === 'native-datetime') {
      return date.format('YYYY-MM-DDTHH:mm');
    }
    return date.format('YYYY-MM-DD');
  };
  var resolveMinDate = function resolveMinDate(input, mode, format) {
    if (mode === 'time' || mode === 'month' || mode === 'year') {
      return null;
    }
    if (typeof window.moment !== 'function') {
      return null;
    }
    var minimumFloor = getBooleanConfig(input, 'uiPickerAllowToday', 'allowToday') ? moment()().startOf('day') : tomorrowStart();
    var minSource = getConfigValue(input, 'uiPickerMin', 'minDate', input.getAttribute('min'));
    var configuredMin = parseMoment(minSource, format);
    if (configuredMin && configuredMin.isSameOrAfter(minimumFloor)) {
      return configuredMin;
    }
    return minimumFloor;
  };
  var setNativePickerState = function setNativePickerState(input, mode) {
    var format = getFormat(input, mode);
    var minDate = resolveMinDate(input, mode, format);
    if (minDate) {
      input.setAttribute('min', formatNativeMin(minDate, mode));
    }
    input.classList.add('ui-picker-input', "ui-picker-input--".concat(mode));
    input.dataset.uiPickerInitialized = 'native';
    wrapPickerInput(input, mode);
  };
  var applyReadonlyDefault = function applyReadonlyDefault(input, mode) {
    if (input.dataset.uiPickerReadonly === 'false') {
      return;
    }
    if (['date', 'datetime', 'time', 'range'].includes(mode)) {
      input.setAttribute('readonly', 'readonly');
    }
  };
  var wrapPickerInput = function wrapPickerInput(input, mode) {
    var _input$parentElement;
    if (input.dataset.uiPickerIcon === 'false' || (_input$parentElement = input.parentElement) !== null && _input$parentElement !== void 0 && _input$parentElement.classList.contains('ui-picker-field')) {
      return;
    }
    if (input.closest('.input-group, .input-group-icon')) {
      input.classList.add('ui-picker-input--has-native-icon');
      return;
    }
    var wrapper = document.createElement('span');
    wrapper.className = "ui-picker-field ui-picker-field--".concat(mode);
    var icon = document.createElement('span');
    icon.className = 'ui-picker-field__icon';
    icon.setAttribute('aria-hidden', 'true');
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    wrapper.appendChild(icon);
    input.dataset.uiPickerWrapped = 'true';
    icon.addEventListener('click', function () {
      if (hasPickerLibrary()) {
        var picker = $(input).data('daterangepicker');
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
  var resolveParent = function resolveParent(input) {
    if (input.dataset.uiPickerParent || input.dataset.parentEl) {
      return input.dataset.uiPickerParent || input.dataset.parentEl;
    }
    return input.closest('.modal') ? "#".concat(input.closest('.modal').id) : 'body';
  };
  var isInsideFloatingLayer = function isInsideFloatingLayer(input) {
    return input.closest('.modal, .frontend-order-modal, [data-transport-reservation-modal], [data-tour-order-modal], [data-activity-order-modal]');
  };
  var resolvePanelZIndex = function resolvePanelZIndex(input) {
    var floatingLayer = isInsideFloatingLayer(input);
    if (!floatingLayer) {
      return 2000;
    }
    var layerZIndex = Number.parseInt(window.getComputedStyle(floatingLayer).zIndex, 10);
    return Number.isFinite(layerZIndex) ? Math.max(layerZIndex + 10, 3000) : 3000;
  };
  var resolveDrops = function resolveDrops(input, picker, configuredDrops) {
    var _picker$container, _picker$container$out;
    if (configuredDrops && configuredDrops !== 'auto') {
      return configuredDrops;
    }
    var inputRect = input.getBoundingClientRect();
    var panelHeight = (picker === null || picker === void 0 ? void 0 : (_picker$container = picker.container) === null || _picker$container === void 0 ? void 0 : (_picker$container$out = _picker$container.outerHeight) === null || _picker$container$out === void 0 ? void 0 : _picker$container$out.call(_picker$container)) || 360;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
    var spaceBelow = viewportHeight - inputRect.bottom;
    var spaceAbove = inputRect.top;
    return spaceBelow < panelHeight + 12 && spaceAbove > spaceBelow ? 'up' : 'down';
  };
  var syncNamedRangeInputs = function syncNamedRangeInputs(input, picker) {
    var form = input.closest('form') || document;
    var startName = input.dataset.uiPickerStartName || input.dataset.startName;
    var endName = input.dataset.uiPickerEndName || input.dataset.endName;
    var startTarget = input.dataset.uiPickerStartTarget;
    var endTarget = input.dataset.uiPickerEndTarget;
    var format = getFormat(input, 'range');
    var write = function write(target, value) {
      if (!target) {
        return;
      }
      target.value = value;
      target.dispatchEvent(new Event('input', {
        bubbles: true
      }));
      target.dispatchEvent(new Event('change', {
        bubbles: true
      }));
    };
    write(startTarget ? document.querySelector(startTarget) : startName ? form.querySelector("[name=\"".concat(startName, "\"]")) : null, picker.startDate.format(format));
    write(endTarget ? document.querySelector(endTarget) : endName ? form.querySelector("[name=\"".concat(endName, "\"]")) : null, picker.endDate.format(format));
  };
  var initRangePicker = function initRangePicker(input, mode) {
    if (!hasPickerLibrary()) {
      setNativePickerState(input, mode);
      return;
    }
    if ($(input).data('daterangepicker')) {
      input.classList.add('ui-picker-input', "ui-picker-input--".concat(mode));
      return;
    }
    var format = getFormat(input, mode);
    var isRange = mode === 'range';
    var isTimeOnly = mode === 'time';
    var isDateTime = mode === 'datetime';
    var showButtons = getBooleanConfig(input, 'uiPickerShowButtons', 'showButtons', isDateTime || isTimeOnly);
    var configuredDrops = getConfigValue(input, 'uiPickerDrops', 'drops', 'auto');
    var startSource = getConfigValue(input, 'uiPickerStart', 'startDate', input.dataset.initialCheckin || input.value);
    var endSource = getConfigValue(input, 'uiPickerEnd', 'endDate', input.dataset.initialCheckout || input.value);
    var maxSource = getConfigValue(input, 'uiPickerMax', 'maxDate', input.getAttribute('max'));
    var minDate = resolveMinDate(input, mode, format);
    var maxDate = getBooleanConfig(input, 'uiPickerDisableFuture', 'disableFuture') ? moment()().endOf('day') : parseMoment(maxSource, format);
    var parsedStartDate = parseMoment(startSource, format);
    var startDate = parsedStartDate && (!minDate || parsedStartDate.isSameOrAfter(minDate)) ? parsedStartDate : minDate ? minDate.clone() : moment()();
    var parsedEndDate = parseMoment(endSource, format);
    var endDate = isRange ? parsedEndDate && parsedEndDate.isAfter(startDate) ? parsedEndDate : startDate.clone().add(1, 'day') : startDate.clone();
    input.classList.add('ui-picker-input', "ui-picker-input--".concat(mode));
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
      startDate: startDate,
      endDate: endDate,
      minDate: minDate || false,
      maxDate: maxDate || false,
      locale: {
        format: format,
        separator: getConfigValue(input, 'uiPickerSeparator', 'separator', ' - '),
        applyLabel: input.dataset.uiPickerApplyLabel || 'Apply',
        cancelLabel: input.dataset.uiPickerCancelLabel || 'Cancel'
      }
    });
    var syncValue = function syncValue(picker) {
      if (isRange) {
        input.value = "".concat(picker.startDate.format(format), " - ").concat(picker.endDate.format(format));
        syncNamedRangeInputs(input, picker);
        input.dispatchEvent(new Event('input', {
          bubbles: true
        }));
        input.dispatchEvent(new Event('change', {
          bubbles: true
        }));
        return;
      }
      input.value = picker.startDate.format(format);
      input.dispatchEvent(new Event('input', {
        bubbles: true
      }));
      input.dispatchEvent(new Event('change', {
        bubbles: true
      }));
    };
    $(input).off('apply.daterangepicker.frontendPicker show.daterangepicker.frontendPicker').on('apply.daterangepicker.frontendPicker', function (_event, picker) {
      return syncValue(picker);
    }).on('show.daterangepicker.frontendPicker', function () {
      var picker = $(input).data('daterangepicker');
      if (picker) {
        var _picker$container$;
        picker.container.addClass("ui-picker-panel ui-picker-panel--".concat(mode));
        (_picker$container$ = picker.container[0]) === null || _picker$container$ === void 0 ? void 0 : _picker$container$.style.setProperty('--ui-picker-panel-z-index', String(resolvePanelZIndex(input)));
        picker.drops = resolveDrops(input, picker, configuredDrops);
        picker.container.toggleClass('drop-up', picker.drops === 'up').toggleClass('drop-down', picker.drops !== 'up');
        if (typeof picker.move === 'function') {
          picker.move();
        }
      }
    });
    if (!input.value && getBooleanConfig(input, 'uiPickerPrefill', 'prefill')) {
      syncValue($(input).data('daterangepicker'));
    }
    input._frontendPickerSetDate = function (value) {
      var nextDate = parseMoment(value, format);
      var picker = $(input).data('daterangepicker');
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
  var initPicker = function initPicker(input) {
    if (input.dataset.uiPickerInitialized) {
      return;
    }
    var mode = normalizeMode(input);
    if (['month', 'year', 'native-datetime'].includes(mode)) {
      setNativePickerState(input, mode);
      return;
    }
    initRangePicker(input, mode);
    input.dataset.uiPickerInitialized = 'true';
  };
  var selector = ['[data-ui-picker]', '[data-picker]', '[data-booking-datetime]', '[data-transport-datetime]', '.frontend-datepicker', '.frontend-datetimepicker', '.frontend-timepicker', '.daterangepicker-input', 'input.datetimepicker', 'input.date-picker', 'input[name="checkincout"]', 'input[type="date"]', 'input[type="datetime-local"]', 'input[type="time"]', 'input[type="month"]'].join(',');
  var safelyInitPicker = function safelyInitPicker(input) {
    try {
      initPicker(input);
    } catch (error) {
      if (window.console && typeof window.console.warn === 'function') {
        window.console.warn('Frontend picker init failed.', input, error);
      }
    }
  };
  var initAll = function initAll() {
    var _root$matches;
    var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
    if ((_root$matches = root.matches) !== null && _root$matches !== void 0 && _root$matches.call(root, selector)) {
      safelyInitPicker(root);
    }
    root.querySelectorAll(selector).forEach(safelyInitPicker);
  };
  var destroyPicker = function destroyPicker(input) {
    var _input$parentElement2;
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
    input.classList.remove('ui-picker-input', 'ui-picker-input--date', 'ui-picker-input--datetime', 'ui-picker-input--native-datetime', 'ui-picker-input--time', 'ui-picker-input--month', 'ui-picker-input--year', 'ui-picker-input--range', 'ui-picker-input--has-native-icon');
    if (input.dataset.uiPickerWrapped === 'true' && (_input$parentElement2 = input.parentElement) !== null && _input$parentElement2 !== void 0 && _input$parentElement2.classList.contains('ui-picker-field')) {
      var wrapper = input.parentElement;
      wrapper.parentNode.insertBefore(input, wrapper);
      wrapper.remove();
      delete input.dataset.uiPickerWrapped;
    }
  };
  var refreshPicker = function refreshPicker(input) {
    destroyPicker(input);
    initPicker(input);
  };
  var boot = function boot() {
    initAll();
    document.addEventListener('shown.bs.modal', function (event) {
      return initAll(event.target);
    });
    if (!document.body) {
      return;
    }
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType === Node.ELEMENT_NODE) {
            initAll(node);
          }
        });
      });
    });
    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  };
  window.FrontendPickerSystem = {
    init: initAll,
    initPicker: initPicker,
    destroy: destroyPicker,
    refresh: refreshPicker
  };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
/******/ })()
;