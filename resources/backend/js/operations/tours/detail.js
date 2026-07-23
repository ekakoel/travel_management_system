document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.tour-detail-page');
  const galleryBase = page?.dataset.tourGalleryBase;
  const csrfToken = page?.dataset.tourCsrf;

  document.querySelectorAll('[data-tour-price-filter]').forEach((input) => {
    input.addEventListener('input', () => {
      const value = input.value.trim().toLowerCase();

      document.querySelectorAll('[data-tour-price-row]').forEach((row) => {
        const capacity = row.dataset.tourPriceCapacity || '';
        row.classList.toggle('is-filtered-out', !capacity.includes(value));
      });
    });
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
