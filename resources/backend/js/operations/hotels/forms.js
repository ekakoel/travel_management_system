document.addEventListener('DOMContentLoaded', () => {
  const coverPreviewUrls = new WeakMap();
  const galleryPreviewUrls = new WeakMap();

  const clearCoverPreview = (input, preview) => {
    const existingUrl = coverPreviewUrls.get(input);

    if (existingUrl) {
      URL.revokeObjectURL(existingUrl);
      coverPreviewUrls.delete(input);
    }

    preview?.replaceChildren();
  };

  document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || !input.matches('[data-hotel-cover-input]')) {
      return;
    }

    const preview = document.querySelector(input.dataset.hotelCoverPreviewTarget || '[data-hotel-cover-preview]');
    const status = document.querySelector('[data-hotel-cover-status]');
    const file = input.files?.[0] || null;

    clearCoverPreview(input, preview);

    if (!file) {
      if (status) {
        status.textContent = status.dataset.hotelCoverStatusDefault || 'No cover selected';
      }

      return;
    }

    if (!file.type.startsWith('image/')) {
      if (status) {
        status.textContent = 'Selected file is not a valid image';
      }

      return;
    }

    const previewUrl = URL.createObjectURL(file);
    const image = document.createElement('img');

    image.src = previewUrl;
    image.alt = file.name;
    coverPreviewUrls.set(input, previewUrl);
    preview?.replaceChildren(image);

    if (status) {
      status.textContent = file.name;
    }
  });

  const clearGalleryPreview = (input, preview) => {
    const existingUrls = galleryPreviewUrls.get(input) || [];

    existingUrls.forEach((url) => URL.revokeObjectURL(url));
    galleryPreviewUrls.delete(input);
    preview?.replaceChildren();
  };

  document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || !input.matches('[data-hotel-gallery-input]')) {
      return;
    }

    const preview = document.querySelector(input.dataset.hotelGalleryPreviewTarget || '[data-hotel-gallery-preview]');
    const status = document.querySelector(input.dataset.hotelGalleryStatusTarget || '[data-hotel-gallery-status]');
    const files = Array.from(input.files || []);

    clearGalleryPreview(input, preview);

    if (!files.length) {
      if (status) {
        status.textContent = 'No gallery files selected';
      }

      return;
    }

    const previewUrls = [];
    const fragment = document.createDocumentFragment();

    files.slice(0, 12).forEach((file) => {
      const item = document.createElement('figure');
      const caption = document.createElement('figcaption');

      item.className = 'hotel-gallery-upload-preview__item';
      caption.textContent = file.name;

      if (file.type.startsWith('image/')) {
        const previewUrl = URL.createObjectURL(file);
        const image = document.createElement('img');

        image.src = previewUrl;
        image.alt = file.name;
        previewUrls.push(previewUrl);
        item.appendChild(image);
      }

      item.appendChild(caption);
      fragment.appendChild(item);
    });

    galleryPreviewUrls.set(input, previewUrls);
    preview?.replaceChildren(fragment);

    if (status) {
      status.textContent = `${files.length} file${files.length === 1 ? '' : 's'} selected`;
    }
  });

  const repeater = document.querySelector('[data-hotel-price-repeater]');

  if (repeater) {
    const list = repeater.querySelector('[data-hotel-price-list]');
    const template = repeater.querySelector('[data-hotel-price-template]');
    const addButton = repeater.querySelector('[data-hotel-price-add]');

    addButton?.addEventListener('click', () => {
      if (!list || !template) {
        return;
      }

      const clone = template.content.firstElementChild.cloneNode(true);
      clone.querySelectorAll('input, select, textarea').forEach((field) => {
        if (field.type !== 'hidden') {
          field.value = '';
        }
      });
      list.appendChild(clone);
      window.initBackendRequiredMarkers?.(clone);
      window.initBackendMoneyInputs?.(clone);
      window.initBackendDatePickers?.(clone);
    });

    list?.addEventListener('click', (event) => {
      const removeButton = event.target.closest('[data-hotel-price-remove]');

      if (!removeButton) {
        return;
      }

      const rows = list.querySelectorAll('[data-hotel-price-row]');

      if (rows.length <= 1) {
        return;
      }

      removeButton.closest('[data-hotel-price-row]')?.remove();
    });
  }

  document.querySelectorAll('[data-hotel-autocomplete]').forEach((input) => {
    const target = document.querySelector(input.dataset.hotelAutocompleteTarget || '');
    const resultKey = input.dataset.hotelAutocompleteResults;
    const url = input.dataset.hotelAutocompleteUrl;

    if (!target || !url || !resultKey) {
      return;
    }

    input.addEventListener('keyup', async () => {
      const query = input.value.trim();

      if (query.length < 2) {
        target.hidden = true;
        target.innerHTML = '';
        return;
      }

      const endpoint = new URL(url, window.location.origin);
      endpoint.searchParams.set('query', query);

      try {
        const response = await fetch(endpoint.toString(), {
          headers: {
            Accept: 'application/json',
          },
        });
        const payload = await response.json();
        const suggestions = payload[resultKey] || [];

        target.innerHTML = suggestions
          .filter((item) => item.name)
          .map((item) => `<button type="button" class="hotel-form-suggestion" data-value="${item.name}">${item.name}</button>`)
          .join('');
        target.hidden = suggestions.length === 0;
      } catch (error) {
        target.hidden = true;
        target.innerHTML = '';
      }
    });

    target.addEventListener('click', (event) => {
      const item = event.target.closest('[data-value]');

      if (!item) {
        return;
      }

      input.value = item.dataset.value || '';
      target.hidden = true;
    });

    document.addEventListener('click', (event) => {
      if (!input.contains(event.target) && !target.contains(event.target)) {
        target.hidden = true;
      }
    });
  });
});
