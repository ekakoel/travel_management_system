document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.reservations-admin-page');

  if (!page) {
    return;
  }

  const searchInput = page.querySelector('[data-reservation-filter="search"]');
  const serviceInput = page.querySelector('[data-reservation-filter="service"]');
  const rows = Array.from(page.querySelectorAll('[data-reservation-row]'));
  const emptyState = page.querySelector('[data-reservation-filter-empty]');
  const calendarElement = page.querySelector('[data-reservation-calendar]');
  const calendarEmpty = page.querySelector('[data-reservation-calendar-empty]');
  const calendarFallback = page.querySelector('[data-reservation-calendar-fallback]');
  const eventsPayload = page.querySelector('[data-reservation-calendar-events]');
  const settingsPayload = page.querySelector('[data-reservation-calendar-settings]');
  const calendarModal = document.querySelector('[data-reservation-calendar-modal]');
  let calendarEvents = [];
  let calendarSettings = {};
  let calendarReady = false;
  let calendarFilterTimer;

  try {
    calendarEvents = JSON.parse(eventsPayload?.textContent || '[]');
    calendarSettings = JSON.parse(settingsPayload?.textContent || '{}');
  } catch (error) {
    calendarEvents = [];
    calendarSettings = {};
  }

  const filteredCalendarEvents = () => {
    const search = (searchInput?.value || '').trim().toLowerCase();
    const service = (serviceInput?.value || '').trim().toLowerCase();

    return calendarEvents
      .filter((event) => (event.search || '').includes(search)
        && (!service || (event.serviceKey || '') === service))
      .map((event) => ({
        ...event,
        className: [
          'reservations-admin-calendar-event',
          `reservations-admin-calendar-event--${['active', 'in-service', 'overdue'].includes(event.tone) ? event.tone : 'active'}`,
        ],
      }));
  };

  const refreshCalendar = () => {
    if (!calendarReady || !window.jQuery) {
      return;
    }

    const visibleEvents = filteredCalendarEvents();
    const calendar = window.jQuery(calendarElement);
    calendar.fullCalendar('removeEvents');
    calendar.fullCalendar('addEventSource', visibleEvents);

    if (calendarEmpty) {
      calendarEmpty.hidden = visibleEvents.length > 0;
    }
  };

  const scheduleCalendarRefresh = () => {
    window.clearTimeout(calendarFilterTimer);
    calendarFilterTimer = window.setTimeout(refreshCalendar, 150);
  };

  const filterRows = () => {
    const search = (searchInput?.value || '').trim().toLowerCase();
    const service = (serviceInput?.value || '').trim().toLowerCase();
    let visibleCards = 0;

    rows.forEach((row) => {
      const matches = (row.dataset.reservationSearch || '').includes(search)
        && (!service || (row.dataset.reservationService || '') === service);

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

  [searchInput, serviceInput].forEach((control) => {
    control?.addEventListener(control.tagName === 'SELECT' ? 'change' : 'input', filterRows);
  });

  page.querySelectorAll('[data-reservation-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const reference = button.dataset.reservationDelete || '';
      const template = page.dataset.reservationConfirm || 'Remove reservation :reference?';

      if (!window.confirm(template.replace(':reference', reference))) {
        event.preventDefault();
      }
    });
  });

  const setCalendarDetail = (field, value) => {
    const element = calendarModal?.querySelector(`[data-calendar-detail="${field}"]`);

    if (element) {
      element.textContent = value || '-';
    }
  };

  if (calendarElement && window.jQuery?.fn?.fullCalendar) {
    const compact = window.matchMedia('(max-width: 767px)').matches;
    const calendar = window.jQuery(calendarElement);

    const initialEvents = filteredCalendarEvents();

    calendar.fullCalendar({
      themeSystem: 'bootstrap4',
      defaultView: compact ? 'listMonth' : 'month',
      header: compact
        ? { left: 'prev,next', center: 'title', right: 'today,listMonth' }
        : { left: 'prev,next today', center: 'month,agendaWeek,listMonth', right: 'title' },
      buttonText: {
        today: calendarSettings.today,
        month: calendarSettings.month,
        week: calendarSettings.week,
        list: calendarSettings.list,
      },
      monthNames: calendarSettings.monthNames,
      monthNamesShort: calendarSettings.monthNamesShort,
      dayNames: calendarSettings.dayNames,
      dayNamesShort: calendarSettings.dayNamesShort,
      allDayText: calendarSettings.allDay,
      noEventsMessage: calendarSettings.empty,
      eventLimitText: (count) => `+${count} ${calendarSettings.more}`,
      events: initialEvents,
      editable: false,
      selectable: false,
      navLinks: true,
      eventLimit: 3,
      eventLimitClick: 'popover',
      fixedWeekCount: false,
      contentHeight: 'auto',
      handleWindowResize: true,
      eventRender: (event, element) => {
        element.attr('title', event.note || event.title);
        element.attr('aria-label', [event.title, event.period, event.note].filter(Boolean).join('. '));
      },
      eventClick: (event, jsEvent) => {
        jsEvent.preventDefault();
        setCalendarDetail('reference', event.reference);
        setCalendarDetail('service', event.service);
        setCalendarDetail('agent', event.agent);
        setCalendarDetail('period', event.period);
        setCalendarDetail('manifest', `${event.guestCount} / ${event.spkCount}`);
        setCalendarDetail('invoice', `${event.invoice} / ${event.dueDate}`);
        setCalendarDetail('note', event.note);

        const detailLink = calendarModal?.querySelector('[data-calendar-detail="url"]');

        if (detailLink) {
          detailLink.href = event.detailUrl;
        }

        if (calendarModal && window.showBackendModal) {
          window.showBackendModal(calendarModal);
        }

        return false;
      },
    });

    calendarReady = true;

    if (calendarEmpty) {
      calendarEmpty.hidden = initialEvents.length > 0;
    }
  } else if (calendarFallback) {
    calendarFallback.hidden = false;
  }

});
