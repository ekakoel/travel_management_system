document.addEventListener('DOMContentLoaded', () => {
  const filterControls = Array.from(document.querySelectorAll('[data-hotel-detail-filter]'));
  const deleteButtons = document.querySelectorAll('[data-hotel-detail-delete]');

  const filterRows = (input) => {
    const target = input.dataset.hotelDetailFilter;
    const query = input.value.trim().toLowerCase();
    const rows = document.querySelectorAll(`[data-hotel-detail-row="${target}"]`);

    rows.forEach((row) => {
      const haystack = row.dataset.hotelDetailSearch || '';
      const isVisible = haystack.includes(query);

      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };

  filterControls.forEach((input) => {
    input.addEventListener('input', () => filterRows(input));
  });

  deleteButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
      const label = button.dataset.hotelDetailDelete || 'this item';
      const confirmed = window.confirm(`Are you sure you want to remove ${label}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
