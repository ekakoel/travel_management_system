document.addEventListener('DOMContentLoaded', () => {
  const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[character]));

  document.querySelectorAll('[data-tour-locations-repeater]').forEach((repeater) => {
    if (repeater.dataset.ready === 'true') {
      return;
    }

    repeater.dataset.ready = 'true';

    const list = repeater.querySelector('[data-tour-location-list]');
    const template = repeater.querySelector('[data-tour-location-template]');
    const addButton = repeater.querySelector('[data-add-tour-location]');
    const resolveUrl = repeater.dataset.resolveUrl;
    const referencesUrl = repeater.dataset.referencesUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const resolveTimers = new WeakMap();
    const referenceTimers = new WeakMap();

    const refreshIndexes = () => {
      list?.querySelectorAll('[data-tour-location-item]').forEach((item, index) => {
        const number = item.querySelector('[data-tour-location-number]');
        if (number) {
          number.textContent = index + 1;
        }

        item.querySelectorAll('[data-field-name]').forEach((field) => {
          field.name = `locations[${index}][${field.dataset.fieldName}]`;
        });

        const order = item.querySelector(`[name="locations[${index}][visit_order]"]`);
        if (order && !order.value) {
          order.value = index + 1;
        }
      });
    };

    const setStatus = (item, message, state = 'text-muted') => {
      const status = item.querySelector('[data-tour-coordinate-status]');
      if (!status) {
        return;
      }

      status.textContent = message;
      status.classList.remove('text-muted', 'text-success', 'text-danger');
      status.classList.add(state);
    };

    const field = (item, selector) => item.querySelector(selector);

    const initRichText = (root) => {
      if (window.initBackendRichText) {
        window.initBackendRichText(root);
      }
    };

    const setRichTextValue = (element, value) => {
      if (window.setBackendRichTextValue) {
        window.setBackendRichTextValue(element, value);
        return;
      }

      if (element) {
        element.value = value || '';
      }
    };

    const closeSuggestions = (item) => {
      const menu = item.querySelector('[data-tour-location-suggestions]');
      if (menu) {
        menu.innerHTML = '';
        menu.classList.remove('is-open');
      }
    };

    const fillLocationFromReference = (item, location) => {
      const referenceInput = field(item, '[data-tour-location-reference-id]');
      const nameInput = field(item, '[data-tour-location-name]');
      const typeInput = field(item, '[data-field-name="location_type"], select[name$="[location_type]"]');
      const latitudeInput = field(item, '[data-tour-location-latitude]');
      const longitudeInput = field(item, '[data-tour-location-longitude]');
      const mapsInput = field(item, '[data-tour-location-map-url]');
      const existingImageInput = field(item, '[data-field-name="existing_marker_image"], input[name$="[existing_marker_image]"]');
      const descriptionInput = field(item, '[data-field-name="description"], textarea[name$="[description]"]');

      if (referenceInput) referenceInput.value = location.id || '';
      if (nameInput) nameInput.value = location.destination_name || '';
      if (typeInput) typeInput.value = location.location_type || 'Attraction';
      if (latitudeInput) latitudeInput.value = location.latitude || '';
      if (longitudeInput) longitudeInput.value = location.longitude || '';
      if (mapsInput) mapsInput.value = location.google_maps_url || '';
      if (existingImageInput) existingImageInput.value = location.marker_image || '';
      if (descriptionInput) setRichTextValue(descriptionInput, location.description || '');

      const preview = item.querySelector('[data-tour-location-image-preview]');
      if (preview && location.marker_image_url) {
        const image = document.createElement('img');
        image.src = location.marker_image_url;
        image.alt = 'Marker image';
        image.className = 'tour-location-marker-preview';
        preview.replaceChildren(image);
      }

      closeSuggestions(item);
    };

    const renderSuggestions = (item, locations) => {
      const menu = item.querySelector('[data-tour-location-suggestions]');
      if (!menu) {
        return;
      }

      if (!locations.length) {
        menu.innerHTML = '<div class="tour-location-suggest__empty">No saved location found.</div>';
        menu.classList.add('is-open');
        return;
      }

      menu.innerHTML = locations.map((location) => {
        const image = location.marker_image_url
          ? `<img src="${escapeHtml(location.marker_image_url)}" alt="">`
          : `<span class="tour-location-suggest__avatar">${escapeHtml((location.destination_name || '?').charAt(0))}</span>`;

        return `<button type="button" class="tour-location-suggest__item" data-reference-id="${location.id}">
          ${image}
          <span><strong>${escapeHtml(location.destination_name)}</strong><small>${escapeHtml(location.location_type)} · ${escapeHtml(location.latitude)}, ${escapeHtml(location.longitude)}</small></span>
        </button>`;
      }).join('');

      menu.querySelectorAll('[data-reference-id]').forEach((button, index) => {
        button.addEventListener('click', () => fillLocationFromReference(item, locations[index]));
      });

      menu.classList.add('is-open');
    };

    const searchLocationReferences = (item) => {
      if (!referencesUrl) {
        return;
      }

      const nameInput = item.querySelector('[data-tour-location-name]');
      const query = nameInput ? nameInput.value.trim() : '';

      if (query.length < 2) {
        closeSuggestions(item);
        return;
      }

      fetch(`${referencesUrl}?q=${encodeURIComponent(query)}`, {
        headers: { Accept: 'application/json' },
      })
        .then((response) => response.json())
        .then((locations) => renderSuggestions(item, Array.isArray(locations) ? locations : []))
        .catch(() => closeSuggestions(item));
    };

    const queueLocationReferenceSearch = (item) => {
      const existingTimer = referenceTimers.get(item);
      if (existingTimer) {
        clearTimeout(existingTimer);
      }

      referenceTimers.set(item, setTimeout(() => searchLocationReferences(item), 260));
    };

    const resolveCoordinates = (item) => {
      if (!resolveUrl) {
        return;
      }

      const urlInput = item.querySelector('[data-tour-location-map-url]');
      const latitudeInput = item.querySelector('[data-tour-location-latitude]');
      const longitudeInput = item.querySelector('[data-tour-location-longitude]');
      const googleMapsUrl = urlInput ? urlInput.value.trim() : '';

      if (!googleMapsUrl || !latitudeInput || !longitudeInput) {
        return;
      }

      setStatus(item, 'Reading coordinates from Google Maps link...');

      fetch(resolveUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ google_maps_url: googleMapsUrl }),
      })
        .then((response) => response.json().then((payload) => {
          if (!response.ok) {
            throw payload;
          }

          return payload;
        }))
        .then((payload) => {
          latitudeInput.value = payload.latitude;
          longitudeInput.value = payload.longitude;
          setStatus(item, 'Coordinates filled automatically from Google Maps link.', 'text-success');
        })
        .catch((error) => {
          setStatus(item, error.message || 'Coordinates could not be read. Please fill latitude and longitude manually.', 'text-danger');
        });
    };

    const queueResolveCoordinates = (item) => {
      const existingTimer = resolveTimers.get(item);
      if (existingTimer) {
        clearTimeout(existingTimer);
      }

      resolveTimers.set(item, setTimeout(() => resolveCoordinates(item), 600));
    };

    if (addButton && template && list) {
      addButton.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        const item = fragment.querySelector('[data-tour-location-item]');

        list.appendChild(fragment);
        refreshIndexes();
        initRichText(item || list);
      });
    }

    repeater.addEventListener('click', (event) => {
      const removeButton = event.target.closest('[data-remove-tour-location]');
      if (!removeButton) {
        return;
      }

      const item = removeButton.closest('[data-tour-location-item]');
      if (item && list.querySelectorAll('[data-tour-location-item]').length > 1) {
        item.remove();
        refreshIndexes();
      }
    });

    repeater.addEventListener('input', (event) => {
      if (event.target.matches('[data-tour-location-name]')) {
        const nameItem = event.target.closest('[data-tour-location-item]');
        const referenceInput = nameItem ? nameItem.querySelector('[data-tour-location-reference-id]') : null;

        if (referenceInput) {
          referenceInput.value = '';
        }

        if (nameItem) {
          queueLocationReferenceSearch(nameItem);
        }

        return;
      }

      if (!event.target.matches('[data-tour-location-map-url]')) {
        return;
      }

      const item = event.target.closest('[data-tour-location-item]');
      if (item) {
        queueResolveCoordinates(item);
      }
    });

    repeater.addEventListener('change', (event) => {
      if (!event.target.matches('[data-tour-location-map-url]')) {
        return;
      }

      const item = event.target.closest('[data-tour-location-item]');
      if (item) {
        resolveCoordinates(item);
      }
    });

    document.addEventListener('click', (event) => {
      if (repeater.contains(event.target)) {
        return;
      }

      repeater.querySelectorAll('[data-tour-location-item]').forEach(closeSuggestions);
    });

    refreshIndexes();
  });
});
