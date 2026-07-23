document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-transport-spk-detail]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const labels = page ? {
        sending: page.dataset.labelSending || 'Sending...',
        sent: page.dataset.labelSent || 'Sent',
        sendFailed: page.dataset.labelSendFailed || 'Unable to send WhatsApp message.',
        missingPhone: page.dataset.labelMissingPhone || 'Phone number is missing.',
        selectTime: page.dataset.labelSelectTime || 'Please select both hour and minute.',
        checking: page.dataset.labelChecking || 'Checking...',
        connected: page.dataset.labelConnected || 'Connected',
        notConnected: page.dataset.labelNotConnected || 'Not connected',
        requestFailed: page.dataset.labelRequestFailed || 'Request failed',
        loadingQr: page.dataset.labelLoadingQr || 'Loading QR...',
        waitingQr: page.dataset.labelWaitingQr || 'Waiting for QR...',
    } : {};

    const confirmForms = () => {
        document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(form.dataset.confirmDelete)) {
                    event.preventDefault();
                }
            });
        });
    };

    const submitLoading = () => {
        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form');
            if (!form) {
                return;
            }

            let button = form.querySelector('[type="submit"]');
            if (!button && form.id) {
                button = document.querySelector(`[type="submit"][form="${form.id}"]`);
            }

            if (!button) {
                return;
            }

            button.disabled = true;
            button.setAttribute('aria-disabled', 'true');
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        }, true);
    };

    const initTimePicker = () => {
        const picker = document.getElementById('transportSpkTimePicker');
        if (!picker) {
            return;
        }

        const hours = picker.querySelector('[data-time-hours]');
        const minutes = picker.querySelector('[data-time-minutes]');
        const close = picker.querySelector('[data-time-close]');
        const set = picker.querySelector('[data-time-set]');
        let activeInput = null;
        let selectedHour = null;
        let selectedMinute = null;

        for (let h = 0; h < 24; h += 1) {
            const value = h.toString().padStart(2, '0');
            const option = document.createElement('div');
            option.dataset.hour = value;
            option.textContent = value;
            hours.appendChild(option);
        }

        for (let m = 0; m < 60; m += 1) {
            const value = m.toString().padStart(2, '0');
            const option = document.createElement('div');
            option.dataset.minute = value;
            option.textContent = value;
            minutes.appendChild(option);
        }

        const hidePicker = () => {
            picker.style.display = 'none';
        };

        const clearActive = () => {
            hours.querySelectorAll('div').forEach((item) => item.classList.remove('active'));
            minutes.querySelectorAll('div').forEach((item) => item.classList.remove('active'));
        };

        document.addEventListener('click', (event) => {
            const input = event.target.closest('.time-input');
            if (input) {
                event.stopPropagation();
                activeInput = input;
                const rect = input.getBoundingClientRect();

                picker.style.top = `${rect.bottom + window.scrollY + 8}px`;
                picker.style.left = `${rect.left + window.scrollX}px`;
                picker.style.display = 'block';

                clearActive();
                const currentValue = input.value;
                if (/^\d{2}:\d{2}$/.test(currentValue)) {
                    const [hour, minute] = currentValue.split(':');
                    hours.querySelector(`[data-hour="${hour}"]`)?.classList.add('active');
                    minutes.querySelector(`[data-minute="${minute}"]`)?.classList.add('active');
                    selectedHour = hour;
                    selectedMinute = minute;
                } else {
                    selectedHour = null;
                    selectedMinute = null;
                }
                return;
            }

            if (!event.target.closest('#transportSpkTimePicker')) {
                hidePicker();
            }
        });

        close?.addEventListener('click', hidePicker);
        hours.addEventListener('click', (event) => {
            const option = event.target.closest('[data-hour]');
            if (!option) {
                return;
            }
            hours.querySelectorAll('div').forEach((item) => item.classList.remove('active'));
            option.classList.add('active');
            selectedHour = option.dataset.hour;
        });

        minutes.addEventListener('click', (event) => {
            const option = event.target.closest('[data-minute]');
            if (!option) {
                return;
            }
            minutes.querySelectorAll('div').forEach((item) => item.classList.remove('active'));
            option.classList.add('active');
            selectedMinute = option.dataset.minute;
        });

        set?.addEventListener('click', () => {
            if (!activeInput) {
                return;
            }
            if (selectedHour === null || selectedMinute === null) {
                window.alert(labels.selectTime);
                return;
            }
            activeInput.value = `${selectedHour}:${selectedMinute}`;
            hidePicker();
        });
    };

    const request = (method, url, data = null) => window.fetch(url, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: data ? JSON.stringify(data) : null,
    }).then((response) => response.json().then((payload) => ({
        ok: response.ok,
        payload,
    })));

    const initWhatsAppSend = () => {
        document.querySelectorAll('.sendWA').forEach((button) => {
            button.addEventListener('click', () => {
                const route = button.dataset.route;
                const phone = button.dataset.phone;
                const spk = button.dataset.spk;
                const originalText = button.textContent;

                if (!route || !phone) {
                    window.alert(labels.missingPhone);
                    return;
                }

                button.disabled = true;
                button.textContent = labels.sending;

                request('POST', route, { phone, spk })
                    .then(({ ok, payload }) => {
                        if (ok && payload.success) {
                            button.textContent = labels.sent;
                            window.alert(payload.message || labels.sent);
                            return;
                        }
                        button.disabled = false;
                        button.textContent = originalText;
                        window.alert(payload.message || labels.sendFailed);
                    })
                    .catch(() => {
                        button.disabled = false;
                        button.textContent = originalText;
                        window.alert(labels.sendFailed);
                    });
            });
        });
    };

    const initWhatsAppStatus = () => {
        if (!page) {
            return;
        }

        const statusRoute = page.dataset.waStatusRoute;
        const qrRoute = page.dataset.waQrRoute;
        const disconnectRoute = page.dataset.waDisconnectRoute;
        const status = document.getElementById('wa-status');
        const statusBox = document.getElementById('wa-status-box');
        const qrBox = document.getElementById('wa-qrcode');
        const connect = document.getElementById('btnConnectWA');
        const disconnect = document.getElementById('btnDisconnectWA');
        const refresh = document.getElementById('btnRefreshWA');

        if (!statusRoute || !qrRoute || !disconnectRoute || !status || !statusBox) {
            return;
        }

        const setStatus = (state, text) => {
            status.innerHTML = `<span class="transport-spk-detail-status transport-spk-detail-status--${state}">${text}</span>`;
        };

        const openQrModal = () => {
            if (window.jQuery) {
                window.jQuery('#waModal').modal('show');
            }
        };

        const closeQrModal = () => {
            if (window.jQuery) {
                window.jQuery('#waModal').modal('hide');
            }
        };

        const loadQr = () => {
            qrBox.textContent = labels.loadingQr;
            openQrModal();
            request('GET', qrRoute)
                .then(({ payload }) => {
                    if (payload.qr) {
                        qrBox.innerHTML = `<img src="${payload.qr}" alt="WhatsApp QR Code">`;
                    } else {
                        qrBox.textContent = labels.waitingQr;
                    }
                })
                .catch(() => {
                    qrBox.textContent = labels.requestFailed;
                });
        };

        const loadStatus = () => {
            setStatus('checking', labels.checking);
            request('GET', statusRoute)
                .then(({ ok, payload }) => {
                    if (!ok) {
                        throw new Error(payload.message || labels.requestFailed);
                    }

                    if (payload.ready) {
                        setStatus('connected', labels.connected);
                        statusBox.innerHTML = `<div class="transport-spk-detail-alert transport-spk-detail-alert--success">WhatsApp connected</div>`;
                        connect.hidden = true;
                        disconnect.hidden = false;
                        closeQrModal();
                    } else {
                        setStatus('not-connected', labels.notConnected);
                        statusBox.innerHTML = `<div class="transport-spk-detail-alert transport-spk-detail-alert--danger">${payload.state || labels.notConnected}</div>`;
                        connect.hidden = false;
                        disconnect.hidden = true;
                        loadQr();
                    }
                })
                .catch((error) => {
                    setStatus('error', 'Error');
                    statusBox.innerHTML = `<div class="transport-spk-detail-alert transport-spk-detail-alert--danger">${error.message || labels.requestFailed}</div>`;
                });
        };

        connect?.addEventListener('click', loadQr);
        disconnect?.addEventListener('click', () => {
            request('POST', disconnectRoute, {})
                .then(loadStatus)
                .catch(loadStatus);
        });
        refresh?.addEventListener('click', loadStatus);
        loadStatus();
    };

    confirmForms();
    submitLoading();
    initTimePicker();
    initWhatsAppSend();
    initWhatsAppStatus();
});

