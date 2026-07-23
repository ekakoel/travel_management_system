document.addEventListener('DOMContentLoaded', () => {
  const nameInput = document.querySelector('[data-driver-filter="name"]');
  const licenseInput = document.querySelector('[data-driver-filter="license"]');
  const rows = Array.from(document.querySelectorAll('[data-driver-row]'));
  const deleteButtons = document.querySelectorAll('[data-driver-delete]');

  const filterRows = () => {
    const nameQuery = (nameInput?.value || '').trim().toLowerCase();
    const licenseQuery = (licenseInput?.value || '').trim().toLowerCase();

    rows.forEach((row) => {
      const driverName = row.dataset.driverName || '';
      const driverLicense = row.dataset.driverLicense || '';
      const isVisible = driverName.includes(nameQuery) && driverLicense.includes(licenseQuery);

      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };

  nameInput?.addEventListener('input', filterRows);
  licenseInput?.addEventListener('input', filterRows);

  deleteButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
      const driverName = button.dataset.driverDelete || 'this driver';
      const confirmed = window.confirm(`Are you sure you want to remove ${driverName} from the driver list?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
