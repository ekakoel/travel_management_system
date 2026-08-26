document.addEventListener('DOMContentLoaded', () => {
  const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[character]));

  const fieldValue = (form, selector) => {
    const element = form?.querySelector(selector);

    if (!element) {
      return '';
    }

    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.summernote) {
      const textarea = window.jQuery(element);

      if (textarea.next('.note-editor').length) {
        return String(textarea.summernote('code') || '').trim();
      }
    }

    return element.value?.trim() || '';
  };

  const plainText = (value) => {
    const wrapper = document.createElement('div');
    wrapper.innerHTML = value || '';

    return (wrapper.textContent || wrapper.innerText || '').replace(/\s+/g, ' ').trim();
  };

  const truncate = (value, limit = 110) => {
    const text = plainText(value);

    if (!text) {
      return 'Not filled';
    }

    return text.length > limit ? `${text.slice(0, limit - 1)}...` : text;
  };

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

  document.querySelectorAll('[data-tour-cover-input]').forEach((input) => {
    if (input.dataset.ready === 'true') {
      return;
    }

    input.dataset.ready = 'true';

    input.addEventListener('change', () => {
      const preview = document.querySelector(input.dataset.tourCoverPreviewTarget || '[data-tour-cover-preview]');
      const status = document.querySelector('[data-tour-cover-status]');
      const defaultStatus = status?.dataset.tourCoverStatusDefault || 'No cover selected';
      const file = input.files?.[0] || null;

      if (status) {
        status.textContent = file ? file.name : defaultStatus;
      }

      if (preview) {
        preview.innerHTML = '';

        if (file && file.type.startsWith('image/')) {
          const image = document.createElement('img');
          image.src = URL.createObjectURL(file);
          image.alt = file.name;
          image.onload = () => URL.revokeObjectURL(image.src);
          preview.appendChild(image);
        }
      }

      document.dispatchEvent(new CustomEvent('tour:create-summary-refresh'));
    });
  });

  const refreshLocationSummaries = (repeater) => {
    const list = repeater.querySelector('[data-tour-location-list]');
    const empty = repeater.querySelector('[data-tour-location-empty]');
    const items = [...(list?.querySelectorAll('[data-tour-location-item]') || [])];

    empty?.classList.toggle('d-none', items.length > 0);

    items.forEach((item, index) => {
      const number = item.querySelector('[data-tour-location-number]');
      const dayInput = item.querySelector('[data-field-name="day_number"]');
      const orderInput = item.querySelector('[data-field-name="visit_order"]');
      const nameInput = item.querySelector('[data-tour-location-name]');
      const typeInput = item.querySelector('[data-field-name="location_type"]');
      const timeInput = item.querySelector('[data-field-name="visit_time"]');
      const latitudeInput = item.querySelector('[data-tour-location-latitude]');
      const longitudeInput = item.querySelector('[data-tour-location-longitude]');

      if (number) number.textContent = index + 1;

      item.querySelectorAll('[data-field-name]').forEach((field) => {
        field.name = `locations[${index}][${field.dataset.fieldName}]`;
      });

      if (dayInput && !dayInput.value) dayInput.value = '1';
      if (orderInput) orderInput.value = index + 1;

      const dayLabel = item.querySelector('[data-tour-location-day-label]');
      const title = item.querySelector('[data-tour-location-title]');
      const typeLabel = item.querySelector('[data-tour-location-type-label]');
      const timeLabel = item.querySelector('[data-tour-location-time-label]');
      const coordinateLabel = item.querySelector('[data-tour-location-coordinate-label]');

      if (dayLabel) dayLabel.textContent = dayInput?.value || '1';
      if (title) title.textContent = nameInput?.value?.trim() || 'Untitled stop';
      if (typeLabel) typeLabel.textContent = typeInput?.value || 'Attraction';
      if (timeLabel) timeLabel.textContent = timeInput?.value || 'No time';
      if (coordinateLabel) {
        coordinateLabel.textContent = latitudeInput?.value && longitudeInput?.value
          ? 'Coordinates available'
          : 'Coordinates missing';
      }
    });

    document.dispatchEvent(new CustomEvent('tour:create-summary-refresh'));
  };

  document.querySelectorAll('[data-tour-locations-repeater]').forEach((repeater) => {
    if (repeater.dataset.ready === 'true') {
      return;
    }

    repeater.dataset.ready = 'true';

    const list = repeater.querySelector('[data-tour-location-list]');
    const template = repeater.querySelector('[data-tour-location-template]');
    const addButton = repeater.querySelector('[data-add-tour-location]');
    const allowEmpty = repeater.dataset.allowEmpty === 'true';
    const resolveUrl = repeater.dataset.resolveUrl;
    const referencesUrl = repeater.dataset.referencesUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const resolveTimers = new WeakMap();
    const referenceTimers = new WeakMap();
    let draggedLocationItem = null;

    const setStatus = (item, message, state = 'text-muted') => {
      const status = item.querySelector('[data-tour-coordinate-status]');
      if (!status) return;

      status.textContent = message;
      status.classList.remove('text-muted', 'text-success', 'text-danger');
      status.classList.add(state);
    };

    const showManualCoordinates = (item) => {
      item.querySelectorAll('[data-tour-manual-coordinate-field]').forEach((field) => {
        field.classList.remove('d-none');
      });
    };

    const closeSuggestions = (item) => {
      const menu = item.querySelector('[data-tour-location-suggestions]');
      if (menu) {
        menu.innerHTML = '';
        menu.classList.remove('is-open');
      }
    };

    const renderLocationMarkerPreview = (item, imageUrl = '') => {
      const preview = item.querySelector('[data-tour-location-image-preview]');
      if (!preview) return;

      if (!imageUrl) {
        preview.innerHTML = '<span>No marker cover selected</span>';
        return;
      }

      const image = document.createElement('img');
      image.src = imageUrl;
      image.alt = 'Marker image';
      image.className = 'tour-location-marker-preview';
      preview.replaceChildren(image);
    };

    const fillLocationFromReference = (item, location) => {
      const referenceInput = item.querySelector('[data-tour-location-reference-id]');
      const nameInput = item.querySelector('[data-tour-location-name]');
      const typeInput = item.querySelector('[data-field-name="location_type"], select[name$="[location_type]"]');
      const latitudeInput = item.querySelector('[data-tour-location-latitude]');
      const longitudeInput = item.querySelector('[data-tour-location-longitude]');
      const mapsInput = item.querySelector('[data-tour-location-map-url]');
      const existingImageInput = item.querySelector('[data-field-name="existing_marker_image"], input[name$="[existing_marker_image]"]');
      const descriptionInput = item.querySelector('[data-field-name="description"], textarea[name$="[description]"]');
      const traditionalDescriptionInput = item.querySelector('[data-field-name="description_traditional"], textarea[name$="[description_traditional]"]');
      const simplifiedDescriptionInput = item.querySelector('[data-field-name="description_simplified"], textarea[name$="[description_simplified]"]');

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

    const renderSuggestions = (item, locations) => {
      const menu = item.querySelector('[data-tour-location-suggestions]');
      if (!menu) return;

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
          <span><strong>${escapeHtml(location.destination_name)}</strong><small>${escapeHtml(location.location_type)} | ${escapeHtml(location.latitude)}, ${escapeHtml(location.longitude)}</small></span>
        </button>`;
      }).join('');

      menu.querySelectorAll('[data-reference-id]').forEach((button, index) => {
        button.addEventListener('click', () => fillLocationFromReference(item, locations[index]));
      });

      menu.classList.add('is-open');
    };

    const searchLocationReferences = (item) => {
      if (!referencesUrl) return;

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
      if (existingTimer) clearTimeout(existingTimer);
      referenceTimers.set(item, setTimeout(() => searchLocationReferences(item), 260));
    };

    const resolveCoordinates = (item) => {
      if (!resolveUrl) return;

      const urlInput = item.querySelector('[data-tour-location-map-url]');
      const latitudeInput = item.querySelector('[data-tour-location-latitude]');
      const longitudeInput = item.querySelector('[data-tour-location-longitude]');
      const googleMapsUrl = urlInput ? urlInput.value.trim() : '';

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
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ google_maps_url: googleMapsUrl }),
      })
        .then((response) => response.json().then((payload) => {
          if (!response.ok) throw payload;
          return payload;
        }))
        .then((payload) => {
          latitudeInput.value = payload.latitude;
          longitudeInput.value = payload.longitude;
          setStatus(item, `Location found. Latitude ${payload.latitude}, longitude ${payload.longitude}.`, 'text-success');
          refreshLocationSummaries(repeater);
        })
        .catch((error) => {
          showManualCoordinates(item);
          setStatus(item, error.message || 'Coordinates could not be read. Fill latitude and longitude manually.', 'text-danger');
        });
    };

    const queueResolveCoordinates = (item) => {
      const existingTimer = resolveTimers.get(item);
      if (existingTimer) clearTimeout(existingTimer);
      resolveTimers.set(item, setTimeout(() => resolveCoordinates(item), 600));
    };

    const locationAfterDragTarget = (container, y) => {
      const draggableItems = [...container.querySelectorAll('[data-tour-location-item]:not(.is-dragging)')];

      return draggableItems.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - (box.height / 2);

        if (offset < 0 && offset > closest.offset) {
          return { offset, element: child };
        }

        return closest;
      }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
    };

    addButton?.addEventListener('click', () => {
      if (!template || !list) return;

      const fragment = template.content.cloneNode(true);
      const item = fragment.querySelector('[data-tour-location-item]');

      list.appendChild(fragment);
      refreshLocationSummaries(repeater);
      initRichText(item || list);
    });

    repeater.addEventListener('click', (event) => {
      const toggleButton = event.target.closest('[data-toggle-tour-location-editor]');
      if (toggleButton) {
        toggleButton.closest('[data-tour-location-item]')?.classList.toggle('is-collapsed');
        return;
      }

      const removeButton = event.target.closest('[data-remove-tour-location]');
      if (!removeButton || !list) return;

      const item = removeButton.closest('[data-tour-location-item]');
      const count = list.querySelectorAll('[data-tour-location-item]').length;

      if (item && (allowEmpty || count > 1)) {
        item.remove();
        refreshLocationSummaries(repeater);
      }
    });

    repeater.addEventListener('dragstart', (event) => {
      const handle = event.target.closest('[data-tour-location-drag-handle]');
      if (!handle || !list) return;

      draggedLocationItem = handle.closest('[data-tour-location-item]');
      if (!draggedLocationItem) return;

      draggedLocationItem.classList.add('is-dragging');
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', '');
    });

    repeater.addEventListener('dragover', (event) => {
      if (!draggedLocationItem || !list) return;

      event.preventDefault();
      const afterElement = locationAfterDragTarget(list, event.clientY);

      if (!afterElement) {
        list.appendChild(draggedLocationItem);
      } else if (afterElement !== draggedLocationItem) {
        list.insertBefore(draggedLocationItem, afterElement);
      }
    });

    repeater.addEventListener('dragend', () => {
      if (!draggedLocationItem) return;

      draggedLocationItem.classList.remove('is-dragging');
      draggedLocationItem = null;
      refreshLocationSummaries(repeater);
    });

    repeater.addEventListener('input', (event) => {
      const item = event.target.closest('[data-tour-location-item]');
      if (!item) return;

      if (event.target.matches('[data-tour-location-name]')) {
        const referenceInput = item.querySelector('[data-tour-location-reference-id]');
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

    repeater.addEventListener('change', (event) => {
      const item = event.target.closest('[data-tour-location-item]');
      if (!item) return;

      if (event.target.matches('[data-tour-location-map-url]')) {
        resolveCoordinates(item);
      }

      if (event.target.matches('[data-field-name="marker_image"]')) {
        const file = event.target.files?.[0] || null;
        const existingImageInput = item.querySelector('[data-field-name="existing_marker_image"], input[name$="[existing_marker_image]"]');

        if (existingImageInput && file) {
          existingImageInput.value = '';
        }

        if (file && file.type.startsWith('image/')) {
          const previewUrl = URL.createObjectURL(file);
          renderLocationMarkerPreview(item, previewUrl);
          const previewImage = item.querySelector('[data-tour-location-image-preview] img');
          if (previewImage) {
            previewImage.onload = () => URL.revokeObjectURL(previewUrl);
          }
        } else if (!file) {
          renderLocationMarkerPreview(item, '');
        }
      }

      refreshLocationSummaries(repeater);
    });

    document.addEventListener('click', (event) => {
      if (repeater.contains(event.target)) return;
      repeater.querySelectorAll('[data-tour-location-item]').forEach(closeSuggestions);
    });

    refreshLocationSummaries(repeater);
  });

  const refreshCreateSummary = () => {
    const wizard = document.querySelector('[data-tour-create-wizard]');
    const form = wizard?.closest('form');

    if (!form) return;

    const routeItems = [...document.querySelectorAll('[data-tour-location-item]')];
    const days = new Set(routeItems.map((item) => item.querySelector('[data-field-name="day_number"]')?.value).filter(Boolean));
    const typeSelect = form.querySelector('[name="type"]');
    const typeLabel = typeSelect?.selectedOptions?.[0]?.textContent?.trim() || 'Not selected';
    const statusSelect = form.querySelector('[name="status"]');
    const coverInput = form.querySelector('[data-tour-cover-input]');
    const coverDefault = coverInput?.dataset.tourCoverExisting || 'No cover selected';
    const contentFieldGroups = [
      ['Short Description', true, 'short_description', 'short_description_traditional', 'short_description_simplified'],
      ['Description', true, 'description', 'description_traditional', 'description_simplified'],
      ['Package Highlights', false, 'package_highlights', 'package_highlights_traditional', 'package_highlights_simplified'],
      ['Include', false, 'include', 'include_traditional', 'include_simplified'],
      ['Exclude', false, 'exclude', 'exclude_traditional', 'exclude_simplified'],
      ['Additional Information', false, 'additional_info', 'additional_info_traditional', 'additional_info_simplified'],
      ['Cancellation Policy', true, 'cancellation_policy', 'cancellation_policy_traditional', 'cancellation_policy_simplified'],
    ];
    const summaryValues = {
      '[data-tour-summary-name]': fieldValue(form, '[name="name"]') || 'Not filled',
      '[data-tour-summary-type]': typeLabel,
      '[data-tour-summary-duration]': `${fieldValue(form, '[name="duration_days"]') || '1'}D / ${fieldValue(form, '[name="duration_nights"]') || '0'}N`,
      '[data-tour-summary-route]': `${days.size || 0} day(s), ${routeItems.length} stop(s)`,
      '[data-tour-summary-cover]': coverInput?.files?.[0]?.name || coverDefault,
      '[data-tour-review-status]': statusSelect?.value || 'Draft',
      '[data-tour-review-code]': fieldValue(form, '[name="code"]') || 'Not filled',
      '[data-tour-review-name-en]': fieldValue(form, '[name="name"]') || 'Not filled',
      '[data-tour-review-name-traditional]': fieldValue(form, '[name="name_traditional"]') || 'Not filled',
      '[data-tour-review-name-simplified]': fieldValue(form, '[name="name_simplified"]') || 'Not filled',
    };

    Object.entries(summaryValues).forEach(([selector, value]) => {
      document.querySelectorAll(selector).forEach((element) => {
        element.textContent = value;
      });
    });

    document.querySelectorAll('[data-tour-review-route-days]').forEach((container) => {
      if (!routeItems.length) {
        container.innerHTML = '<p>No route stops added yet.</p>';
        return;
      }

      const grouped = routeItems.reduce((carry, item, index) => {
        const day = item.querySelector('[data-field-name="day_number"]')?.value || '1';
        const order = item.querySelector('[data-field-name="visit_order"]')?.value || String(index + 1);
        const time = item.querySelector('[data-field-name="visit_time"]')?.value || '';
        const name = item.querySelector('[data-tour-location-name]')?.value?.trim() || 'Untitled stop';
        const type = item.querySelector('[data-field-name="location_type"]')?.value || 'Attraction';
        const latitude = item.querySelector('[data-tour-location-latitude]')?.value || '';
        const longitude = item.querySelector('[data-tour-location-longitude]')?.value || '';

        carry[day] = carry[day] || [];
        carry[day].push({
          order: Number(order) || index + 1,
          time,
          name,
          type,
          hasCoordinates: Boolean(latitude && longitude),
        });

        return carry;
      }, {});

      container.innerHTML = Object.keys(grouped)
        .sort((first, second) => Number(first) - Number(second))
        .map((day) => {
          const stops = grouped[day]
            .sort((first, second) => first.order - second.order)
            .map((stop) => `<li>
              <strong>${escapeHtml(stop.time ? `${stop.time} | ${stop.name}` : stop.name)}</strong>
              <small>${escapeHtml(stop.type)} | ${stop.hasCoordinates ? 'Coordinates available' : 'Coordinates missing'}</small>
            </li>`)
            .join('');

          return `<section><h4>Day ${escapeHtml(day)} <span>${grouped[day].length} stop(s)</span></h4><ol>${stops}</ol></section>`;
        })
        .join('');
    });

    const contentRows = contentFieldGroups.flatMap(([group, required, english, traditional, simplified]) => [
      { group, required, locale: 'English', value: fieldValue(form, `[name="${english}"]`) },
      { group, required, locale: 'Traditional Chinese', value: fieldValue(form, `[name="${traditional}"]`) },
      { group, required, locale: 'Simplified Chinese', value: fieldValue(form, `[name="${simplified}"]`) },
    ]);
    const requiredRows = contentRows.filter((row) => row.required);
    const filledCount = requiredRows.filter((row) => plainText(row.value) !== '').length;

    document.querySelectorAll('[data-tour-review-content-summary]').forEach((element) => {
      element.textContent = `${filledCount} of ${requiredRows.length} required fields filled`;
    });

    document.querySelectorAll('[data-tour-review-content-list]').forEach((container) => {
      container.innerHTML = contentFieldGroups.map(([group, required, english, traditional, simplified]) => {
        const fields = [
          ['English', english],
          ['Traditional Chinese', traditional],
          ['Simplified Chinese', simplified],
        ].map(([locale, name]) => {
          const value = fieldValue(form, `[name="${name}"]`);
          const isFilled = plainText(value) !== '';
          const stateClass = isFilled ? 'is-filled' : (required ? 'is-empty' : 'is-optional');
          const stateLabel = isFilled ? 'Filled' : (required ? 'Missing' : 'Optional');

          return `<div class="${stateClass}">
            <span>${escapeHtml(locale)}</span>
            <strong>${stateLabel}</strong>
            <small>${escapeHtml(truncate(value, 90))}</small>
          </div>`;
        }).join('');

        return `<section><h4>${escapeHtml(group)}</h4><div>${fields}</div></section>`;
      }).join('');
    });
  };

  document.querySelectorAll('[data-tour-create-wizard]').forEach((wizard) => {
    if (wizard.dataset.ready === 'true') {
      return;
    }

    wizard.dataset.ready = 'true';

    const form = wizard.closest('form');
    const steps = [...wizard.querySelectorAll('[data-tour-wizard-step]')];
    const panels = [...wizard.querySelectorAll('[data-tour-wizard-panel]')];
    const previousButton = wizard.querySelector('[data-tour-wizard-back]');
    const nextButton = wizard.querySelector('[data-tour-wizard-next]');
    const submitButton = wizard.querySelector('[data-tour-wizard-submit]');
    const currentLabel = wizard.querySelector('[data-tour-wizard-current-label]');
    const errorStep = wizard.dataset.errorStep;
    let activeStep = Math.max(0, steps.findIndex((step) => step.dataset.tourWizardStep === errorStep));

    panels.forEach((panel) => {
      panel.querySelectorAll('input:disabled, select:disabled, textarea:disabled, button:disabled').forEach((control) => {
        control.dataset.wasDisabled = 'true';
      });
    });

    const setPanelFieldsState = () => {
      panels.forEach((panel, index) => {
        panel.querySelectorAll('input, select, textarea, button').forEach((control) => {
          if (control.dataset.wasDisabled === 'true') return;
          control.disabled = index !== activeStep;
        });
      });
    };

    const enableAllFields = () => {
      panels.forEach((panel) => {
        panel.querySelectorAll('input, select, textarea, button').forEach((control) => {
          if (control.dataset.wasDisabled !== 'true') {
            control.disabled = false;
          }
        });
      });
    };

    const showStep = (index) => {
      activeStep = Math.max(0, Math.min(index, panels.length - 1));

      steps.forEach((step, stepIndex) => {
        const isActive = stepIndex === activeStep;
        step.classList.toggle('is-active', isActive);
        step.classList.toggle('is-complete', stepIndex < activeStep);
        step.setAttribute('aria-current', isActive ? 'step' : 'false');
      });

      panels.forEach((panel, panelIndex) => {
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

    const validateStep = (index) => {
      const panel = panels[index];
      if (!panel) return true;

      const controls = [...panel.querySelectorAll('input, select, textarea')].filter((control) => {
        return !control.disabled && control.type !== 'hidden';
      });

      for (const control of controls) {
        if (!control.checkValidity()) {
          control.reportValidity();
          return false;
        }
      }

      return true;
    };

    steps.forEach((step, index) => {
      step.addEventListener('click', () => {
        if (index <= activeStep || validateStep(activeStep)) {
          showStep(index);
        }
      });
    });

    previousButton?.addEventListener('click', () => showStep(activeStep - 1));
    nextButton?.addEventListener('click', () => {
      if (validateStep(activeStep)) {
        showStep(activeStep + 1);
      }
    });

    form?.addEventListener('submit', (event) => {
      event.preventDefault();
      enableAllFields();

      for (let index = 0; index < panels.length; index += 1) {
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
  document.addEventListener('input', (event) => {
    if (event.target.closest('[data-tour-create-wizard]')) refreshCreateSummary();
  });
  document.addEventListener('change', (event) => {
    if (event.target.closest('[data-tour-create-wizard]')) refreshCreateSummary();
  });
  refreshCreateSummary();
});