const escapeHtml = (value = '') => String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const getSafeHttpUrl = (value = '') => {
    try {
        const url = new URL(String(value), window.location.origin);

        return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
    } catch (error) {
        return '';
    }
};

const extractCoordinatesFromText = (value = '') => {
    const decoded = decodeURIComponent(String(value));
    const patterns = [
        /@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
        /!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/,
        /[?&](?:q|ll)=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
        /(-?\d+\.\d+),\s*(-?\d+\.\d+)/,
    ];

    for (const pattern of patterns) {
        const match = decoded.match(pattern);

        if (match) {
            return {
                lat: Number(match[1]),
                lng: Number(match[2]),
            };
        }
    }

    return null;
};

const resolveDestinationCoordinates = (destination) => {
    const latitude = Number(destination.lat);
    const longitude = Number(destination.lng);

    if (Number.isFinite(latitude) && Number.isFinite(longitude)) {
        return { lat: latitude, lng: longitude };
    }

    const parsed = extractCoordinatesFromText(destination.address || '');

    if (parsed && Number.isFinite(parsed.lat) && Number.isFinite(parsed.lng)) {
        return parsed;
    }

    return null;
};

const showMapNotice = (mapElement, message) => {
    mapElement.querySelector('.transport-spk-detail-map-notice')?.remove();

    const notice = document.createElement('div');
    notice.className = 'transport-spk-detail-map-notice';
    notice.textContent = message;
    mapElement.appendChild(notice);
};

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const projectLatLng = (latitude, longitude, zoom) => {
    const tileSize = 256;
    const scale = tileSize * (2 ** zoom);
    const safeLatitude = clamp(latitude, -85.05112878, 85.05112878);
    const sinLatitude = Math.sin((safeLatitude * Math.PI) / 180);

    return {
        x: ((longitude + 180) / 360) * scale,
        y: (0.5 - (Math.log((1 + sinLatitude) / (1 - sinLatitude)) / (4 * Math.PI))) * scale,
    };
};

