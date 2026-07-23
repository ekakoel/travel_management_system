document.addEventListener('DOMContentLoaded', () => {
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
