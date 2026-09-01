/******/ (() => { // webpackBootstrap
/*!***************************************************************!*\
  !*** ./resources/backend/js/operations/reservations/index.js ***!
  \***************************************************************/
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
document.addEventListener('DOMContentLoaded', function () {
  var _window$jQuery, _window$jQuery$fn;
  var page = document.querySelector('.reservations-admin-page');
  if (!page) {
    return;
  }
  var searchInput = page.querySelector('[data-reservation-filter="search"]');
  var serviceInput = page.querySelector('[data-reservation-filter="service"]');
  var rows = Array.from(page.querySelectorAll('[data-reservation-row]'));
  var emptyState = page.querySelector('[data-reservation-filter-empty]');
  var calendarElement = page.querySelector('[data-reservation-calendar]');
  var calendarEmpty = page.querySelector('[data-reservation-calendar-empty]');
  var calendarFallback = page.querySelector('[data-reservation-calendar-fallback]');
  var eventsPayload = page.querySelector('[data-reservation-calendar-events]');
  var settingsPayload = page.querySelector('[data-reservation-calendar-settings]');
  var calendarModal = document.querySelector('[data-reservation-calendar-modal]');
  var calendarEvents = [];
  var calendarSettings = {};
  var calendarReady = false;
  var calendarFilterTimer;
  try {
    calendarEvents = JSON.parse((eventsPayload === null || eventsPayload === void 0 ? void 0 : eventsPayload.textContent) || '[]');
    calendarSettings = JSON.parse((settingsPayload === null || settingsPayload === void 0 ? void 0 : settingsPayload.textContent) || '{}');
  } catch (error) {
    calendarEvents = [];
    calendarSettings = {};
  }
  var filteredCalendarEvents = function filteredCalendarEvents() {
    var search = ((searchInput === null || searchInput === void 0 ? void 0 : searchInput.value) || '').trim().toLowerCase();
    var service = ((serviceInput === null || serviceInput === void 0 ? void 0 : serviceInput.value) || '').trim().toLowerCase();
    return calendarEvents.filter(function (event) {
      return (event.search || '').includes(search) && (!service || (event.serviceKey || '') === service);
    }).map(function (event) {
      return _objectSpread(_objectSpread({}, event), {}, {
        className: ['reservations-admin-calendar-event', "reservations-admin-calendar-event--".concat(['active', 'in-service', 'overdue'].includes(event.tone) ? event.tone : 'active')]
      });
    });
  };
  var refreshCalendar = function refreshCalendar() {
    if (!calendarReady || !window.jQuery) {
      return;
    }
    var visibleEvents = filteredCalendarEvents();
    var calendar = window.jQuery(calendarElement);
    calendar.fullCalendar('removeEvents');
    calendar.fullCalendar('addEventSource', visibleEvents);
    if (calendarEmpty) {
      calendarEmpty.hidden = visibleEvents.length > 0;
    }
  };
  var scheduleCalendarRefresh = function scheduleCalendarRefresh() {
    window.clearTimeout(calendarFilterTimer);
    calendarFilterTimer = window.setTimeout(refreshCalendar, 150);
  };
  var filterRows = function filterRows() {
    var search = ((searchInput === null || searchInput === void 0 ? void 0 : searchInput.value) || '').trim().toLowerCase();
    var service = ((serviceInput === null || serviceInput === void 0 ? void 0 : serviceInput.value) || '').trim().toLowerCase();
    var visibleCards = 0;
    rows.forEach(function (row) {
      var matches = (row.dataset.reservationSearch || '').includes(search) && (!service || (row.dataset.reservationService || '') === service);
      row.hidden = !matches;
      if (matches && row.matches('article')) {
        visibleCards += 1;
      }
    });
    if (emptyState) {
      emptyState.hidden = visibleCards > 0 || rows.length === 0;
    }
    scheduleCalendarRefresh();
  };
  [searchInput, serviceInput].forEach(function (control) {
    control === null || control === void 0 ? void 0 : control.addEventListener(control.tagName === 'SELECT' ? 'change' : 'input', filterRows);
  });
  page.querySelectorAll('[data-reservation-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var reference = button.dataset.reservationDelete || '';
      var template = page.dataset.reservationConfirm || 'Remove reservation :reference?';
      if (!window.confirm(template.replace(':reference', reference))) {
        event.preventDefault();
      }
    });
  });
  var setCalendarDetail = function setCalendarDetail(field, value) {
    var element = calendarModal === null || calendarModal === void 0 ? void 0 : calendarModal.querySelector("[data-calendar-detail=\"".concat(field, "\"]"));
    if (element) {
      element.textContent = value || '-';
    }
  };
  if (calendarElement && (_window$jQuery = window.jQuery) !== null && _window$jQuery !== void 0 && (_window$jQuery$fn = _window$jQuery.fn) !== null && _window$jQuery$fn !== void 0 && _window$jQuery$fn.fullCalendar) {
    var compact = window.matchMedia('(max-width: 767px)').matches;
    var calendar = window.jQuery(calendarElement);
    var initialEvents = filteredCalendarEvents();
    calendar.fullCalendar({
      themeSystem: 'bootstrap4',
      defaultView: compact ? 'listMonth' : 'month',
      header: compact ? {
        left: 'prev,next',
        center: 'title',
        right: 'today,listMonth'
      } : {
        left: 'prev,next today',
        center: 'month,agendaWeek,listMonth',
        right: 'title'
      },
      buttonText: {
        today: calendarSettings.today,
        month: calendarSettings.month,
        week: calendarSettings.week,
        list: calendarSettings.list
      },
      monthNames: calendarSettings.monthNames,
      monthNamesShort: calendarSettings.monthNamesShort,
      dayNames: calendarSettings.dayNames,
      dayNamesShort: calendarSettings.dayNamesShort,
      allDayText: calendarSettings.allDay,
      noEventsMessage: calendarSettings.empty,
      eventLimitText: function eventLimitText(count) {
        return "+".concat(count, " ").concat(calendarSettings.more);
      },
      events: initialEvents,
      editable: false,
      selectable: false,
      navLinks: true,
      eventLimit: 3,
      eventLimitClick: 'popover',
      fixedWeekCount: false,
      contentHeight: 'auto',
      handleWindowResize: true,
      eventRender: function eventRender(event, element) {
        element.attr('title', event.note || event.title);
        element.attr('aria-label', [event.title, event.period, event.note].filter(Boolean).join('. '));
      },
      eventClick: function eventClick(event, jsEvent) {
        jsEvent.preventDefault();
        setCalendarDetail('reference', event.reference);
        setCalendarDetail('service', event.service);
        setCalendarDetail('agent', event.agent);
        setCalendarDetail('period', event.period);
        setCalendarDetail('manifest', "".concat(event.guestCount, " / ").concat(event.spkCount));
        setCalendarDetail('invoice', "".concat(event.invoice, " / ").concat(event.dueDate));
        setCalendarDetail('note', event.note);
        var detailLink = calendarModal === null || calendarModal === void 0 ? void 0 : calendarModal.querySelector('[data-calendar-detail="url"]');
        if (detailLink) {
          detailLink.href = event.detailUrl;
        }
        if (calendarModal && window.showBackendModal) {
          window.showBackendModal(calendarModal);
        }
        return false;
      }
    });
    calendarReady = true;
    if (calendarEmpty) {
      calendarEmpty.hidden = initialEvents.length > 0;
    }
  } else if (calendarFallback) {
    calendarFallback.hidden = false;
  }
});
/******/ })()
;