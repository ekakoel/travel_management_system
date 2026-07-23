document.addEventListener('DOMContentLoaded', () => {
  const nameInput = document.querySelector('[data-hotel-filter="name"]');
  const locationInput = document.querySelector('[data-hotel-filter="location"]');
  const rows = Array.from(document.querySelectorAll('[data-hotel-row]'));
  const deleteButtons = document.querySelectorAll('[data-hotel-delete]');

  const filterRows = () => {
    const nameQuery = (nameInput?.value || '').trim().toLowerCase();
    const locationQuery = (locationInput?.value || '').trim().toLowerCase();

    rows.forEach((row) => {
      const hotelName = row.dataset.hotelName || '';
      const hotelLocation = row.dataset.hotelLocation || '';
      const isVisible = hotelName.includes(nameQuery) && hotelLocation.includes(locationQuery);

      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };

  nameInput?.addEventListener('input', filterRows);
  locationInput?.addEventListener('input', filterRows);

  deleteButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
      const hotelName = button.dataset.hotelDelete || 'this hotel';
      const confirmed = window.confirm(`Are you sure you want to remove ${hotelName} from the hotel list?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
