document.addEventListener('DOMContentLoaded', () => {
  const filters = {
    type: '',
    duration: '',
  };

  const applyFilters = () => {
    document.querySelectorAll('[data-transport-price-row]').forEach((row) => {
      const matchesType = (row.dataset.transportPriceType || '').includes(filters.type);
      const matchesDuration = (row.dataset.transportPriceDuration || '').includes(filters.duration);

      row.classList.toggle('is-filtered-out', !(matchesType && matchesDuration));
    });
  };

  document.querySelectorAll('[data-transport-price-filter]').forEach((input) => {
    input.addEventListener('input', () => {
      filters[input.dataset.transportPriceFilter] = input.value.trim().toLowerCase();
      applyFilters();
    });
  });

  document.querySelectorAll('[data-transport-price-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const priceName = button.dataset.transportPriceDelete || 'this transport price';
      const confirmed = window.confirm(`Are you sure you want to remove ${priceName}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
