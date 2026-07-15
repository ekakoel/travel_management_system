const { createFormSubmissionGuard } = require('../../components/form-submission-guard');

document.addEventListener('DOMContentLoaded', () => {
    const formatCurrency = (value) => {
        const amount = Math.max(Number(value) || 0, 0);

        return `$${amount.toLocaleString('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    };

    const mapElement = document.getElementById('tourRouteMap');
    const mapDataElement = document.querySelector('[data-tour-route-locations]');
    const routeMarkers = new Map();
    let routeLocations = [];
    let routeMap = null;
    let routePolyline = null;

    document.querySelectorAll('.tour-gallery-modal').forEach((modal) => {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.addEventListener('show.bs.modal', () => {
            document.body.classList.add('tour-gallery-modal-open');
            document.documentElement.classList.add('tour-gallery-modal-open');
        });

        modal.addEventListener('hidden.bs.modal', () => {
            if (!document.querySelector('.tour-gallery-modal.show')) {
                document.body.classList.remove('tour-gallery-modal-open');
                document.documentElement.classList.remove('tour-gallery-modal-open');
            }
        });

        modal.addEventListener('click', (event) => {
            if (event.target !== modal || !window.bootstrap?.Modal) {
                return;
            }

            window.bootstrap.Modal.getOrCreateInstance(modal).hide();
        });
    });

    document.querySelectorAll('[data-tour-gallery-showcase]').forEach((gallery) => {
        const mainButton = gallery.querySelector('[data-tour-gallery-main]');
        const mainImage = gallery.querySelector('[data-tour-gallery-main-image]');
        const currentLabel = gallery.querySelector('[data-tour-gallery-current]');
        const captionLabel = gallery.querySelector('[data-tour-gallery-caption]');
        const thumbs = [...gallery.querySelectorAll('[data-tour-gallery-thumb]')];
        const prevButton = gallery.querySelector('[data-tour-gallery-prev]');
        const nextButton = gallery.querySelector('[data-tour-gallery-next]');
        let activeIndex = thumbs.findIndex((thumb) => thumb.classList.contains('is-active'));

        if (!mainButton || !mainImage || !thumbs.length) {
            return;
        }

        activeIndex = activeIndex >= 0 ? activeIndex : 0;

        const activateGalleryImage = (nextIndex) => {
            const normalizedIndex = (nextIndex + thumbs.length) % thumbs.length;
            const activeThumb = thumbs[normalizedIndex];

            if (!activeThumb) {
                return;
            }

            activeIndex = normalizedIndex;
            thumbs.forEach((thumb, index) => {
                const isActive = index === activeIndex;
                thumb.classList.toggle('is-active', isActive);
                thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
            });

            const nextImage = activeThumb.dataset.galleryMain;
            const nextModal = activeThumb.dataset.galleryModal;

            if (nextImage && mainImage.getAttribute('src') !== nextImage) {
                mainImage.src = nextImage;
            }

            if (nextModal) {
                mainButton.setAttribute('data-bs-target', nextModal);
            }

            if (currentLabel) {
                currentLabel.textContent = String(activeIndex + 1).padStart(2, '0');
            }

            if (captionLabel) {
                captionLabel.textContent = activeThumb.dataset.galleryCaption || '';
            }

            activeThumb.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center',
            });
        };

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => activateGalleryImage(index));
        });

        prevButton?.addEventListener('click', () => activateGalleryImage(activeIndex - 1));
        nextButton?.addEventListener('click', () => activateGalleryImage(activeIndex + 1));
    });

    const keepActivePopupInView = () => {
        if (!routeMap) {
            return;
        }

        window.requestAnimationFrame(() => {
            const mapContainer = routeMap.getContainer();
            const popup = mapContainer.querySelector('.leaflet-popup');

            if (!popup) {
                return;
            }

            const mapRect = mapContainer.getBoundingClientRect();
            const popupRect = popup.getBoundingClientRect();
            const popupCenterX = popupRect.left + (popupRect.width / 2);
            const popupCenterY = popupRect.top + (popupRect.height / 2);
            const mapCenterX = mapRect.left + (mapRect.width / 2);
            const mapCenterY = mapRect.top + (mapRect.height / 2);

            routeMap.panBy([
                popupCenterX - mapCenterX,
                popupCenterY - mapCenterY,
            ], {
                animate: true,
                duration: 0.28,
            });
        });
    };

    const highlightRouteStop = (order) => {
        document.querySelectorAll('[data-tour-route-stop]').forEach((card) => {
            card.classList.toggle('is-active', card.dataset.tourRouteStop === String(order));
        });

        document.querySelectorAll('.tour-route-map__pin.is-highlighted').forEach((pin) => {
            pin.classList.remove('is-highlighted');
        });

        const marker = routeMarkers.get(String(order));

        if (!marker || !routeMap) {
            return;
        }

        const markerElement = marker.getElement();
        const markerPin = markerElement ? markerElement.querySelector('.tour-route-map__pin') : null;

        if (markerPin) {
            markerPin.classList.add('is-highlighted');
        }

        routeMap.setView(marker.getLatLng(), Math.max(routeMap.getZoom(), 13), {
            animate: true,
            duration: 0.45,
        });
        marker.openPopup();
        keepActivePopupInView();
        window.setTimeout(keepActivePopupInView, 480);
    };

    const locationsForDay = (day) => {
        if (day === 'all') {
            return routeLocations;
        }

        return routeLocations.filter((location) => String(location.day) === String(day));
    };

    const getMarkerLatLng = (location) => [
        Number(location.marker_lat ?? location.lat),
        Number(location.marker_lng ?? location.lng),
    ];

    const applyMarkerOffsets = (locations) => {
        const groups = locations.reduce((accumulator, location) => {
            const key = `${Number(location.lat).toFixed(5)}:${Number(location.lng).toFixed(5)}`;

            if (!accumulator.has(key)) {
                accumulator.set(key, []);
            }

            accumulator.get(key).push(location);
            return accumulator;
        }, new Map());

        groups.forEach((group) => {
            if (group.length === 1) {
                group[0].marker_lat = group[0].lat;
                group[0].marker_lng = group[0].lng;
                return;
            }

            const centerLat = Number(group[0].lat);
            const centerLng = Number(group[0].lng);
            const radiusMeters = Math.min(16 + group.length * 2, 28);
            const latMeters = 111320;
            const lngMeters = Math.max(111320 * Math.cos(centerLat * Math.PI / 180), 1);

            group.forEach((location, index) => {
                const angle = (Math.PI * 2 * index) / group.length - Math.PI / 2;
                location.marker_lat = centerLat + (Math.sin(angle) * radiusMeters) / latMeters;
                location.marker_lng = centerLng + (Math.cos(angle) * radiusMeters) / lngMeters;
            });
        });

        return locations;
    };

    const fitRouteLocations = (locations) => {
        if (!routeMap || !locations.length) {
            return;
        }

        const bounds = locations.map(getMarkerLatLng);

        if (bounds.length > 1) {
            routeMap.fitBounds(bounds, { padding: [48, 48], animate: true });
            return;
        }

        routeMap.setView(bounds[0], Math.max(routeMap.getZoom(), 13), {
            animate: true,
            duration: 0.35,
        });
    };

    const syncRouteDay = (day, shouldFit = true) => {
        if (!routeMap) {
            return;
        }

        const activeDay = day || mapElement?.dataset.activeDay || 'all';
        const activeLocations = locationsForDay(activeDay);
        const activeOrders = new Set(activeLocations.map((location) => String(location.order)));

        if (mapElement) {
            mapElement.dataset.activeDay = activeDay;
        }

        routeMap.closePopup();

        document.querySelectorAll('[data-tour-route-day-tab]').forEach((tab) => {
            const isActive = tab.dataset.tourRouteDayTab === String(activeDay);
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        document.querySelectorAll('[data-tour-route-day-panel]').forEach((panel) => {
            panel.classList.toggle('is-active', panel.dataset.tourRouteDayPanel === String(activeDay));
        });

        document.querySelectorAll('[data-tour-route-stop]').forEach((card) => {
            card.classList.remove('is-active');
        });

        routeMarkers.forEach((marker, order) => {
            const shouldShow = activeOrders.has(order);

            if (shouldShow && !routeMap.hasLayer(marker)) {
                marker.addTo(routeMap);
            }

            if (!shouldShow && routeMap.hasLayer(marker)) {
                routeMap.removeLayer(marker);
            }
        });

        if (routePolyline) {
            routeMap.removeLayer(routePolyline);
            routePolyline = null;
        }

        if (activeLocations.length > 1 && window.L) {
            routePolyline = window.L.polyline(activeLocations.map((location) => [location.lat, location.lng]), {
                color: '#0f766e',
                opacity: 0.82,
                weight: 4,
                dashArray: '8 10',
            }).addTo(routeMap);
        }

        if (shouldFit) {
            fitRouteLocations(activeLocations);
        }
    };

    const getMarkerHtml = (location) => {
        const color = location.color || '#0f766e';
        const displayOrder = location.display_order || location.visit_order || location.order;

        return `<span class="tour-route-map__pin tour-route-map__pin--number" style="--tour-marker-color:${color}"><strong>${displayOrder}</strong></span>`;
    };
    const createMarkerPopup = (location) => {
        const popup = document.createElement('div');
        popup.className = 'tour-route-map__popup-card tour-route-map__popup-card--compact';

        const icon = document.createElement('span');
        icon.className = 'tour-route-map__popup-icon';
        icon.style.setProperty('--tour-marker-color', location.color || '#0f766e');
        icon.innerHTML = `<i class="fa ${location.icon || 'fa-landmark'}" aria-hidden="true"></i>`;

        const title = document.createElement('div');
        title.className = 'tour-route-map__popup-title';
        title.textContent = location.visit_time ? `${location.name} (${location.visit_time})` : location.name;

        popup.appendChild(icon);
        popup.appendChild(title);

        return popup;
    };

    if (mapElement && mapDataElement && window.L && mapElement.dataset.initialized !== 'true') {
        let locations = [];

        try {
            locations = JSON.parse(mapDataElement.textContent || '[]');
        } catch (error) {
            locations = [];
        }

        if (locations.length > 0) {
            locations = applyMarkerOffsets(locations);
            mapElement.dataset.initialized = 'true';

            const map = L.map(mapElement, {
                scrollWheelZoom: false,
                zoomControl: true,
            });
            routeMap = map;

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            locations.forEach((location) => {
                const latLng = getMarkerLatLng(location);

                const marker = L.marker(latLng, {
                    icon: L.divIcon({
                        className: '',
                        html: getMarkerHtml(location),
                        iconSize: [30, 30],
                        iconAnchor: [15, 15],
                        popupAnchor: [0, -18],
                    }),
                }).addTo(map);
                marker._tourRouteLocation = location;
                routeMarkers.set(String(location.order), marker);

                const popup = document.createElement('div');
                const title = document.createElement('div');
                const meta = document.createElement('p');

                title.className = 'tour-route-map__popup-title';
                title.textContent = location.name;
                meta.className = 'tour-route-map__popup-meta';
                meta.textContent = [
                    `${mapElement.dataset.dayLabel || 'Day'} ${location.day}`,
                    `${mapElement.dataset.stopLabel || 'Stop'} ${location.visit_order}`,
                    location.visit_time ? `${mapElement.dataset.timeLabel || 'Time'} ${location.visit_time}` : null,
                ].filter(Boolean).join(' · ');

                popup.appendChild(title);
                popup.appendChild(meta);

                marker.bindPopup(createMarkerPopup(location), {
                    closeButton: false,
                    minWidth: 230,
                    autoPan: true,
                    autoPanPadding: [28, 28],
                    keepInView: true,
                });
            });

            routeLocations = locations;
            syncRouteDay(mapElement.dataset.activeDay || 'all', true);

            window.setTimeout(() => map.invalidateSize(), 250);
        }
    }

    async function ensureTourRouteMap() {
        if (!mapElement || !mapDataElement || mapElement.dataset.initialized === 'true') {
            return;
        }

        let locations = [];

        try {
            locations = JSON.parse(mapDataElement.textContent || '[]');
        } catch (error) {
            locations = [];
        }

        if (!locations.length) {
            return;
        }

        if (!window.L) {
            try {
                const fallbackCss = document.createElement('link');
                fallbackCss.rel = 'stylesheet';
                fallbackCss.href = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(fallbackCss);

                await new Promise((resolve, reject) => {
                    const fallbackScript = document.createElement('script');
                    fallbackScript.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js';
                    fallbackScript.onload = resolve;
                    fallbackScript.onerror = reject;
                    document.head.appendChild(fallbackScript);
                });
            } catch (error) {
                mapElement.classList.add('tour-route-map__canvas--fallback');
                mapElement.innerHTML = '';

                const fallback = document.createElement('div');
                fallback.className = 'tour-route-map__fallback';

                locations.forEach((location) => {
                    const item = document.createElement('div');
                    item.className = 'tour-route-map__fallback-marker';

                    const avatar = document.createElement('span');
                    avatar.className = 'tour-route-map__pin';

                    const number = document.createElement('strong');
                    number.textContent = location.order;

                    const body = document.createElement('div');
                    const title = document.createElement('p');
                    const meta = document.createElement('small');

                    title.textContent = location.name;
                    meta.textContent = `${mapElement.dataset.dayLabel || 'Day'} ${location.day} - ${mapElement.dataset.stopLabel || 'Stop'} ${location.visit_order}`;

                    avatar.classList.add('tour-route-map__pin--number');
                    avatar.style.setProperty('--tour-marker-color', location.color || '#0f766e');
                    avatar.appendChild(number);
                    body.appendChild(title);
                    body.appendChild(meta);
                    item.appendChild(avatar);
                    item.appendChild(body);
                    fallback.appendChild(item);
                });

                mapElement.appendChild(fallback);
                return;
            }
        }

        if (!window.L) {
            return;
        }

        mapElement.dataset.initialized = 'true';
        mapElement.innerHTML = '';

        const map = L.map(mapElement, {
            scrollWheelZoom: false,
            zoomControl: true,
        });
        routeMap = map;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        locations = applyMarkerOffsets(locations);

        locations.forEach((location) => {
            const latLng = getMarkerLatLng(location);

            const marker = L.marker(latLng, {
                icon: L.divIcon({
                    className: '',
                    html: getMarkerHtml(location),
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -18],
                }),
            }).addTo(map);
            marker._tourRouteLocation = location;
            routeMarkers.set(String(location.order), marker);

            const popup = document.createElement('div');
            const title = document.createElement('div');
            const meta = document.createElement('p');

            title.className = 'tour-route-map__popup-title';
            title.textContent = location.name;
            meta.className = 'tour-route-map__popup-meta';
            meta.textContent = [
                `${mapElement.dataset.dayLabel || 'Day'} ${location.day}`,
                `${mapElement.dataset.stopLabel || 'Stop'} ${location.visit_order}`,
                location.visit_time ? `${mapElement.dataset.timeLabel || 'Time'} ${location.visit_time}` : null,
            ].filter(Boolean).join(' - ');

            popup.appendChild(title);
            popup.appendChild(meta);

            marker.bindPopup(createMarkerPopup(location), {
                closeButton: false,
                minWidth: 230,
                autoPan: true,
                autoPanPadding: [28, 28],
                keepInView: true,
            });
        });

        routeLocations = locations;
        syncRouteDay(mapElement.dataset.activeDay || 'all', true);

        window.setTimeout(() => map.invalidateSize(), 250);
    }

    ensureTourRouteMap();

    document.addEventListener('click', (event) => {
        const dayTab = event.target.closest('[data-tour-route-day-tab]');

        if (dayTab) {
            syncRouteDay(dayTab.dataset.tourRouteDayTab, true);
            window.setTimeout(() => routeMap?.invalidateSize(), 80);
            return;
        }

        const stopCard = event.target.closest('[data-tour-route-stop]');

        if (!stopCard) {
            return;
        }

        highlightRouteStop(stopCard.dataset.tourRouteStop);
    });

    const orderForm = document.querySelector('[data-tour-order-form]');
    const reservationModalElement = document.getElementById('tourReservationModal');

    if (!orderForm) {
        return;
    }

    let rates = [];

    try {
        rates = JSON.parse(orderForm.dataset.rates || '[]');
    } catch (error) {
        rates = [];
    }

    const guestInput = orderForm.querySelector('[data-tour-guests]');
    const selectedPriceId = orderForm.querySelector('[data-tour-price-id]');
    const pricePerPax = orderForm.querySelector('[data-tour-price-per-pax]');
    const totalPrice = orderForm.querySelector('[data-tour-total-price]');
    const priceNote = orderForm.querySelector('[data-tour-price-note]');
    const submitButton = orderForm.querySelector('button[type="submit"]');
    const guestError = orderForm.querySelector('[data-tour-guest-error]');
    const guestTableBody = orderForm.querySelector('[data-tour-guest-table-body]');
    const guestEmptyRow = orderForm.querySelector('[data-tour-guest-empty-row]');
    const guestInputsTarget = orderForm.querySelector('[data-tour-guest-inputs]');
    const guestProgressTarget = orderForm.querySelector('[data-tour-guest-progress]');
    const reviewGuestTableBody = orderForm.querySelector('[data-tour-review-guest-table-body]');
    const reviewGuestEmptyRow = orderForm.querySelector('[data-tour-review-guest-empty-row]');
    const guestEditIndexInput = orderForm.querySelector('[data-tour-guest-edit-index]');
    const guestSaveButton = orderForm.querySelector('[data-tour-guest-save]');
    const guestCancelButton = orderForm.querySelector('[data-tour-guest-cancel]');
    const guestFieldElements = {
        name: orderForm.querySelector('[data-tour-guest-field="name"]'),
        phone: orderForm.querySelector('[data-tour-guest-field="phone"]'),
        age: orderForm.querySelector('[data-tour-guest-field="age"]'),
        sex: orderForm.querySelector('[data-tour-guest-field="sex"]'),
        identification_type: orderForm.querySelector('[data-tour-guest-field="identification_type"]'),
        identification_no: orderForm.querySelector('[data-tour-guest-field="identification_no"]'),
        is_leader: orderForm.querySelector('[data-tour-guest-field="is_leader"]'),
    };
    const leadGuestName = orderForm.querySelector('[data-tour-lead-name]');
    const leadGuestPhone = orderForm.querySelector('[data-tour-lead-phone]');
    const reviewFields = [...orderForm.querySelectorAll('[data-tour-review-field]')];
    const reviewValues = [...orderForm.querySelectorAll('[data-tour-review-value]')];
    const wizardSteps = [...orderForm.querySelectorAll('[data-tour-wizard-step]')];
    const wizardNavItems = [...orderForm.querySelectorAll('[data-tour-wizard-nav]')];
    const previousStepButtons = [...orderForm.querySelectorAll('[data-tour-wizard-prev]')];
    const nextStepButtons = [...orderForm.querySelectorAll('[data-tour-wizard-next]')];
    const wizardSubmitButtons = [...orderForm.querySelectorAll('[data-tour-wizard-submit]')];
    const wizardSubmitButton = wizardSubmitButtons[0] || null;
    const submitOverlay = orderForm.querySelector('[data-form-submit-overlay]');
    const bookingDiscount = Number(orderForm.dataset.bookingDiscount || 0);
    const promotionDiscount = Number(orderForm.dataset.promotionDiscount || 0);
    const submissionGuard = createFormSubmissionGuard(orderForm, {
        storageKey: orderForm.dataset.submissionKey || `tour-order:${window.location.pathname}`,
    });
    const leaderLabel = orderForm.dataset.leaderLabel || 'Leader';
    const setLeaderLabel = orderForm.dataset.setLeaderLabel || 'Set leader';
    const leaderPhoneRequiredLabel = orderForm.dataset.leaderPhoneRequiredLabel || 'Phone required';
    const guestLabel = orderForm.dataset.guestLabel || 'Guest';
    const adultLabel = orderForm.dataset.adultLabel || 'Adult';
    const childLabel = orderForm.dataset.childLabel || 'Child';
    const maleLabel = orderForm.dataset.maleLabel || 'Male';
    const femaleLabel = orderForm.dataset.femaleLabel || 'Female';
    const editLabel = orderForm.dataset.editLabel || 'Edit';
    const removeLabel = orderForm.dataset.removeLabel || 'Remove';
    const addGuestLabel = orderForm.dataset.addGuestLabel || 'Add';
    const updateGuestLabel = orderForm.dataset.updateGuestLabel || 'Update';
    const cancelEditLabel = orderForm.dataset.cancelEditLabel || 'Cancel';
    const guestTableEmptyLabel = orderForm.dataset.guestTableEmptyLabel || 'No guest has been added yet.';
    const guestProgressLabel = orderForm.dataset.guestProgressLabel || ':count guest details saved for this booking of :total pax';
    const guestCountMismatchLabel = orderForm.dataset.guestCountMismatchLabel || 'Please add at least one guest detail.';
    let guests = [];
    let isSubmitting = false;

    try {
        guests = JSON.parse(orderForm.dataset.initialGuests || '[]')
            .filter((guest) => Object.values(guest || {}).some((value) => value !== null && value !== ''))
            .map((guest) => ({
                name: String(guest.name || '').trim(),
                phone: String(guest.phone || '').trim(),
                age: String(guest.age || '').trim(),
                sex: String(guest.sex || '').trim(),
                identification_type: String(guest.identification_type || '').trim(),
                identification_no: String(guest.identification_no || '').trim(),
                is_leader: Boolean(Number(guest.is_leader || 0)),
            }));
    } catch (error) {
        guests = [];
    }

    const normalizeLeaderEligibility = (guestList) => guestList.map((guest) => ({
        ...guest,
        is_leader: Boolean(guest.is_leader),
    }));

    const getRequestedGuestCount = ({ allowIncomplete = false } = {}) => {
        const minGuests = Number(guestInput?.getAttribute('min') || 2);
        const maxGuests = Number(guestInput?.getAttribute('max') || 200);
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

    guests = normalizeLeaderEligibility(guests);
    let activeWizardStep = 0;

    const focusFirstInvalidField = (container) => {
        const invalidField = container?.querySelector('.is-invalid, :invalid');

        if (invalidField && typeof invalidField.focus === 'function') {
            invalidField.focus({ preventScroll: false });
        }
    };

    if (reservationModalElement && orderForm.dataset.openOnLoad === 'true' && window.bootstrap?.Modal) {
        window.setTimeout(() => {
            window.bootstrap.Modal.getOrCreateInstance(reservationModalElement).show();
        }, 120);
    }

    const setSubmittingState = (submitting) => {
        const processingLabel = orderForm.dataset.processingLabel || 'Processing...';
        isSubmitting = Boolean(submitting);
        orderForm.dataset.isSubmitting = isSubmitting ? 'true' : 'false';
        orderForm.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');
        orderForm.toggleAttribute('inert', isSubmitting);
        document.documentElement.classList.toggle('tour-submit-locked', isSubmitting);
        document.body.classList.toggle('tour-submit-locked', isSubmitting);
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

        [...previousStepButtons, ...nextStepButtons, ...wizardSubmitButtons]
            .forEach((button) => {
                const originalHtml = button.dataset.originalHtml || button.innerHTML;
                button.dataset.originalHtml = originalHtml;
                button.disabled = isSubmitting;
                button.classList.toggle('is-processing', isSubmitting && button === wizardSubmitButton);

                if (button !== wizardSubmitButton) {
                    return;
                }

                button.innerHTML = isSubmitting
                    ? '<span class="booking-submit-button__spinner" aria-hidden="true"></span><span>' + processingLabel + '</span>'
                    : originalHtml;
            });
    };

    const attemptOrderSubmit = () => {
        if (isSubmitting) {
            return;
        }

        for (let index = 0; index < wizardSteps.length; index += 1) {
            if (!validateWizardStep(index, false)) {
                showWizardStep(index);
                window.setTimeout(() => focusFirstInvalidField(wizardSteps[index]), 80);
                return;
            }
        }

        if (wizardSubmitButton?.disabled) {
            showWizardStep(wizardSteps.length - 1);
            return;
        }

        setSubmittingState(true);
        submissionGuard.markSubmitted();
        const submitForm = () => {
            if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype.submit) {
                HTMLFormElement.prototype.submit.call(orderForm);
                return;
            }

            orderForm.submit();
        };

        if (typeof window.requestAnimationFrame === 'function') {
            window.requestAnimationFrame(() => {
                window.setTimeout(submitForm, 0);
            });
            return;
        }

        window.setTimeout(submitForm, 0);
    };

    const validateWizardStep = (stepIndex, focusInvalid = true) => {
        const step = wizardSteps[stepIndex];

        if (!step) {
            return true;
        }

        let isValid = true;
        const fields = [...step.querySelectorAll('input, select, textarea')].filter((field) => {
            return !field.disabled && field.type !== 'hidden';
        });

        fields.forEach((field) => {
            const fieldIsValid = field.checkValidity();
            field.classList.toggle('is-invalid', !fieldIsValid);

            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (step?.querySelector('[data-tour-guest-table-body]')) {
            isValid = validateGuestManifest(true) && isValid;
        }

        if (!isValid && focusInvalid) {
            focusFirstInvalidField(step);
        }

        return isValid;
    };

    const showWizardStep = (stepIndex) => {
        if (!wizardSteps.length) {
            return;
        }

        activeWizardStep = Math.min(Math.max(stepIndex, 0), wizardSteps.length - 1);

        wizardSteps.forEach((step, index) => {
            const isActive = index === activeWizardStep;
            step.hidden = !isActive;
            step.classList.toggle('is-active', isActive);
        });

        wizardNavItems.forEach((item, index) => {
            item.classList.toggle('is-active', index === activeWizardStep);
            item.classList.toggle('is-complete', index < activeWizardStep);
        });

        if (activeWizardStep === wizardSteps.length - 1) {
            updateReservationReview();
        }

        wizardSteps[activeWizardStep]?.scrollIntoView({ block: 'start', behavior: 'smooth' });
    };

    orderForm.addEventListener('input', (event) => {
        if (event.target.matches('input, select, textarea')) {
            event.target.classList.remove('is-invalid');
        }
    });

    nextStepButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const currentPanel = button.closest('[data-tour-wizard-step]');
            const currentStep = currentPanel ? wizardSteps.indexOf(currentPanel) : activeWizardStep;

            if (!validateWizardStep(currentStep)) {
                return;
            }

            showWizardStep(currentStep + 1);
        });
    });

    previousStepButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const currentPanel = button.closest('[data-tour-wizard-step]');
            const currentStep = currentPanel ? wizardSteps.indexOf(currentPanel) : activeWizardStep;
            showWizardStep(currentStep - 1);
        });
    });

    wizardNavItems.forEach((item) => {
        item.addEventListener('click', () => {
            const targetStep = Number(item.dataset.tourWizardNav || 0);

            if (targetStep <= activeWizardStep) {
                showWizardStep(targetStep);
                return;
            }

            for (let index = activeWizardStep; index < targetStep; index += 1) {
                if (!validateWizardStep(index, false)) {
                    showWizardStep(index);
                    window.setTimeout(() => focusFirstInvalidField(wizardSteps[index]), 80);
                    return;
                }
            }

            showWizardStep(targetStep);
        });
    });

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const syncLeadGuestFromLeader = () => {
        const leader = guests.find((guest) => guest.is_leader);

        if (leadGuestName) {
            leadGuestName.value = leader?.name || '';
        }

        if (leadGuestPhone) {
            leadGuestPhone.value = leader?.phone || '';
        }
    };

    const localizeGuestAge = (value) => value === 'Adult' ? adultLabel : (value === 'Child' ? childLabel : value);
    const localizeGuestSex = (value) => value === 'Male' ? maleLabel : (value === 'Female' ? femaleLabel : value);
    const getEditingIndex = () => {
        const value = guestEditIndexInput?.value || '';
        return value === '' ? null : Number(value);
    };
    const setEditingIndex = (index) => {
        if (guestEditIndexInput) {
            guestEditIndexInput.value = Number.isInteger(index) ? String(index) : '';
        }
    };
    const clearGuestFormErrors = () => {
        Object.values(guestFieldElements).forEach((field) => field?.classList.remove('is-invalid'));
    };
    const resetGuestForm = () => {
        clearGuestFormErrors();
        Object.entries(guestFieldElements).forEach(([key, field]) => {
            if (!field) return;
            if (key === 'is_leader') {
                field.checked = false;
                return;
            }
            field.value = '';
        });
        setEditingIndex(null);
        if (guestSaveButton) guestSaveButton.textContent = addGuestLabel;
        if (guestCancelButton) {
            guestCancelButton.textContent = cancelEditLabel;
            guestCancelButton.hidden = true;
        }
    };
    const fillGuestForm = (guest = {}) => {
        Object.entries(guestFieldElements).forEach(([key, field]) => {
            if (!field) return;
            if (key === 'is_leader') {
                field.checked = Boolean(guest.is_leader);
                return;
            }
            field.value = guest[key] || '';
        });
    };
    const getGuestDraft = () => ({
        name: String(guestFieldElements.name?.value || '').trim(),
        phone: String(guestFieldElements.phone?.value || '').trim(),
        age: String(guestFieldElements.age?.value || '').trim(),
        sex: String(guestFieldElements.sex?.value || '').trim(),
        identification_type: String(guestFieldElements.identification_type?.value || '').trim(),
        identification_no: String(guestFieldElements.identification_no?.value || '').trim(),
        is_leader: Boolean(guestFieldElements.is_leader?.checked),
    });
    const validateGuestDraft = (focusInvalid = false) => {
        const draft = getGuestDraft();
        const requiredFields = ['name', 'age', 'sex', 'identification_type', 'identification_no'];
        let firstInvalidField = null;

        clearGuestFormErrors();
        requiredFields.forEach((fieldName) => {
            if (!draft[fieldName] && guestFieldElements[fieldName]) {
                guestFieldElements[fieldName].classList.add('is-invalid');
                firstInvalidField = firstInvalidField || guestFieldElements[fieldName];
            }
        });
        if (draft.is_leader && !draft.phone && guestFieldElements.phone) {
            guestFieldElements.phone.classList.add('is-invalid');
            firstInvalidField = firstInvalidField || guestFieldElements.phone;
        }
        if (focusInvalid && firstInvalidField) firstInvalidField.focus();
        return !firstInvalidField;
    };
    const setGuestErrorMessage = (message = '', visible = false) => {
        if (!guestError) return;
        guestError.hidden = !visible;
        if (visible) guestError.textContent = message;
    };
    const renderGuestProgress = () => {
        if (!guestProgressTarget) return;
        const requestedGuestCount = getRequestedGuestCount({ allowIncomplete: true }) ?? 0;
        guestProgressTarget.textContent = guestProgressLabel
            .replace(':count', String(guests.length))
            .replace(':total', String(requestedGuestCount));
    };
    const renderGuestHiddenInputs = () => {
        if (!guestInputsTarget) return;
        guestInputsTarget.innerHTML = guests.map((guest, index) => `
            <input type="hidden" name="guests[${index}][name]" value="${escapeHtml(guest.name)}">
            <input type="hidden" name="guests[${index}][phone]" value="${escapeHtml(guest.phone)}">
            <input type="hidden" name="guests[${index}][age]" value="${escapeHtml(guest.age)}">
            <input type="hidden" name="guests[${index}][sex]" value="${escapeHtml(guest.sex)}">
            <input type="hidden" name="guests[${index}][identification_type]" value="${escapeHtml(guest.identification_type)}">
            <input type="hidden" name="guests[${index}][identification_no]" value="${escapeHtml(guest.identification_no)}">
            <input type="hidden" name="guests[${index}][is_leader]" value="${guest.is_leader ? '1' : '0'}">
        `).join('');
        syncLeadGuestFromLeader();
    };
    const renderGuestTable = () => {
        if (!guestTableBody) return;
        guestTableBody.querySelectorAll('[data-tour-guest-row]').forEach((row) => row.remove());
        guests.forEach((guest, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-tour-guest-row', 'true');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${escapeHtml(guest.name || `${guestLabel} ${index + 1}`)}</td>
                <td>${escapeHtml(localizeGuestAge(guest.age) || '-')}</td>
                <td>${escapeHtml(localizeGuestSex(guest.sex) || '-')}</td>
                <td>${escapeHtml(guest.phone || '-')}</td>
                <td>${escapeHtml(guest.identification_type || '-')}</td>
                <td>${escapeHtml(guest.identification_no || '-')}</td>
                <td>${guest.is_leader ? escapeHtml(leaderLabel) : '-'}</td>
                <td><div class="tour-guest-table__actions">
                    <button type="button" class="tour-guest-table__action" data-tour-guest-edit="${index}"><i class="fa fa-edit" aria-hidden="true"></i><span>${escapeHtml(editLabel)}</span></button>
                    <button type="button" class="tour-guest-table__action tour-guest-table__action--danger" data-tour-guest-remove="${index}"><i class="fa fa-trash" aria-hidden="true"></i><span>${escapeHtml(removeLabel)}</span></button>
                </div></td>
            `;
            guestTableBody.appendChild(row);
        });
        if (guestEmptyRow) {
            guestEmptyRow.hidden = guests.length > 0;
            const emptyCell = guestEmptyRow.querySelector('td');
            if (emptyCell) emptyCell.textContent = guestTableEmptyLabel;
        }
        renderGuestHiddenInputs();
        renderGuestProgress();
    };
    const renderReviewGuestTable = () => {
        if (!reviewGuestTableBody) return;
        reviewGuestTableBody.querySelectorAll('[data-tour-review-guest-row]').forEach((row) => row.remove());
        guests.forEach((guest, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-tour-review-guest-row', 'true');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${escapeHtml(guest.name || `${guestLabel} ${index + 1}`)}</td>
                <td>${escapeHtml(localizeGuestAge(guest.age) || '-')}</td>
                <td>${escapeHtml(localizeGuestSex(guest.sex) || '-')}</td>
                <td>${escapeHtml(guest.phone || '-')}</td>
                <td>${escapeHtml(guest.identification_type || '-')}</td>
                <td>${escapeHtml(guest.identification_no || '-')}</td>
                <td>${guest.is_leader ? escapeHtml(leaderLabel) : '-'}</td>
            `;
            reviewGuestTableBody.appendChild(row);
        });
        if (reviewGuestEmptyRow) {
            reviewGuestEmptyRow.hidden = guests.length > 0;
            const emptyCell = reviewGuestEmptyRow.querySelector('td');
            if (emptyCell) emptyCell.textContent = guestTableEmptyLabel;
        }
    };
    const validateGuestManifest = (showMessage = false) => {
        const leader = guests.find((guest) => guest.is_leader && guest.phone);
        const isValid = guests.length > 0 && Boolean(leader);
        const message = guests.length > 0 ? leaderPhoneRequiredLabel : guestCountMismatchLabel;
        setGuestErrorMessage(message, showMessage && !isValid);
        return isValid;
    };
    const updateReservationReview = () => {
        const valueMap = reviewFields.reduce((values, field) => {
            values[field.dataset.tourReviewField] = field.value || '-';
            return values;
        }, {});
        const leader = guests.find((guest) => guest.is_leader);

        valueMap.leader = leader ? `${leader.name}${leader.phone ? ` (${leader.phone})` : ''}` : '-';
        valueMap.guestManifest = guests.length ? `${guests.length} guest${guests.length > 1 ? 's' : ''}` : '-';

        reviewValues.forEach((target) => {
            target.textContent = valueMap[target.dataset.tourReviewValue] || '-';
        });
        renderReviewGuestTable();
        renderGuestProgress();
    };

    const persistGuestDraft = () => {
        if (!validateGuestDraft(true)) return;
        const editingIndex = getEditingIndex();
        const draft = getGuestDraft();
        if (draft.is_leader) guests = guests.map((guest) => ({ ...guest, is_leader: false }));
        if (editingIndex !== null && guests[editingIndex]) guests[editingIndex] = draft;
        else guests.push(draft);
        resetGuestForm();
        setGuestErrorMessage('', false);
        renderGuestTable();
        updateReservationReview();
    };

    guestSaveButton?.addEventListener('click', persistGuestDraft);
    guestCancelButton?.addEventListener('click', resetGuestForm);
    guestTableBody?.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-tour-guest-edit]');
        const removeButton = event.target.closest('[data-tour-guest-remove]');
        if (editButton) {
            const index = Number(editButton.dataset.tourGuestEdit);
            if (!Number.isInteger(index) || !guests[index]) return;
            setEditingIndex(index);
            fillGuestForm(guests[index]);
            if (guestSaveButton) guestSaveButton.textContent = updateGuestLabel;
            if (guestCancelButton) guestCancelButton.hidden = false;
            guestFieldElements.name?.focus();
            return;
        }
        if (removeButton) {
            const index = Number(removeButton.dataset.tourGuestRemove);
            if (!Number.isInteger(index) || !guests[index]) return;
            guests.splice(index, 1);
            resetGuestForm();
            renderGuestTable();
            updateReservationReview();
            validateGuestManifest(false);
        }
    });

    reviewFields.forEach((field) => {
        field.addEventListener('input', updateReservationReview);
        field.addEventListener('change', updateReservationReview);
    });

    wizardSubmitButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            attemptOrderSubmit();
        });
    });

    const getHighestRate = () => [...rates].sort((a, b) => Number(b.max_qty) - Number(a.max_qty))[0];
    const updatePricePreview = () => {
        const guests = Number(guestInput?.value || 0);
        const normalizedGuests = Number.isFinite(guests) ? Math.trunc(guests) : 0;
        let matchedRate = rates.find((rate) => {
            return normalizedGuests >= Number(rate.min_qty) && normalizedGuests <= Number(rate.max_qty);
        });
        const isFallbackRate = !matchedRate && normalizedGuests >= 2 && normalizedGuests <= 200;

        if (isFallbackRate) {
            matchedRate = getHighestRate();
        }

        if (!matchedRate || normalizedGuests < 2 || normalizedGuests > 200) {
            if (pricePerPax) pricePerPax.textContent = '-';
            if (totalPrice) totalPrice.textContent = '-';
            if (priceNote) priceNote.textContent = orderForm.dataset.noRateLabel || 'No matching active rate for this guest count yet.';
            if (selectedPriceId) selectedPriceId.value = '';
            if (submitButton) submitButton.disabled = true;
            return;
        }

        const unitPrice = Number(matchedRate.price || 0);
        const grossTotal = unitPrice * normalizedGuests;
        const finalTotal = Math.max(grossTotal - bookingDiscount - promotionDiscount, 0);

        if (selectedPriceId) selectedPriceId.value = matchedRate.id || '';
        if (pricePerPax) pricePerPax.textContent = formatCurrency(unitPrice);
        if (totalPrice) totalPrice.textContent = formatCurrency(finalTotal);
        if (priceNote) priceNote.textContent = '';
        if (submitButton) submitButton.disabled = false;
    };

    guestInput?.addEventListener('input', () => {
        updatePricePreview();
        renderGuestProgress();
        updateReservationReview();
        validateGuestManifest(false);
    });
    guestInput?.addEventListener('change', () => {
        updatePricePreview();
        renderGuestProgress();
        updateReservationReview();
        validateGuestManifest(false);
    });
    updatePricePreview();
    renderGuestTable();
    updateReservationReview();
    validateGuestManifest(false);
    const initialWizardStep = Number(orderForm.dataset.initialStep || 0);
    showWizardStep(Number.isFinite(initialWizardStep) ? initialWizardStep : 0);

    orderForm.addEventListener('submit', (event) => {
        if (isSubmitting) {
            return;
        }
        event.preventDefault();
        attemptOrderSubmit();
    });
    reservationModalElement?.addEventListener('hide.bs.modal', (event) => {
        if (isSubmitting) {
            event.preventDefault();
        }
    });
    submissionGuard.bindHistoryRestore(() => {
        setSubmittingState(false);
        window.location.reload();
    });
});
