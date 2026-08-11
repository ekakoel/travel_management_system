document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.tour-detail-page');
  const galleryBase = page?.dataset.tourGalleryBase;
  const csrfToken = page?.dataset.tourCsrf;
  const priceFormContext = page?.dataset.tourPriceFormContext;

  const updateMarkupInput = (typeSelect) => {
    const form = typeSelect.closest('form');
    const amountInput = form?.querySelector('[data-tour-markup-amount]');
    const label = form?.querySelector('[data-tour-markup-label]');
    const help = form?.querySelector('[data-tour-markup-help]');

    if (!amountInput || !label || !help) {
      return;
    }

    const config = {
      percentage: {
        label: 'Markup Percentage *',
        help: 'Percentage of the contract rate per pax (maximum 100%).',
        placeholder: '10.00',
        step: '0.01',
      },
      usd: {
        label: 'Markup USD *',
        help: 'USD amount per pax; maximum two decimal places.',
        placeholder: '20.00',
        step: '0.01',
      },
      idr: {
        label: 'Markup IDR *',
        help: 'Whole rupiah amount per pax.',
        placeholder: '250000',
        step: '1',
      },
    }[typeSelect.value] || null;

    if (!config) {
      return;
    }

    label.textContent = config.label;
    help.textContent = config.help;
    amountInput.placeholder = config.placeholder;
    amountInput.step = config.step;
  };

  document.querySelectorAll('[data-tour-markup-type]').forEach((typeSelect) => {
    updateMarkupInput(typeSelect);
    typeSelect.addEventListener('change', () => updateMarkupInput(typeSelect));
  });

  if (priceFormContext && window.jQuery?.fn?.modal) {
    const updateContext = /^update:(\d+)$/.exec(priceFormContext);
    const modalSelector = priceFormContext === 'create'
      ? '#add-price'
      : updateContext
        ? `#update-price-${updateContext[1]}`
        : null;

    if (modalSelector && document.querySelector(modalSelector)) {
      window.jQuery(modalSelector).modal('show');
    }
  }

  const applyPriceFilters = () => {
    const capacity = document.querySelector('[data-tour-price-filter="capacity"]')?.value.trim().toLowerCase() || '';
    const review = document.querySelector('[data-tour-price-filter="review"]')?.value || '';

    document.querySelectorAll('[data-tour-price-row]').forEach((row) => {
      const matchesCapacity = (row.dataset.tourPriceCapacity || '').includes(capacity);
      const matchesReview = !review || row.dataset.tourPriceReview === review;
      row.classList.toggle('is-filtered-out', !matchesCapacity || !matchesReview);
    });
  };

  document.querySelectorAll('[data-tour-price-filter]').forEach((input) => {
    input.addEventListener('input', applyPriceFilters);
    input.addEventListener('change', applyPriceFilters);
  });

  document.querySelectorAll('[data-tour-price-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const capacity = button.dataset.tourPriceDelete || 'this price row';
      const confirmed = window.confirm(`Are you sure you want to delete ${capacity}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('[data-tour-gallery-delete]').forEach((button) => {
    button.addEventListener('click', async () => {
      if (!galleryBase || !csrfToken) {
        return;
      }

      const imageId = button.dataset.tourGalleryDelete;
      const confirmed = window.confirm('Are you sure you want to delete this image?');

      if (!confirmed) {
        return;
      }

      const response = await fetch(`${galleryBase}/${imageId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
      });
      const payload = await response.json();

      if (payload.success) {
        document.getElementById(`image-${imageId}`)?.remove();
      } else {
        window.alert(payload.message || 'Failed to delete image.');
      }
    });
  });

  document.querySelectorAll('[data-tour-gallery-update]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!galleryBase || !csrfToken) {
        return;
      }

      const imageId = button.dataset.tourGalleryUpdate;
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = 'image/*';
      input.addEventListener('change', async () => {
        if (!input.files?.length) {
          return;
        }

        const formData = new FormData();
        formData.append('file', input.files[0]);

        const response = await fetch(`${galleryBase}/${imageId}/update`, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            Accept: 'application/json',
          },
        });
        const payload = await response.json();

        if (payload.success) {
          const image = document.querySelector(`#image-${imageId} img`);
          if (image) {
            image.src = `${payload.url}?v=${Date.now()}`;
          }
        } else {
          window.alert(payload.message || 'Failed to update image.');
        }
      });
      input.click();
    });
  });
});
