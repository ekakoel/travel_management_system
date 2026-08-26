document.addEventListener('DOMContentLoaded', () => {
  const nameInput = document.querySelector('[data-partner-filter="name"]');
  const typeInput = document.querySelector('[data-partner-filter="type"]');
  const rows = Array.from(document.querySelectorAll('[data-partner-row]'));
  const deleteButtons = document.querySelectorAll('[data-partner-delete]');
  const previewUrls = new WeakMap();

  const filterRows = () => {
    const nameQuery = (nameInput?.value || '').trim().toLowerCase();
    const typeQuery = (typeInput?.value || '').trim().toLowerCase();

    rows.forEach((row) => {
      const partnerName = row.dataset.partnerName || '';
      const partnerType = row.dataset.partnerType || '';
      const isVisible = partnerName.includes(nameQuery) && partnerType.includes(typeQuery);

      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };

  nameInput?.addEventListener('input', filterRows);
  typeInput?.addEventListener('input', filterRows);

  deleteButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
      const partnerName = button.dataset.partnerDelete || 'this partner';
      const confirmed = window.confirm(`Are you sure you want to remove ${partnerName} from the partner list?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });

  document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || !input.matches('[data-partner-cover-input]')) {
      return;
    }

    const previewUrl = previewUrls.get(input);

    if (previewUrl) {
      URL.revokeObjectURL(previewUrl);
      previewUrls.delete(input);
    }

    const previewKey = input.dataset.partnerCoverInput;
    const preview = document.querySelector(`[data-partner-cover-preview="${previewKey}"]`);
    const wrapper = document.querySelector(`[data-partner-cover-preview-wrapper="${previewKey}"]`);
    const file = input.files?.[0];

    if (!file || !preview || !wrapper || !file.type.startsWith('image/')) {
      return;
    }

    const nextPreviewUrl = URL.createObjectURL(file);
    previewUrls.set(input, nextPreviewUrl);
    preview.src = nextPreviewUrl;
    wrapper.style.display = '';
  });
});