const unprojectPixel = (x, y, zoom) => {
    const tileSize = 256;
    const scale = tileSize * (2 ** zoom);
    const longitude = (x / scale) * 360 - 180;
    const n = Math.PI - ((2 * Math.PI * y) / scale);
    const latitude = (180 / Math.PI) * Math.atan(0.5 * (Math.exp(n) - Math.exp(-n)));

    return { lat: latitude, lng: longitude };
};

const chooseMapZoom = (points, width, height) => {
    if (points.length <= 1) {
        return 15;
    }

    for (let zoom = 17; zoom >= 9; zoom -= 1) {
        const projected = points.map(([latitude, longitude]) => projectLatLng(latitude, longitude, zoom));
        const xs = projected.map((point) => point.x);
        const ys = projected.map((point) => point.y);
        const projectedWidth = Math.max(...xs) - Math.min(...xs);
        const projectedHeight = Math.max(...ys) - Math.min(...ys);

        if (projectedWidth <= width - 88 && projectedHeight <= height - 88) {
            return zoom;
        }
    }

    return 9;
};

const createMapLayer = (className) => {
    const layer = document.createElement('div');
    layer.className = className;
    return layer;
};

const getTransportSpkMapDestinations = (page) => {
    const mapData = document.getElementById('transportSpkMapData');

    if (mapData?.textContent?.trim()) {
        try {
            return JSON.parse(mapData.textContent);
        } catch (error) {
            return [];
        }
    }

    try {
        return JSON.parse(page.dataset.destinations || '[]');
    } catch (error) {
        return [];
    }
};

