document.addEventListener('DOMContentLoaded', function () {
    if (!window.jQuery || typeof window.jQuery.fn.daterangepicker !== 'function' || typeof window.moment !== 'function') {
        return;
    }

    const $ = window.jQuery;

    document.querySelectorAll('[data-check-price-card]').forEach(function (checkPriceCard) {
        const stayInput = checkPriceCard.querySelector('[data-check-price-input]');
        const checkinInput = checkPriceCard.querySelector('[data-check-price-checkin]');
        const checkoutInput = checkPriceCard.querySelector('[data-check-price-checkout]');
        const note = checkPriceCard.querySelector('[data-check-price-note]');
        const warning = checkPriceCard.querySelector('[data-check-price-warning]');
        const minStay = Number.parseInt(checkPriceCard.dataset.minStay || '0', 10);
        const nightLabel = checkPriceCard.dataset.nightLabel || 'nights';

        if (!stayInput || !checkinInput || !checkoutInput) {
            return;
        }

        if ($(stayInput).data('daterangepicker')) {
            return;
        }

        const tomorrow = moment().startOf('day').add(1, 'day');
        const initialCheckin = moment(checkinInput.value, 'YYYY-MM-DD', true);
        const initialCheckout = moment(checkoutInput.value, 'YYYY-MM-DD', true);

        const fallbackStart = tomorrow.clone();
        const fallbackEnd = tomorrow.clone().add(Math.max(minStay, 1), 'days');

        const startDate = initialCheckin.isValid() && initialCheckin.isSameOrAfter(tomorrow, 'day')
            ? initialCheckin
            : fallbackStart;
        const endDate = initialCheckout.isValid() && initialCheckout.isAfter(startDate, 'day')
            ? initialCheckout
            : fallbackEnd;

        const syncStayState = function (start, end) {
            const duration = Math.max(end.diff(start, 'days'), 1);

            stayInput.value = `${start.format('YYYY-MM-DD')} - ${end.format('YYYY-MM-DD')}`;
            checkinInput.value = start.format('YYYY-MM-DD');
            checkoutInput.value = end.format('YYYY-MM-DD');

            if (note) {
                note.textContent = `${start.format('YYYY-MM-DD')} - ${end.format('YYYY-MM-DD')} | ${duration} ${nightLabel}`;
            }

            if (warning) {
                const shouldWarn = minStay > 0 && duration < minStay;
                warning.classList.toggle('d-none', !shouldWarn);
            }
        };

        $(stayInput).daterangepicker({
            autoUpdateInput: false,
            minDate: tomorrow,
            startDate,
            endDate,
            parentEl: 'body',
            opens: 'center',
            locale: {
                format: 'YYYY-MM-DD',
            },
        });

        syncStayState(startDate, endDate);

        $(stayInput).on('apply.daterangepicker', function (_event, picker) {
            syncStayState(picker.startDate.clone().startOf('day'), picker.endDate.clone().startOf('day'));
        });

        $(stayInput).on('show.daterangepicker', function () {
            const picker = $(stayInput).data('daterangepicker');

            if (picker) {
                picker.container.css('z-index', '2000');
            }
        });

        stayInput.addEventListener('click', function () {
            $(stayInput).data('daterangepicker')?.show();
        });
    });
});
