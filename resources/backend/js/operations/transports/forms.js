document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-transport-form]').forEach((form) => {
    form.dataset.transportFormReady = 'true';
  });

  document.querySelectorAll('[data-transport-cover-input]').forEach((input) => {
    input.addEventListener('change', () => {
      const file = input.files && input.files[0] ? input.files[0] : null;
      const preview = document.querySelector('[data-transport-cover-preview]');
      const status = document.querySelector('[data-transport-cover-status]');

      if (status) {
        status.textContent = file ? file.name : status.dataset.transportFileInputDefault || 'No cover selected';
      }

      if (!file || !preview || !file.type.startsWith('image/')) {
        return;
      }

      const reader = new FileReader();
      reader.addEventListener('load', () => {
        const image = document.createElement('img');
        image.src = reader.result;
        image.alt = 'Selected transport cover preview';
        preview.replaceChildren(image);
      });
      reader.readAsDataURL(file);
    });
  });

  document.querySelectorAll('[data-transport-gallery-input]').forEach((input) => {
    input.addEventListener('change', () => {
      const files = Array.from(input.files || []);
      const statusSelector = input.dataset.transportFileInputTarget;
      const status = statusSelector ? document.querySelector(statusSelector) : null;
      const preview = document.querySelector('[data-transport-gallery-preview]');

      if (status) {
        const defaultText = status.dataset.transportFileInputDefault || 'No gallery images selected';
        status.textContent = files.length ? `${files.length} image${files.length === 1 ? '' : 's'} selected` : defaultText;
      }

      if (!preview) {
        return;
      }

      preview.innerHTML = '';

      files
        .filter((file) => file.type.startsWith('image/'))
        .slice(0, 8)
        .forEach((file) => {
          const reader = new FileReader();
          reader.addEventListener('load', () => {
            const item = document.createElement('figure');
            item.className = 'transport-gallery-preview__item';
            const image = document.createElement('img');
            image.src = reader.result;
            image.alt = file.name;
            const caption = document.createElement('figcaption');
            caption.textContent = file.name;
            item.append(image, caption);
            preview.appendChild(item);
          });
          reader.readAsDataURL(file);
        });
    });
  });

  document.querySelectorAll('[data-transport-gallery-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const transportName = button.dataset.transportGalleryDelete || 'this transport gallery image';
      const confirmed = window.confirm(`Are you sure you want to remove a gallery image from ${transportName}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