const initTransportSpkOpenMap = () => {
    const mapElement = document.getElementById('transportSpkMap');
    const page = document.querySelector('[data-transport-spk-detail]');

    if (!mapElement || !page) {
        return;
    }

    if (mapElement.dataset.mapInitialized === 'true') {
        return;
    }

    mapElement.dataset.mapInitialized = 'true';

    const destinations = getTransportSpkMapDestinations(page);

    if (!destinations.length) {
        mapElement.dataset.empty = 'true';
        showMapNotice(mapElement, page.dataset.labelNoCoordinate || 'No valid destination coordinates found');
        return;
    }

    mapElement.querySelector('[data-map-fallback]')?.remove();

    const markerPoints = [];
    const markerDestinations = [];
    const openMapLabel = page.dataset.labelOpenMap || 'Open map';
    const routeUnavailableLabel = page.dataset.labelRouteUnavailable || 'Route unavailable';
    const noCoordinateLabel = page.dataset.labelNoCoordinate || 'No valid destination coordinates found';

    destinations.forEach((destination, index) => {
        const order = destination.order || index + 1;
        const coordinates = resolveDestinationCoordinates(destination);

        if (!coordinates) {
            return;
        }

        const point = [coordinates.lat, coordinates.lng];
        markerPoints.push(point);
        markerDestinations.push({ ...destination, order, coordinates });
    });

    const bounds = mapElement.getBoundingClientRect();
    const width = Math.max(Math.round(bounds.width || mapElement.clientWidth || 360), 320);
    const height = Math.max(Math.round(bounds.height || mapElement.clientHeight || 360), 320);
    const mapPoints = markerPoints.length ? markerPoints : [[-8.4095, 115.1889]];
    let zoom = chooseMapZoom(mapPoints, width, height);
    const center = mapPoints.reduce((carry, point) => ({
        lat: carry.lat + point[0],
        lng: carry.lng + point[1],
    }), { lat: 0, lng: 0 });
    const centerPoint = [
        center.lat / mapPoints.length,
        center.lng / mapPoints.length,
    ];
    let centerPixels = projectLatLng(centerPoint[0], centerPoint[1], zoom);
    const tilePane = createMapLayer('transport-spk-detail-map-tiles');
    const routePane = createMapLayer('transport-spk-detail-map-route');
    const markerPane = createMapLayer('transport-spk-detail-map-markers');
    const controls = createMapLayer('transport-spk-detail-map-controls');
    const attribution = createMapLayer('transport-spk-detail-map-attribution');
    attribution.innerHTML = '&copy; OpenStreetMap contributors';

    const tileSize = 256;
    let routeDisplayPoints = markerPoints;

    const getViewportPoint = ([latitude, longitude]) => {
        const projected = projectLatLng(latitude, longitude, zoom);

        return {
            x: projected.x - centerPixels.x + (width / 2),
            y: projected.y - centerPixels.y + (height / 2),
        };
    };

    const drawRoute = (points, strong = false) => {
        if (points.length < 2) {
            return;
        }

        const projectedPoints = points.map(getViewportPoint);
        const polylinePoints = projectedPoints
            .map((point) => `${point.x.toFixed(1)},${point.y.toFixed(1)}`)
            .join(' ');

        routePane.innerHTML = `
            <svg viewBox="0 0 ${width} ${height}" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="${polylinePoints}" class="${strong ? 'is-routed' : ''}" />
            </svg>
        `;
    };

    const renderTiles = () => {
        tilePane.innerHTML = '';

        const tileCount = 2 ** zoom;
        const minTileX = Math.floor((centerPixels.x - (width / 2)) / tileSize);
        const maxTileX = Math.floor((centerPixels.x + (width / 2)) / tileSize);
        const minTileY = Math.floor((centerPixels.y - (height / 2)) / tileSize);
        const maxTileY = Math.floor((centerPixels.y + (height / 2)) / tileSize);

        for (let tileX = minTileX; tileX <= maxTileX; tileX += 1) {
            for (let tileY = minTileY; tileY <= maxTileY; tileY += 1) {
                if (tileY < 0 || tileY >= tileCount) {
                    continue;
                }

                const wrappedTileX = ((tileX % tileCount) + tileCount) % tileCount;
                const image = document.createElement('img');
                image.alt = '';
                image.decoding = 'async';
                image.loading = 'lazy';
                image.src = `https://tile.openstreetmap.org/${zoom}/${wrappedTileX}/${tileY}.png`;
                image.style.left = `${(tileX * tileSize) - centerPixels.x + (width / 2)}px`;
                image.style.top = `${(tileY * tileSize) - centerPixels.y + (height / 2)}px`;
                tilePane.appendChild(image);
            }
        }
    };

    const renderMarkers = () => {
        markerPane.innerHTML = '';

        markerDestinations.forEach((destination) => {
            const point = getViewportPoint([destination.coordinates.lat, destination.coordinates.lng]);
            const marker = document.createElement('button');
            marker.type = 'button';
            marker.className = `transport-spk-detail-map-marker ${destination.status === 'Visited' ? 'is-visited' : ''}`;
            marker.style.left = `${point.x}px`;
            marker.style.top = `${point.y}px`;
            marker.textContent = destination.order;
            marker.title = `${destination.name || '-'} (${destination.status || 'Pending'})`;

            const safeAddress = getSafeHttpUrl(destination.address);
            marker.addEventListener('click', () => {
                markerPane.querySelector('.transport-spk-detail-map-popup')?.remove();

                const popup = document.createElement('div');
                popup.className = 'transport-spk-detail-map-popup';
                popup.style.left = `${point.x}px`;
                popup.style.top = `${point.y}px`;
                popup.innerHTML = `
                    <strong>${escapeHtml(destination.name || '-')}</strong>
                    ${destination.time ? `<small>${escapeHtml(destination.time)}</small>` : ''}
                    <span>${escapeHtml(destination.status || 'Pending')}</span>
                    ${safeAddress ? `<a href="${escapeHtml(safeAddress)}" target="_blank" rel="noopener">${escapeHtml(openMapLabel)}</a>` : ''}
                `;
                markerPane.appendChild(popup);
            });

            markerPane.appendChild(marker);
        });
    };

    const renderMap = () => {
        renderTiles();
        routePane.innerHTML = '';
        drawRoute(routeDisplayPoints, routeDisplayPoints !== markerPoints);
        renderMarkers();
    };

    const zoomTo = (nextZoom) => {
        const clampedZoom = clamp(nextZoom, 9, 18);

        if (clampedZoom === zoom) {
            return;
        }

        const centerLatLng = unprojectPixel(centerPixels.x, centerPixels.y, zoom);
        zoom = clampedZoom;
        centerPixels = projectLatLng(centerLatLng.lat, centerLatLng.lng, zoom);
        renderMap();
    };

    controls.innerHTML = `
        <button type="button" data-map-zoom-in aria-label="Zoom in">+</button>
        <button type="button" data-map-zoom-out aria-label="Zoom out">-</button>
    `;
    controls.querySelector('[data-map-zoom-in]')?.addEventListener('click', () => zoomTo(zoom + 1));
    controls.querySelector('[data-map-zoom-out]')?.addEventListener('click', () => zoomTo(zoom - 1));

    let isDragging = false;
    let lastPointer = null;

    const endDrag = () => {
        isDragging = false;
        lastPointer = null;
        mapElement.classList.remove('is-dragging');
    };

    mapElement.addEventListener('pointerdown', (event) => {
        if (event.target.closest('button, a')) {
            return;
        }

        isDragging = true;
        lastPointer = { x: event.clientX, y: event.clientY };
        mapElement.classList.add('is-dragging');
        mapElement.setPointerCapture?.(event.pointerId);
    });

    mapElement.addEventListener('pointermove', (event) => {
        if (!isDragging || !lastPointer) {
            return;
        }

        centerPixels = {
            x: centerPixels.x - (event.clientX - lastPointer.x),
            y: centerPixels.y - (event.clientY - lastPointer.y),
        };
        lastPointer = { x: event.clientX, y: event.clientY };
        renderMap();
    });

    mapElement.addEventListener('pointerup', endDrag);
    mapElement.addEventListener('pointercancel', endDrag);
    mapElement.addEventListener('mouseleave', endDrag);

    mapElement.append(tilePane, routePane, markerPane, controls, attribution);
    renderMap();

    if (!markerPoints.length) {
        mapElement.dataset.empty = 'true';
        showMapNotice(mapElement, noCoordinateLabel);
        return;
    }

    if (markerPoints.length === 1) {
        return;
    }

    const osrmCoordinates = markerPoints
        .map(([latitude, longitude]) => `${longitude},${latitude}`)
        .join(';');

    fetch(`https://router.project-osrm.org/route/v1/driving/${osrmCoordinates}?overview=full&geometries=geojson`)
        .then((response) => {
            if (!response.ok) {
                throw new Error(routeUnavailableLabel);
            }

            return response.json();
        })
        .then((payload) => {
            const geometry = payload?.routes?.[0]?.geometry;

            if (!geometry) {
                return;
            }

            routeDisplayPoints = geometry.coordinates.map(([longitude, latitude]) => [latitude, longitude]);
            renderMap();
        })
        .catch(() => {});
};

document.addEventListener('DOMContentLoaded', () => {
    initTransportSpkOpenMap();
});

window.initTransportSpkOpenMap = initTransportSpkOpenMap;
initTransportSpkOpenMap();
