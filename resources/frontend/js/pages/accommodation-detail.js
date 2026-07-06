document.addEventListener('DOMContentLoaded', function () {
    const roomModal = document.getElementById('roomModal');

    if (!roomModal) {
        return;
    }

    const roomModalImage = document.getElementById('roomModalImage');
    const roomModalTitle = document.getElementById('roomModalTitle');
    const roomModalPromos = document.getElementById('roomModalPromos');
    const roomModalPackages = document.getElementById('roomModalPackages');
    const bookingPeriodLabel = roomModal.getAttribute('data-label-booking-period') || 'Booking Period';
    const stayPeriodLabel = roomModal.getAttribute('data-label-stay-period') || 'Stay Period';
    const durationLabel = roomModal.getAttribute('data-label-duration') || 'Duration';

    function safeJsonParse(value) {
        if (!value) {
            return [];
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            return [];
        }
    }

    function renderPromoList(items) {
        if (!roomModalPromos) {
            return;
        }

        const list = roomModalPromos.querySelector('.room-modal-section__list');

        if (!list || !items.length) {
            roomModalPromos.classList.add('d-none');
            if (list) {
                list.innerHTML = '';
            }
            return;
        }

        list.innerHTML = items.map(function (item) {
            return (
                '<div class="room-modal-offer room-modal-offer--promo">' +
                    '<div class="room-modal-offer__title">' + item.name + '</div>' +
                    '<div class="room-modal-offer__meta"><span>' + bookingPeriodLabel + '</span><strong>' + item.booking_period + '</strong></div>' +
                    '<div class="room-modal-offer__meta"><span>' + stayPeriodLabel + '</span><strong>' + item.stay_period + '</strong></div>' +
                '</div>'
            );
        }).join('');

        roomModalPromos.classList.remove('d-none');
    }

    function renderPackageList(items) {
        if (!roomModalPackages) {
            return;
        }

        const list = roomModalPackages.querySelector('.room-modal-section__list');

        if (!list || !items.length) {
            roomModalPackages.classList.add('d-none');
            if (list) {
                list.innerHTML = '';
            }
            return;
        }

        list.innerHTML = items.map(function (item) {
            return (
                '<div class="room-modal-offer room-modal-offer--package">' +
                    '<div class="room-modal-offer__title">' + item.name + '</div>' +
                    '<div class="room-modal-offer__meta"><span>' + stayPeriodLabel + '</span><strong>' + item.stay_period + '</strong></div>' +
                    '<div class="room-modal-offer__meta"><span>' + durationLabel + '</span><strong>' + item.duration + '</strong></div>' +
                '</div>'
            );
        }).join('');

        roomModalPackages.classList.remove('d-none');
    }

    roomModal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const card = trigger && typeof trigger.closest === 'function'
            ? (trigger.closest('.accommodation-room-card') || trigger)
            : trigger;

        if (!card) {
            return;
        }

        const imageSrc = card.getAttribute('data-image');
        const roomName = card.getAttribute('data-room-name');
        const promos = safeJsonParse(card.getAttribute('data-room-promos'));
        const packages = safeJsonParse(card.getAttribute('data-room-packages'));

        roomModalImage.src = imageSrc;
        roomModalTitle.textContent = roomName;
        renderPromoList(promos);
        renderPackageList(packages);
    });
});
