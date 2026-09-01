/******/ (() => { // webpackBootstrap
/*!********************************************************!*\
  !*** ./resources/backend/js/operations/tours/forms.js ***!
  \********************************************************/
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e2) { throw _e2; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e3) { didErr = true; err = _e3; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
document.addEventListener('DOMContentLoaded', function () {
  var escapeHtml = function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function (character) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[character];
    });
  };
  var fieldValue = function fieldValue(form, selector) {
    var _element$value;
    var element = form === null || form === void 0 ? void 0 : form.querySelector(selector);
    if (!element) {
      return '';
    }
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.summernote) {
      var textarea = window.jQuery(element);
      if (textarea.next('.note-editor').length) {
        return String(textarea.summernote('code') || '').trim();
      }
    }
    return ((_element$value = element.value) === null || _element$value === void 0 ? void 0 : _element$value.trim()) || '';
  };
  var plainText = function plainText(value) {
    var wrapper = document.createElement('div');
    wrapper.innerHTML = value || '';
    return (wrapper.textContent || wrapper.innerText || '').replace(/\s+/g, ' ').trim();
  };
  var truncate = function truncate(value) {
    var limit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 110;
    var text = plainText(value);
    if (!text) {
      return 'Not filled';
    }
    return text.length > limit ? "".concat(text.slice(0, limit - 1), "...") : text;
  };
  var initRichText = function initRichText(root) {
    if (window.initBackendRichText) {
      window.initBackendRichText(root);
    }
  };
  var setRichTextValue = function setRichTextValue(element, value) {
    if (window.setBackendRichTextValue) {
      window.setBackendRichTextValue(element, value);
      return;
    }
    if (element) {
      element.value = value || '';
    }
  };
  document.querySelectorAll('[data-tour-cover-input]').forEach(function (input) {
    if (input.dataset.ready === 'true') {
      return;
    }
    input.dataset.ready = 'true';
    input.addEventListener('change', function () {
      var _input$files;
      var preview = document.querySelector(input.dataset.tourCoverPreviewTarget || '[data-tour-cover-preview]');
      var status = document.querySelector('[data-tour-cover-status]');
      var defaultStatus = (status === null || status === void 0 ? void 0 : status.dataset.tourCoverStatusDefault) || 'No cover selected';
      var file = ((_input$files = input.files) === null || _input$files === void 0 ? void 0 : _input$files[0]) || null;
      if (status) {
        status.textContent = file ? file.name : defaultStatus;
      }
      if (preview) {
        preview.innerHTML = '';
        if (file && file.type.startsWith('image/')) {
          var image = document.createElement('img');
          image.src = URL.createObjectURL(file);
          image.alt = file.name;
          image.onload = function () {
            return URL.revokeObjectURL(image.src);
          };
          preview.appendChild(image);
        }
      }
      document.dispatchEvent(new CustomEvent('tour:create-summary-refresh'));
    });
  });
  var refreshLocationSummaries = function refreshLocationSummaries(repeater) {
    var list = repeater.querySelector('[data-tour-location-list]');
    var empty = repeater.querySelector('[data-tour-location-empty]');
    var items = _toConsumableArray((list === null || list === void 0 ? void 0 : list.querySelectorAll('[data-tour-location-item]')) || []);
    empty === null || empty === void 0 ? void 0 : empty.classList.toggle('d-none', items.length > 0);
    items.forEach(function (item, index) {
      var _nameInput$value;
      var number = item.querySelector('[data-tour-location-number]');
      var dayInput = item.querySelector('[data-field-name="day_number"]');
      var orderInput = item.querySelector('[data-field-name="visit_order"]');
      var nameInput = item.querySelector('[data-tour-location-name]');
      var typeInput = item.querySelector('[data-field-name="location_type"]');
      var timeInput = item.querySelector('[data-field-name="visit_time"]');
      var latitudeInput = item.querySelector('[data-tour-location-latitude]');
      var longitudeInput = item.querySelector('[data-tour-location-longitude]');
      if (number) number.textContent = index + 1;
      item.querySelectorAll('[data-field-name]').forEach(function (field) {
        field.name = "locations[".concat(index, "][").concat(field.dataset.fieldName, "]");
      });
      if (dayInput && !dayInput.value) dayInput.value = '1';
      if (orderInput) orderInput.value = index + 1;
      var dayLabel = item.querySelector('[data-tour-location-day-label]');
      var title = item.querySelector('[data-tour-location-title]');
      var typeLabel = item.querySelector('[data-tour-location-type-label]');
      var timeLabel = item.querySelector('[data-tour-location-time-label]');
      var coordinateLabel = item.querySelector('[data-tour-location-coordinate-label]');
      if (dayLabel) dayLabel.textContent = (dayInput === null || dayInput === void 0 ? void 0 : dayInput.value) || '1';
      if (title) title.textContent = (nameInput === null || nameInput === void 0 ? void 0 : (_nameInput$value = nameInput.value) === null || _nameInput$value === void 0 ? void 0 : _nameInput$value.trim()) || 'Untitled stop';
      if (typeLabel) typeLabel.textContent = (typeInput === null || typeInput === void 0 ? void 0 : typeInput.value) || 'Attraction';
      if (timeLabel) timeLabel.textContent = (timeInput === null || timeInput === void 0 ? void 0 : timeInput.value) || 'No time';
      if (coordinateLabel) {
        coordinateLabel.textContent = latitudeInput !== null && latitudeInput !== void 0 && latitudeInput.value && longitudeInput !== null && longitudeInput !== void 0 && longitudeInput.value ? 'Coordinates available' : 'Coordinates missing';
      }
    });
    document.dispatchEvent(new CustomEvent('tour:create-summary-refresh'));
  };
  document.querySelectorAll('[data-tour-locations-repeater]').forEach(function (repeater) {
    var _document$querySelect;
    if (repeater.dataset.ready === 'true') {
      return;
    }
    repeater.dataset.ready = 'true';
    var list = repeater.querySelector('[data-tour-location-list]');
    var template = repeater.querySelector('[data-tour-location-template]');
    var addButton = repeater.querySelector('[data-add-tour-location]');
    var allowEmpty = repeater.dataset.allowEmpty === 'true';
    var resolveUrl = repeater.dataset.resolveUrl;
    var referencesUrl = repeater.dataset.referencesUrl;
    var csrfToken = ((_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.content) || '';
    var resolveTimers = new WeakMap();
    var referenceTimers = new WeakMap();
    var draggedLocationItem = null;
    var setStatus = function setStatus(item, message) {
      var state = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'text-muted';
      var status = item.querySelector('[data-tour-coordinate-status]');
      if (!status) return;
      status.textContent = message;
      status.classList.remove('text-muted', 'text-success', 'text-danger');
      status.classList.add(state);
    };
    var showManualCoordinates = function showManualCoordinates(item) {
      item.querySelectorAll('[data-tour-manual-coordinate-field]').forEach(function (field) {
        field.classList.remove('d-none');
      });
    };
    var closeSuggestions = function closeSuggestions(item) {
      var menu = item.querySelector('[data-tour-location-suggestions]');
      if (menu) {
        menu.innerHTML = '';
        menu.classList.remove('is-open');
      }
    };
    var renderLocationMarkerPreview = function renderLocationMarkerPreview(item) {
      var imageUrl = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      var preview = item.querySelector('[data-tour-location-image-preview]');
      if (!preview) return;
      if (!imageUrl) {
        preview.innerHTML = '<span>No marker cover selected</span>';
        return;
      }
      var image = document.createElement('img');
      image.src = imageUrl;
      image.alt = 'Marker image';
      image.className = 'tour-location-marker-preview';
      preview.replaceChildren(image);
    };
    var fillLocationFromReference = function fillLocationFromReference(item, location) {
      var referenceInput = item.querySelector('[data-tour-location-reference-id]');
      var nameInput = item.querySelector('[data-tour-location-name]');
      var typeInput = item.querySelector('[data-field-name="location_type"], select[name$="[location_type]"]');
      var latitudeInput = item.querySelector('[data-tour-location-latitude]');
      var longitudeInput = item.querySelector('[data-tour-location-longitude]');
      var mapsInput = item.querySelector('[data-tour-location-map-url]');
      var existingImageInput = item.querySelector('[data-field-name="existing_marker_image"], input[name$="[existing_marker_image]"]');
      var descriptionInput = item.querySelector('[data-field-name="description"], textarea[name$="[description]"]');
      var traditionalDescriptionInput = item.querySelector('[data-field-name="description_traditional"], textarea[name$="[description_traditional]"]');
      var simplifiedDescriptionInput = item.querySelector('[data-field-name="description_simplified"], textarea[name$="[description_simplified]"]');
      if (referenceInput) referenceInput.value = location.id || '';
      if (nameInput) nameInput.value = location.destination_name || '';
      if (typeInput) typeInput.value = location.location_type || 'Attraction';
      if (latitudeInput) latitudeInput.value = location.latitude || '';
      if (longitudeInput) longitudeInput.value = location.longitude || '';
      if (mapsInput) mapsInput.value = location.google_maps_url || '';
      if (existingImageInput) existingImageInput.value = location.marker_image || '';
      if (descriptionInput) setRichTextValue(descriptionInput, location.description || '');
      if (traditionalDescriptionInput) setRichTextValue(traditionalDescriptionInput, location.description_traditional || '');
      if (simplifiedDescriptionInput) setRichTextValue(simplifiedDescriptionInput, location.description_simplified || '');
      renderLocationMarkerPreview(item, location.marker_image_url || '');
      setStatus(item, 'Coordinates available from saved reference.', 'text-success');
      closeSuggestions(item);
      refreshLocationSummaries(repeater);
    };
    var renderSuggestions = function renderSuggestions(item, locations) {
      var menu = item.querySelector('[data-tour-location-suggestions]');
      if (!menu) return;
      if (!locations.length) {
        menu.innerHTML = '<div class="tour-location-suggest__empty">No saved location found.</div>';
        menu.classList.add('is-open');
        return;
      }
      menu.innerHTML = locations.map(function (location) {
        var image = location.marker_image_url ? "<img src=\"".concat(escapeHtml(location.marker_image_url), "\" alt=\"\">") : "<span class=\"tour-location-suggest__avatar\">".concat(escapeHtml((location.destination_name || '?').charAt(0)), "</span>");
        return "<button type=\"button\" class=\"tour-location-suggest__item\" data-reference-id=\"".concat(location.id, "\">\n          ").concat(image, "\n          <span><strong>").concat(escapeHtml(location.destination_name), "</strong><small>").concat(escapeHtml(location.location_type), " | ").concat(escapeHtml(location.latitude), ", ").concat(escapeHtml(location.longitude), "</small></span>\n        </button>");
      }).join('');
      menu.querySelectorAll('[data-reference-id]').forEach(function (button, index) {
        button.addEventListener('click', function () {
          return fillLocationFromReference(item, locations[index]);
        });
      });
      menu.classList.add('is-open');
    };
    var searchLocationReferences = function searchLocationReferences(item) {
      if (!referencesUrl) return;
      var nameInput = item.querySelector('[data-tour-location-name]');
      var query = nameInput ? nameInput.value.trim() : '';
      if (query.length < 2) {
        closeSuggestions(item);
        return;
      }
      fetch("".concat(referencesUrl, "?q=").concat(encodeURIComponent(query)), {
        headers: {
          Accept: 'application/json'
        }
      }).then(function (response) {
        return response.json();
      }).then(function (locations) {
        return renderSuggestions(item, Array.isArray(locations) ? locations : []);
      })["catch"](function () {
        return closeSuggestions(item);
      });
    };
    var queueLocationReferenceSearch = function queueLocationReferenceSearch(item) {
      var existingTimer = referenceTimers.get(item);
      if (existingTimer) clearTimeout(existingTimer);
      referenceTimers.set(item, setTimeout(function () {
        return searchLocationReferences(item);
      }, 260));
    };
    var resolveCoordinates = function resolveCoordinates(item) {
      if (!resolveUrl) return;
      var urlInput = item.querySelector('[data-tour-location-map-url]');
      var latitudeInput = item.querySelector('[data-tour-location-latitude]');
      var longitudeInput = item.querySelector('[data-tour-location-longitude]');
      var googleMapsUrl = urlInput ? urlInput.value.trim() : '';
      if (!googleMapsUrl || !latitudeInput || !longitudeInput) {
        setStatus(item, 'Add a Google Maps URL before resolving coordinates.', 'text-danger');
        return;
      }
      setStatus(item, 'Reading coordinates from Google Maps link...');
      fetch(resolveUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          google_maps_url: googleMapsUrl
        })
      }).then(function (response) {
        return response.json().then(function (payload) {
          if (!response.ok) throw payload;
          return payload;
        });
      }).then(function (payload) {
        latitudeInput.value = payload.latitude;
        longitudeInput.value = payload.longitude;
        setStatus(item, "Location found. Latitude ".concat(payload.latitude, ", longitude ").concat(payload.longitude, "."), 'text-success');
        refreshLocationSummaries(repeater);
      })["catch"](function (error) {
        showManualCoordinates(item);
        setStatus(item, error.message || 'Coordinates could not be read. Fill latitude and longitude manually.', 'text-danger');
      });
    };
    var queueResolveCoordinates = function queueResolveCoordinates(item) {
      var existingTimer = resolveTimers.get(item);
      if (existingTimer) clearTimeout(existingTimer);
      resolveTimers.set(item, setTimeout(function () {
        return resolveCoordinates(item);
      }, 600));
    };
    var locationAfterDragTarget = function locationAfterDragTarget(container, y) {
      var draggableItems = _toConsumableArray(container.querySelectorAll('[data-tour-location-item]:not(.is-dragging)'));
      return draggableItems.reduce(function (closest, child) {
        var box = child.getBoundingClientRect();
        var offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
          return {
            offset: offset,
            element: child
          };
        }
        return closest;
      }, {
        offset: Number.NEGATIVE_INFINITY,
        element: null
      }).element;
    };
    addButton === null || addButton === void 0 ? void 0 : addButton.addEventListener('click', function () {
      if (!template || !list) return;
      var fragment = template.content.cloneNode(true);
      var item = fragment.querySelector('[data-tour-location-item]');
      list.appendChild(fragment);
      refreshLocationSummaries(repeater);
      initRichText(item || list);
    });
    repeater.addEventListener('click', function (event) {
      var toggleButton = event.target.closest('[data-toggle-tour-location-editor]');
      if (toggleButton) {
        var _toggleButton$closest;
        (_toggleButton$closest = toggleButton.closest('[data-tour-location-item]')) === null || _toggleButton$closest === void 0 ? void 0 : _toggleButton$closest.classList.toggle('is-collapsed');
        return;
      }
      var removeButton = event.target.closest('[data-remove-tour-location]');
      if (!removeButton || !list) return;
      var item = removeButton.closest('[data-tour-location-item]');
      var count = list.querySelectorAll('[data-tour-location-item]').length;
      if (item && (allowEmpty || count > 1)) {
        item.remove();
        refreshLocationSummaries(repeater);
      }
    });
    repeater.addEventListener('dragstart', function (event) {
      var handle = event.target.closest('[data-tour-location-drag-handle]');
      if (!handle || !list) return;
      draggedLocationItem = handle.closest('[data-tour-location-item]');
      if (!draggedLocationItem) return;
      draggedLocationItem.classList.add('is-dragging');
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', '');
    });
    repeater.addEventListener('dragover', function (event) {
      if (!draggedLocationItem || !list) return;
      event.preventDefault();
      var afterElement = locationAfterDragTarget(list, event.clientY);
      if (!afterElement) {
        list.appendChild(draggedLocationItem);
      } else if (afterElement !== draggedLocationItem) {
        list.insertBefore(draggedLocationItem, afterElement);
      }
    });
    repeater.addEventListener('dragend', function () {
      if (!draggedLocationItem) return;
      draggedLocationItem.classList.remove('is-dragging');
      draggedLocationItem = null;
      refreshLocationSummaries(repeater);
    });
    repeater.addEventListener('input', function (event) {
      var item = event.target.closest('[data-tour-location-item]');
      if (!item) return;
      if (event.target.matches('[data-tour-location-name]')) {
        var referenceInput = item.querySelector('[data-tour-location-reference-id]');
        if (referenceInput) referenceInput.value = '';
        queueLocationReferenceSearch(item);
        refreshLocationSummaries(repeater);
        return;
      }
      if (event.target.matches('[data-tour-location-map-url]')) {
        queueResolveCoordinates(item);
      }
      refreshLocationSummaries(repeater);
    });
    repeater.addEventListener('change', function (event) {
      var item = event.target.closest('[data-tour-location-item]');
      if (!item) return;
      if (event.target.matches('[data-tour-location-map-url]')) {
        resolveCoordinates(item);
      }
      if (event.target.matches('[data-field-name="marker_image"]')) {
        var _event$target$files;
        var file = ((_event$target$files = event.target.files) === null || _event$target$files === void 0 ? void 0 : _event$target$files[0]) || null;
        var existingImageInput = item.querySelector('[data-field-name="existing_marker_image"], input[name$="[existing_marker_image]"]');
        if (existingImageInput && file) {
          existingImageInput.value = '';
        }
        if (file && file.type.startsWith('image/')) {
          var previewUrl = URL.createObjectURL(file);
          renderLocationMarkerPreview(item, previewUrl);
          var previewImage = item.querySelector('[data-tour-location-image-preview] img');
          if (previewImage) {
            previewImage.onload = function () {
              return URL.revokeObjectURL(previewUrl);
            };
          }
        } else if (!file) {
          renderLocationMarkerPreview(item, '');
        }
      }
      refreshLocationSummaries(repeater);
    });
    document.addEventListener('click', function (event) {
      if (repeater.contains(event.target)) return;
      repeater.querySelectorAll('[data-tour-location-item]').forEach(closeSuggestions);
    });
    refreshLocationSummaries(repeater);
  });
  var refreshCreateSummary = function refreshCreateSummary() {
    var _typeSelect$selectedO, _typeSelect$selectedO2, _typeSelect$selectedO3, _coverInput$files, _coverInput$files$;
    var wizard = document.querySelector('[data-tour-create-wizard]');
    var form = wizard === null || wizard === void 0 ? void 0 : wizard.closest('form');
    if (!form) return;
    var routeItems = _toConsumableArray(document.querySelectorAll('[data-tour-location-item]'));
    var days = new Set(routeItems.map(function (item) {
      var _item$querySelector;
      return (_item$querySelector = item.querySelector('[data-field-name="day_number"]')) === null || _item$querySelector === void 0 ? void 0 : _item$querySelector.value;
    }).filter(Boolean));
    var typeSelect = form.querySelector('[name="type"]');
    var typeLabel = (typeSelect === null || typeSelect === void 0 ? void 0 : (_typeSelect$selectedO = typeSelect.selectedOptions) === null || _typeSelect$selectedO === void 0 ? void 0 : (_typeSelect$selectedO2 = _typeSelect$selectedO[0]) === null || _typeSelect$selectedO2 === void 0 ? void 0 : (_typeSelect$selectedO3 = _typeSelect$selectedO2.textContent) === null || _typeSelect$selectedO3 === void 0 ? void 0 : _typeSelect$selectedO3.trim()) || 'Not selected';
    var statusSelect = form.querySelector('[name="status"]');
    var coverInput = form.querySelector('[data-tour-cover-input]');
    var coverDefault = (coverInput === null || coverInput === void 0 ? void 0 : coverInput.dataset.tourCoverExisting) || 'No cover selected';
    var contentFieldGroups = [['Short Description', true, 'short_description', 'short_description_traditional', 'short_description_simplified'], ['Description', true, 'description', 'description_traditional', 'description_simplified'], ['Package Highlights', false, 'package_highlights', 'package_highlights_traditional', 'package_highlights_simplified'], ['Include', false, 'include', 'include_traditional', 'include_simplified'], ['Exclude', false, 'exclude', 'exclude_traditional', 'exclude_simplified'], ['Additional Information', false, 'additional_info', 'additional_info_traditional', 'additional_info_simplified'], ['Cancellation Policy', true, 'cancellation_policy', 'cancellation_policy_traditional', 'cancellation_policy_simplified']];
    var summaryValues = {
      '[data-tour-summary-name]': fieldValue(form, '[name="name"]') || 'Not filled',
      '[data-tour-summary-type]': typeLabel,
      '[data-tour-summary-duration]': "".concat(fieldValue(form, '[name="duration_days"]') || '1', "D / ").concat(fieldValue(form, '[name="duration_nights"]') || '0', "N"),
      '[data-tour-summary-route]': "".concat(days.size || 0, " day(s), ").concat(routeItems.length, " stop(s)"),
      '[data-tour-summary-cover]': (coverInput === null || coverInput === void 0 ? void 0 : (_coverInput$files = coverInput.files) === null || _coverInput$files === void 0 ? void 0 : (_coverInput$files$ = _coverInput$files[0]) === null || _coverInput$files$ === void 0 ? void 0 : _coverInput$files$.name) || coverDefault,
      '[data-tour-review-status]': (statusSelect === null || statusSelect === void 0 ? void 0 : statusSelect.value) || 'Draft',
      '[data-tour-review-code]': fieldValue(form, '[name="code"]') || 'Not filled',
      '[data-tour-review-name-en]': fieldValue(form, '[name="name"]') || 'Not filled',
      '[data-tour-review-name-traditional]': fieldValue(form, '[name="name_traditional"]') || 'Not filled',
      '[data-tour-review-name-simplified]': fieldValue(form, '[name="name_simplified"]') || 'Not filled'
    };
    Object.entries(summaryValues).forEach(function (_ref) {
      var _ref2 = _slicedToArray(_ref, 2),
        selector = _ref2[0],
        value = _ref2[1];
      document.querySelectorAll(selector).forEach(function (element) {
        element.textContent = value;
      });
    });
    document.querySelectorAll('[data-tour-review-route-days]').forEach(function (container) {
      if (!routeItems.length) {
        container.innerHTML = '<p>No route stops added yet.</p>';
        return;
      }
      var grouped = routeItems.reduce(function (carry, item, index) {
        var _item$querySelector2, _item$querySelector3, _item$querySelector4, _item$querySelector5, _item$querySelector5$, _item$querySelector6, _item$querySelector7, _item$querySelector8;
        var day = ((_item$querySelector2 = item.querySelector('[data-field-name="day_number"]')) === null || _item$querySelector2 === void 0 ? void 0 : _item$querySelector2.value) || '1';
        var order = ((_item$querySelector3 = item.querySelector('[data-field-name="visit_order"]')) === null || _item$querySelector3 === void 0 ? void 0 : _item$querySelector3.value) || String(index + 1);
        var time = ((_item$querySelector4 = item.querySelector('[data-field-name="visit_time"]')) === null || _item$querySelector4 === void 0 ? void 0 : _item$querySelector4.value) || '';
        var name = ((_item$querySelector5 = item.querySelector('[data-tour-location-name]')) === null || _item$querySelector5 === void 0 ? void 0 : (_item$querySelector5$ = _item$querySelector5.value) === null || _item$querySelector5$ === void 0 ? void 0 : _item$querySelector5$.trim()) || 'Untitled stop';
        var type = ((_item$querySelector6 = item.querySelector('[data-field-name="location_type"]')) === null || _item$querySelector6 === void 0 ? void 0 : _item$querySelector6.value) || 'Attraction';
        var latitude = ((_item$querySelector7 = item.querySelector('[data-tour-location-latitude]')) === null || _item$querySelector7 === void 0 ? void 0 : _item$querySelector7.value) || '';
        var longitude = ((_item$querySelector8 = item.querySelector('[data-tour-location-longitude]')) === null || _item$querySelector8 === void 0 ? void 0 : _item$querySelector8.value) || '';
        carry[day] = carry[day] || [];
        carry[day].push({
          order: Number(order) || index + 1,
          time: time,
          name: name,
          type: type,
          hasCoordinates: Boolean(latitude && longitude)
        });
        return carry;
      }, {});
      container.innerHTML = Object.keys(grouped).sort(function (first, second) {
        return Number(first) - Number(second);
      }).map(function (day) {
        var stops = grouped[day].sort(function (first, second) {
          return first.order - second.order;
        }).map(function (stop) {
          return "<li>\n              <strong>".concat(escapeHtml(stop.time ? "".concat(stop.time, " | ").concat(stop.name) : stop.name), "</strong>\n              <small>").concat(escapeHtml(stop.type), " | ").concat(stop.hasCoordinates ? 'Coordinates available' : 'Coordinates missing', "</small>\n            </li>");
        }).join('');
        return "<section><h4>Day ".concat(escapeHtml(day), " <span>").concat(grouped[day].length, " stop(s)</span></h4><ol>").concat(stops, "</ol></section>");
      }).join('');
    });
    var contentRows = contentFieldGroups.flatMap(function (_ref3) {
      var _ref4 = _slicedToArray(_ref3, 5),
        group = _ref4[0],
        required = _ref4[1],
        english = _ref4[2],
        traditional = _ref4[3],
        simplified = _ref4[4];
      return [{
        group: group,
        required: required,
        locale: 'English',
        value: fieldValue(form, "[name=\"".concat(english, "\"]"))
      }, {
        group: group,
        required: required,
        locale: 'Traditional Chinese',
        value: fieldValue(form, "[name=\"".concat(traditional, "\"]"))
      }, {
        group: group,
        required: required,
        locale: 'Simplified Chinese',
        value: fieldValue(form, "[name=\"".concat(simplified, "\"]"))
      }];
    });
    var requiredRows = contentRows.filter(function (row) {
      return row.required;
    });
    var filledCount = requiredRows.filter(function (row) {
      return plainText(row.value) !== '';
    }).length;
    document.querySelectorAll('[data-tour-review-content-summary]').forEach(function (element) {
      element.textContent = "".concat(filledCount, " of ").concat(requiredRows.length, " required fields filled");
    });
    document.querySelectorAll('[data-tour-review-content-list]').forEach(function (container) {
      container.innerHTML = contentFieldGroups.map(function (_ref5) {
        var _ref6 = _slicedToArray(_ref5, 5),
          group = _ref6[0],
          required = _ref6[1],
          english = _ref6[2],
          traditional = _ref6[3],
          simplified = _ref6[4];
        var fields = [['English', english], ['Traditional Chinese', traditional], ['Simplified Chinese', simplified]].map(function (_ref7) {
          var _ref8 = _slicedToArray(_ref7, 2),
            locale = _ref8[0],
            name = _ref8[1];
          var value = fieldValue(form, "[name=\"".concat(name, "\"]"));
          var isFilled = plainText(value) !== '';
          var stateClass = isFilled ? 'is-filled' : required ? 'is-empty' : 'is-optional';
          var stateLabel = isFilled ? 'Filled' : required ? 'Missing' : 'Optional';
          return "<div class=\"".concat(stateClass, "\">\n            <span>").concat(escapeHtml(locale), "</span>\n            <strong>").concat(stateLabel, "</strong>\n            <small>").concat(escapeHtml(truncate(value, 90)), "</small>\n          </div>");
        }).join('');
        return "<section><h4>".concat(escapeHtml(group), "</h4><div>").concat(fields, "</div></section>");
      }).join('');
    });
  };
  document.querySelectorAll('[data-tour-create-wizard]').forEach(function (wizard) {
    if (wizard.dataset.ready === 'true') {
      return;
    }
    wizard.dataset.ready = 'true';
    var form = wizard.closest('form');
    var steps = _toConsumableArray(wizard.querySelectorAll('[data-tour-wizard-step]'));
    var panels = _toConsumableArray(wizard.querySelectorAll('[data-tour-wizard-panel]'));
    var previousButton = wizard.querySelector('[data-tour-wizard-back]');
    var nextButton = wizard.querySelector('[data-tour-wizard-next]');
    var submitButton = wizard.querySelector('[data-tour-wizard-submit]');
    var currentLabel = wizard.querySelector('[data-tour-wizard-current-label]');
    var errorStep = wizard.dataset.errorStep;
    var activeStep = Math.max(0, steps.findIndex(function (step) {
      return step.dataset.tourWizardStep === errorStep;
    }));
    panels.forEach(function (panel) {
      panel.querySelectorAll('input:disabled, select:disabled, textarea:disabled, button:disabled').forEach(function (control) {
        control.dataset.wasDisabled = 'true';
      });
    });
    var setPanelFieldsState = function setPanelFieldsState() {
      panels.forEach(function (panel, index) {
        panel.querySelectorAll('input, select, textarea, button').forEach(function (control) {
          if (control.dataset.wasDisabled === 'true') return;
          control.disabled = index !== activeStep;
        });
      });
    };
    var enableAllFields = function enableAllFields() {
      panels.forEach(function (panel) {
        panel.querySelectorAll('input, select, textarea, button').forEach(function (control) {
          if (control.dataset.wasDisabled !== 'true') {
            control.disabled = false;
          }
        });
      });
    };
    var showStep = function showStep(index) {
      activeStep = Math.max(0, Math.min(index, panels.length - 1));
      steps.forEach(function (step, stepIndex) {
        var isActive = stepIndex === activeStep;
        step.classList.toggle('is-active', isActive);
        step.classList.toggle('is-complete', stepIndex < activeStep);
        step.setAttribute('aria-current', isActive ? 'step' : 'false');
      });
      panels.forEach(function (panel, panelIndex) {
        panel.classList.toggle('is-active', panelIndex === activeStep);
        panel.hidden = panelIndex !== activeStep;
      });
      if (currentLabel && steps[activeStep]) {
        currentLabel.textContent = steps[activeStep].dataset.stepTitle || steps[activeStep].textContent.trim();
      }
      if (previousButton) previousButton.disabled = activeStep === 0;
      if (nextButton) nextButton.hidden = activeStep === panels.length - 1;
      if (submitButton) submitButton.hidden = activeStep !== panels.length - 1;
      setPanelFieldsState();
      initRichText(panels[activeStep]);
      refreshCreateSummary();
    };
    var validateStep = function validateStep(index) {
      var panel = panels[index];
      if (!panel) return true;
      var controls = _toConsumableArray(panel.querySelectorAll('input, select, textarea')).filter(function (control) {
        return !control.disabled && control.type !== 'hidden';
      });
      var _iterator = _createForOfIteratorHelper(controls),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var control = _step.value;
          if (!control.checkValidity()) {
            control.reportValidity();
            return false;
          }
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      return true;
    };
    steps.forEach(function (step, index) {
      step.addEventListener('click', function () {
        if (index <= activeStep || validateStep(activeStep)) {
          showStep(index);
        }
      });
    });
    previousButton === null || previousButton === void 0 ? void 0 : previousButton.addEventListener('click', function () {
      return showStep(activeStep - 1);
    });
    nextButton === null || nextButton === void 0 ? void 0 : nextButton.addEventListener('click', function () {
      if (validateStep(activeStep)) {
        showStep(activeStep + 1);
      }
    });
    form === null || form === void 0 ? void 0 : form.addEventListener('submit', function (event) {
      event.preventDefault();
      enableAllFields();
      for (var index = 0; index < panels.length; index += 1) {
        showStep(index);
        enableAllFields();
        if (!validateStep(index)) {
          return;
        }
      }
      enableAllFields();
      form.submit();
    });
    showStep(activeStep);
  });
  document.addEventListener('tour:create-summary-refresh', refreshCreateSummary);
  document.addEventListener('input', function (event) {
    if (event.target.closest('[data-tour-create-wizard]')) refreshCreateSummary();
  });
  document.addEventListener('change', function (event) {
    if (event.target.closest('[data-tour-create-wizard]')) refreshCreateSummary();
  });
  refreshCreateSummary();
});
/******/ })()
;