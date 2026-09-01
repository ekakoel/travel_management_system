/******/ (() => { // webpackBootstrap
/*!************************************************************************!*\
  !*** ./resources/backend/js/operations/transport-management/detail.js ***!
  \************************************************************************/
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
document.addEventListener('DOMContentLoaded', function () {
  var _document$querySelect;
  var page = document.querySelector('[data-transport-spk-detail]');
  var csrf = ((_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.getAttribute('content')) || '';
  var labels = page ? {
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
    waitingQr: page.dataset.labelWaitingQr || 'Waiting for QR...'
  } : {};
  var confirmForms = function confirmForms() {
    document.querySelectorAll('[data-confirm-delete]').forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!window.confirm(form.dataset.confirmDelete)) {
          event.preventDefault();
        }
      });
    });
  };
  var initTimePicker = function initTimePicker() {
    var picker = document.getElementById('transportSpkTimePicker');
    if (!picker) {
      return;
    }
    var hours = picker.querySelector('[data-time-hours]');
    var minutes = picker.querySelector('[data-time-minutes]');
    var close = picker.querySelector('[data-time-close]');
    var set = picker.querySelector('[data-time-set]');
    var activeInput = null;
    var selectedHour = null;
    var selectedMinute = null;
    for (var h = 0; h < 24; h += 1) {
      var value = h.toString().padStart(2, '0');
      var option = document.createElement('div');
      option.dataset.hour = value;
      option.textContent = value;
      hours.appendChild(option);
    }
    for (var m = 0; m < 60; m += 1) {
      var _value = m.toString().padStart(2, '0');
      var _option = document.createElement('div');
      _option.dataset.minute = _value;
      _option.textContent = _value;
      minutes.appendChild(_option);
    }
    var hidePicker = function hidePicker() {
      picker.style.display = 'none';
    };
    var clearActive = function clearActive() {
      hours.querySelectorAll('div').forEach(function (item) {
        return item.classList.remove('active');
      });
      minutes.querySelectorAll('div').forEach(function (item) {
        return item.classList.remove('active');
      });
    };
    document.addEventListener('click', function (event) {
      var input = event.target.closest('.time-input');
      if (input) {
        event.stopPropagation();
        activeInput = input;
        var rect = input.getBoundingClientRect();
        picker.style.top = "".concat(rect.bottom + window.scrollY + 8, "px");
        picker.style.left = "".concat(rect.left + window.scrollX, "px");
        picker.style.display = 'block';
        clearActive();
        var currentValue = input.value;
        if (/^\d{2}:\d{2}$/.test(currentValue)) {
          var _hours$querySelector, _minutes$querySelecto;
          var _currentValue$split = currentValue.split(':'),
            _currentValue$split2 = _slicedToArray(_currentValue$split, 2),
            hour = _currentValue$split2[0],
            minute = _currentValue$split2[1];
          (_hours$querySelector = hours.querySelector("[data-hour=\"".concat(hour, "\"]"))) === null || _hours$querySelector === void 0 ? void 0 : _hours$querySelector.classList.add('active');
          (_minutes$querySelecto = minutes.querySelector("[data-minute=\"".concat(minute, "\"]"))) === null || _minutes$querySelecto === void 0 ? void 0 : _minutes$querySelecto.classList.add('active');
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
    close === null || close === void 0 ? void 0 : close.addEventListener('click', hidePicker);
    hours.addEventListener('click', function (event) {
      var option = event.target.closest('[data-hour]');
      if (!option) {
        return;
      }
      hours.querySelectorAll('div').forEach(function (item) {
        return item.classList.remove('active');
      });
      option.classList.add('active');
      selectedHour = option.dataset.hour;
    });
    minutes.addEventListener('click', function (event) {
      var option = event.target.closest('[data-minute]');
      if (!option) {
        return;
      }
      minutes.querySelectorAll('div').forEach(function (item) {
        return item.classList.remove('active');
      });
      option.classList.add('active');
      selectedMinute = option.dataset.minute;
    });
    set === null || set === void 0 ? void 0 : set.addEventListener('click', function () {
      if (!activeInput) {
        return;
      }
      if (selectedHour === null || selectedMinute === null) {
        window.alert(labels.selectTime);
        return;
      }
      activeInput.value = "".concat(selectedHour, ":").concat(selectedMinute);
      hidePicker();
    });
  };
  var request = function request(method, url) {
    var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    return window.fetch(url, {
      method: method,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf
      },
      body: data ? JSON.stringify(data) : null
    }).then(function (response) {
      return response.json().then(function (payload) {
        return {
          ok: response.ok,
          payload: payload
        };
      });
    });
  };
  var initWhatsAppSend = function initWhatsAppSend() {
    document.querySelectorAll('.sendWA').forEach(function (button) {
      button.addEventListener('click', function () {
        var route = button.dataset.route;
        var phone = button.dataset.phone;
        var spk = button.dataset.spk;
        var originalText = button.textContent;
        if (!route || !phone) {
          window.alert(labels.missingPhone);
          return;
        }
        button.disabled = true;
        button.textContent = labels.sending;
        request('POST', route, {
          phone: phone,
          spk: spk
        }).then(function (_ref) {
          var ok = _ref.ok,
            payload = _ref.payload;
          if (ok && payload.success) {
            button.textContent = labels.sent;
            window.alert(payload.message || labels.sent);
            return;
          }
          button.disabled = false;
          button.textContent = originalText;
          window.alert(payload.message || labels.sendFailed);
        })["catch"](function () {
          button.disabled = false;
          button.textContent = originalText;
          window.alert(labels.sendFailed);
        });
      });
    });
  };
  var initWhatsAppStatus = function initWhatsAppStatus() {
    if (!page) {
      return;
    }
    var statusRoute = page.dataset.waStatusRoute;
    var qrRoute = page.dataset.waQrRoute;
    var disconnectRoute = page.dataset.waDisconnectRoute;
    var status = document.getElementById('wa-status');
    var statusBox = document.getElementById('wa-status-box');
    var qrBox = document.getElementById('wa-qrcode');
    var connect = document.getElementById('btnConnectWA');
    var disconnect = document.getElementById('btnDisconnectWA');
    var refresh = document.getElementById('btnRefreshWA');
    if (!statusRoute || !qrRoute || !disconnectRoute || !status || !statusBox) {
      return;
    }
    var setStatus = function setStatus(state, text) {
      status.innerHTML = "<span class=\"transport-spk-detail-status transport-spk-detail-status--".concat(state, "\">").concat(text, "</span>");
    };
    var openQrModal = function openQrModal() {
      if (window.jQuery) {
        window.jQuery('#waModal').modal('show');
      }
    };
    var closeQrModal = function closeQrModal() {
      if (window.jQuery) {
        window.jQuery('#waModal').modal('hide');
      }
    };
    var loadQr = function loadQr() {
      qrBox.textContent = labels.loadingQr;
      openQrModal();
      request('GET', qrRoute).then(function (_ref2) {
        var payload = _ref2.payload;
        if (payload.qr) {
          qrBox.innerHTML = "<img src=\"".concat(payload.qr, "\" alt=\"WhatsApp QR Code\">");
        } else {
          qrBox.textContent = labels.waitingQr;
        }
      })["catch"](function () {
        qrBox.textContent = labels.requestFailed;
      });
    };
    var loadStatus = function loadStatus() {
      setStatus('checking', labels.checking);
      request('GET', statusRoute).then(function (_ref3) {
        var ok = _ref3.ok,
          payload = _ref3.payload;
        if (!ok) {
          throw new Error(payload.message || labels.requestFailed);
        }
        if (payload.ready) {
          setStatus('connected', labels.connected);
          statusBox.innerHTML = "<div class=\"transport-spk-detail-alert transport-spk-detail-alert--success\">WhatsApp connected</div>";
          connect.hidden = true;
          disconnect.hidden = false;
          closeQrModal();
        } else {
          setStatus('not-connected', labels.notConnected);
          statusBox.innerHTML = "<div class=\"transport-spk-detail-alert transport-spk-detail-alert--danger\">".concat(payload.state || labels.notConnected, "</div>");
          connect.hidden = false;
          disconnect.hidden = true;
          loadQr();
        }
      })["catch"](function (error) {
        setStatus('error', 'Error');
        statusBox.innerHTML = "<div class=\"transport-spk-detail-alert transport-spk-detail-alert--danger\">".concat(error.message || labels.requestFailed, "</div>");
      });
    };
    connect === null || connect === void 0 ? void 0 : connect.addEventListener('click', loadQr);
    disconnect === null || disconnect === void 0 ? void 0 : disconnect.addEventListener('click', function () {
      request('POST', disconnectRoute, {}).then(loadStatus)["catch"](loadStatus);
    });
    refresh === null || refresh === void 0 ? void 0 : refresh.addEventListener('click', loadStatus);
    loadStatus();
  };
  confirmForms();
  initTimePicker();
  initWhatsAppSend();
  initWhatsAppStatus();
});
var escapeHtml = function escapeHtml() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
};
var getSafeHttpUrl = function getSafeHttpUrl() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  try {
    var url = new URL(String(value), window.location.origin);
    return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
  } catch (error) {
    return '';
  }
};
var extractCoordinatesFromText = function extractCoordinatesFromText() {
  var value = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var decoded = decodeURIComponent(String(value));
  var patterns = [/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/, /!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/, /[?&](?:q|ll)=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/, /(-?\d+\.\d+),\s*(-?\d+\.\d+)/];
  for (var _i2 = 0, _patterns = patterns; _i2 < _patterns.length; _i2++) {
    var pattern = _patterns[_i2];
    var match = decoded.match(pattern);
    if (match) {
      return {
        lat: Number(match[1]),
        lng: Number(match[2])
      };
    }
  }
  return null;
};
var resolveDestinationCoordinates = function resolveDestinationCoordinates(destination) {
  var latitude = Number(destination.lat);
  var longitude = Number(destination.lng);
  if (Number.isFinite(latitude) && Number.isFinite(longitude)) {
    return {
      lat: latitude,
      lng: longitude
    };
  }
  var parsed = extractCoordinatesFromText(destination.address || '');
  if (parsed && Number.isFinite(parsed.lat) && Number.isFinite(parsed.lng)) {
    return parsed;
  }
  return null;
};
var showMapNotice = function showMapNotice(mapElement, message) {
  var _mapElement$querySele;
  (_mapElement$querySele = mapElement.querySelector('.transport-spk-detail-map-notice')) === null || _mapElement$querySele === void 0 ? void 0 : _mapElement$querySele.remove();
  var notice = document.createElement('div');
  notice.className = 'transport-spk-detail-map-notice';
  notice.textContent = message;
  mapElement.appendChild(notice);
};
var clamp = function clamp(value, min, max) {
  return Math.min(Math.max(value, min), max);
};
var projectLatLng = function projectLatLng(latitude, longitude, zoom) {
  var tileSize = 256;
  var scale = tileSize * Math.pow(2, zoom);
  var safeLatitude = clamp(latitude, -85.05112878, 85.05112878);
  var sinLatitude = Math.sin(safeLatitude * Math.PI / 180);
  return {
    x: (longitude + 180) / 360 * scale,
    y: (0.5 - Math.log((1 + sinLatitude) / (1 - sinLatitude)) / (4 * Math.PI)) * scale
  };
};
var unprojectPixel = function unprojectPixel(x, y, zoom) {
  var tileSize = 256;
  var scale = tileSize * Math.pow(2, zoom);
  var longitude = x / scale * 360 - 180;
  var n = Math.PI - 2 * Math.PI * y / scale;
  var latitude = 180 / Math.PI * Math.atan(0.5 * (Math.exp(n) - Math.exp(-n)));
  return {
    lat: latitude,
    lng: longitude
  };
};
var chooseMapZoom = function chooseMapZoom(points, width, height) {
  if (points.length <= 1) {
    return 15;
  }
  var _loop = function _loop(zoom) {
    var projected = points.map(function (_ref4) {
      var _ref5 = _slicedToArray(_ref4, 2),
        latitude = _ref5[0],
        longitude = _ref5[1];
      return projectLatLng(latitude, longitude, zoom);
    });
    var xs = projected.map(function (point) {
      return point.x;
    });
    var ys = projected.map(function (point) {
      return point.y;
    });
    var projectedWidth = Math.max.apply(Math, _toConsumableArray(xs)) - Math.min.apply(Math, _toConsumableArray(xs));
    var projectedHeight = Math.max.apply(Math, _toConsumableArray(ys)) - Math.min.apply(Math, _toConsumableArray(ys));
    if (projectedWidth <= width - 88 && projectedHeight <= height - 88) {
      return {
        v: zoom
      };
    }
  };
  for (var zoom = 17; zoom >= 9; zoom -= 1) {
    var _ret = _loop(zoom);
    if (_typeof(_ret) === "object") return _ret.v;
  }
  return 9;
};
var createMapLayer = function createMapLayer(className) {
  var layer = document.createElement('div');
  layer.className = className;
  return layer;
};
var getTransportSpkMapDestinations = function getTransportSpkMapDestinations(page) {
  var _mapData$textContent;
  var mapData = document.getElementById('transportSpkMapData');
  if (mapData !== null && mapData !== void 0 && (_mapData$textContent = mapData.textContent) !== null && _mapData$textContent !== void 0 && _mapData$textContent.trim()) {
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
var initTransportSpkOpenMap = function initTransportSpkOpenMap() {
  var _mapElement$querySele2, _controls$querySelect, _controls$querySelect2;
  var mapElement = document.getElementById('transportSpkMap');
  var page = document.querySelector('[data-transport-spk-detail]');
  if (!mapElement || !page) {
    return;
  }
  if (mapElement.dataset.mapInitialized === 'true') {
    return;
  }
  mapElement.dataset.mapInitialized = 'true';
  var destinations = getTransportSpkMapDestinations(page);
  if (!destinations.length) {
    mapElement.dataset.empty = 'true';
    showMapNotice(mapElement, page.dataset.labelNoCoordinate || 'No valid destination coordinates found');
    return;
  }
  (_mapElement$querySele2 = mapElement.querySelector('[data-map-fallback]')) === null || _mapElement$querySele2 === void 0 ? void 0 : _mapElement$querySele2.remove();
  var markerPoints = [];
  var markerDestinations = [];
  var openMapLabel = page.dataset.labelOpenMap || 'Open map';
  var routeUnavailableLabel = page.dataset.labelRouteUnavailable || 'Route unavailable';
  var noCoordinateLabel = page.dataset.labelNoCoordinate || 'No valid destination coordinates found';
  destinations.forEach(function (destination, index) {
    var order = destination.order || index + 1;
    var coordinates = resolveDestinationCoordinates(destination);
    if (!coordinates) {
      return;
    }
    var point = [coordinates.lat, coordinates.lng];
    markerPoints.push(point);
    markerDestinations.push(_objectSpread(_objectSpread({}, destination), {}, {
      order: order,
      coordinates: coordinates
    }));
  });
  var bounds = mapElement.getBoundingClientRect();
  var width = Math.max(Math.round(bounds.width || mapElement.clientWidth || 360), 320);
  var height = Math.max(Math.round(bounds.height || mapElement.clientHeight || 360), 320);
  var mapPoints = markerPoints.length ? markerPoints : [[-8.4095, 115.1889]];
  var zoom = chooseMapZoom(mapPoints, width, height);
  var center = mapPoints.reduce(function (carry, point) {
    return {
      lat: carry.lat + point[0],
      lng: carry.lng + point[1]
    };
  }, {
    lat: 0,
    lng: 0
  });
  var centerPoint = [center.lat / mapPoints.length, center.lng / mapPoints.length];
  var centerPixels = projectLatLng(centerPoint[0], centerPoint[1], zoom);
  var tilePane = createMapLayer('transport-spk-detail-map-tiles');
  var routePane = createMapLayer('transport-spk-detail-map-route');
  var markerPane = createMapLayer('transport-spk-detail-map-markers');
  var controls = createMapLayer('transport-spk-detail-map-controls');
  var attribution = createMapLayer('transport-spk-detail-map-attribution');
  attribution.innerHTML = '&copy; OpenStreetMap contributors';
  var tileSize = 256;
  var routeDisplayPoints = markerPoints;
  var getViewportPoint = function getViewportPoint(_ref6) {
    var _ref7 = _slicedToArray(_ref6, 2),
      latitude = _ref7[0],
      longitude = _ref7[1];
    var projected = projectLatLng(latitude, longitude, zoom);
    return {
      x: projected.x - centerPixels.x + width / 2,
      y: projected.y - centerPixels.y + height / 2
    };
  };
  var drawRoute = function drawRoute(points) {
    var strong = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    if (points.length < 2) {
      return;
    }
    var projectedPoints = points.map(getViewportPoint);
    var polylinePoints = projectedPoints.map(function (point) {
      return "".concat(point.x.toFixed(1), ",").concat(point.y.toFixed(1));
    }).join(' ');
    routePane.innerHTML = "\n            <svg viewBox=\"0 0 ".concat(width, " ").concat(height, "\" preserveAspectRatio=\"none\" aria-hidden=\"true\">\n                <polyline points=\"").concat(polylinePoints, "\" class=\"").concat(strong ? 'is-routed' : '', "\" />\n            </svg>\n        ");
  };
  var renderTiles = function renderTiles() {
    tilePane.innerHTML = '';
    var tileCount = Math.pow(2, zoom);
    var minTileX = Math.floor((centerPixels.x - width / 2) / tileSize);
    var maxTileX = Math.floor((centerPixels.x + width / 2) / tileSize);
    var minTileY = Math.floor((centerPixels.y - height / 2) / tileSize);
    var maxTileY = Math.floor((centerPixels.y + height / 2) / tileSize);
    for (var tileX = minTileX; tileX <= maxTileX; tileX += 1) {
      for (var tileY = minTileY; tileY <= maxTileY; tileY += 1) {
        if (tileY < 0 || tileY >= tileCount) {
          continue;
        }
        var wrappedTileX = (tileX % tileCount + tileCount) % tileCount;
        var image = document.createElement('img');
        image.alt = '';
        image.decoding = 'async';
        image.loading = 'lazy';
        image.src = "https://tile.openstreetmap.org/".concat(zoom, "/").concat(wrappedTileX, "/").concat(tileY, ".png");
        image.style.left = "".concat(tileX * tileSize - centerPixels.x + width / 2, "px");
        image.style.top = "".concat(tileY * tileSize - centerPixels.y + height / 2, "px");
        tilePane.appendChild(image);
      }
    }
  };
  var renderMarkers = function renderMarkers() {
    markerPane.innerHTML = '';
    markerDestinations.forEach(function (destination) {
      var point = getViewportPoint([destination.coordinates.lat, destination.coordinates.lng]);
      var marker = document.createElement('button');
      marker.type = 'button';
      marker.className = "transport-spk-detail-map-marker ".concat(destination.status === 'Visited' ? 'is-visited' : '');
      marker.style.left = "".concat(point.x, "px");
      marker.style.top = "".concat(point.y, "px");
      marker.textContent = destination.order;
      marker.title = "".concat(destination.name || '-', " (").concat(destination.status || 'Pending', ")");
      var safeAddress = getSafeHttpUrl(destination.address);
      marker.addEventListener('click', function () {
        var _markerPane$querySele;
        (_markerPane$querySele = markerPane.querySelector('.transport-spk-detail-map-popup')) === null || _markerPane$querySele === void 0 ? void 0 : _markerPane$querySele.remove();
        var popup = document.createElement('div');
        popup.className = 'transport-spk-detail-map-popup';
        popup.style.left = "".concat(point.x, "px");
        popup.style.top = "".concat(point.y, "px");
        popup.innerHTML = "\n                    <strong>".concat(escapeHtml(destination.name || '-'), "</strong>\n                    ").concat(destination.time ? "<small>".concat(escapeHtml(destination.time), "</small>") : '', "\n                    <span>").concat(escapeHtml(destination.status || 'Pending'), "</span>\n                    ").concat(safeAddress ? "<a href=\"".concat(escapeHtml(safeAddress), "\" target=\"_blank\" rel=\"noopener\">").concat(escapeHtml(openMapLabel), "</a>") : '', "\n                ");
        markerPane.appendChild(popup);
      });
      markerPane.appendChild(marker);
    });
  };
  var renderMap = function renderMap() {
    renderTiles();
    routePane.innerHTML = '';
    drawRoute(routeDisplayPoints, routeDisplayPoints !== markerPoints);
    renderMarkers();
  };
  var zoomTo = function zoomTo(nextZoom) {
    var clampedZoom = clamp(nextZoom, 9, 18);
    if (clampedZoom === zoom) {
      return;
    }
    var centerLatLng = unprojectPixel(centerPixels.x, centerPixels.y, zoom);
    zoom = clampedZoom;
    centerPixels = projectLatLng(centerLatLng.lat, centerLatLng.lng, zoom);
    renderMap();
  };
  controls.innerHTML = "\n        <button type=\"button\" data-map-zoom-in aria-label=\"Zoom in\">+</button>\n        <button type=\"button\" data-map-zoom-out aria-label=\"Zoom out\">-</button>\n    ";
  (_controls$querySelect = controls.querySelector('[data-map-zoom-in]')) === null || _controls$querySelect === void 0 ? void 0 : _controls$querySelect.addEventListener('click', function () {
    return zoomTo(zoom + 1);
  });
  (_controls$querySelect2 = controls.querySelector('[data-map-zoom-out]')) === null || _controls$querySelect2 === void 0 ? void 0 : _controls$querySelect2.addEventListener('click', function () {
    return zoomTo(zoom - 1);
  });
  var isDragging = false;
  var lastPointer = null;
  var endDrag = function endDrag() {
    isDragging = false;
    lastPointer = null;
    mapElement.classList.remove('is-dragging');
  };
  mapElement.addEventListener('pointerdown', function (event) {
    var _mapElement$setPointe;
    if (event.target.closest('button, a')) {
      return;
    }
    isDragging = true;
    lastPointer = {
      x: event.clientX,
      y: event.clientY
    };
    mapElement.classList.add('is-dragging');
    (_mapElement$setPointe = mapElement.setPointerCapture) === null || _mapElement$setPointe === void 0 ? void 0 : _mapElement$setPointe.call(mapElement, event.pointerId);
  });
  mapElement.addEventListener('pointermove', function (event) {
    if (!isDragging || !lastPointer) {
      return;
    }
    centerPixels = {
      x: centerPixels.x - (event.clientX - lastPointer.x),
      y: centerPixels.y - (event.clientY - lastPointer.y)
    };
    lastPointer = {
      x: event.clientX,
      y: event.clientY
    };
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
  var osrmCoordinates = markerPoints.map(function (_ref8) {
    var _ref9 = _slicedToArray(_ref8, 2),
      latitude = _ref9[0],
      longitude = _ref9[1];
    return "".concat(longitude, ",").concat(latitude);
  }).join(';');
  fetch("https://router.project-osrm.org/route/v1/driving/".concat(osrmCoordinates, "?overview=full&geometries=geojson")).then(function (response) {
    if (!response.ok) {
      throw new Error(routeUnavailableLabel);
    }
    return response.json();
  }).then(function (payload) {
    var _payload$routes, _payload$routes$;
    var geometry = payload === null || payload === void 0 ? void 0 : (_payload$routes = payload.routes) === null || _payload$routes === void 0 ? void 0 : (_payload$routes$ = _payload$routes[0]) === null || _payload$routes$ === void 0 ? void 0 : _payload$routes$.geometry;
    if (!geometry) {
      return;
    }
    routeDisplayPoints = geometry.coordinates.map(function (_ref10) {
      var _ref11 = _slicedToArray(_ref10, 2),
        longitude = _ref11[0],
        latitude = _ref11[1];
      return [latitude, longitude];
    });
    renderMap();
  })["catch"](function () {});
};
document.addEventListener('DOMContentLoaded', function () {
  initTransportSpkOpenMap();
});
window.initTransportSpkOpenMap = initTransportSpkOpenMap;
initTransportSpkOpenMap();
/******/ })()
;