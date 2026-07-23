document.addEventListener('DOMContentLoaded', () => {
  const nameInput = document.querySelector('[data-guide-filter="name"]');
  const languageInput = document.querySelector('[data-guide-filter="language"]');
  const rows = Array.from(document.querySelectorAll('[data-guide-row]'));
  const deleteButtons = document.querySelectorAll('[data-guide-delete]');

  const filterRows = () => {
    const nameQuery = (nameInput?.value || '').trim().toLowerCase();
    const languageQuery = (languageInput?.value || '').trim().toLowerCase();

    rows.forEach((row) => {
      const guideName = row.dataset.guideName || '';
      const guideLanguage = row.dataset.guideLanguage || '';
      const isVisible = guideName.includes(nameQuery) && guideLanguage.includes(languageQuery);

      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };

  nameInput?.addEventListener('input', filterRows);
  languageInput?.addEventListener('input', filterRows);

  deleteButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
      const guideName = button.dataset.guideDelete || 'this guide';
      const confirmed = window.confirm(`Are you sure you want to remove ${guideName} from the guide list?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
