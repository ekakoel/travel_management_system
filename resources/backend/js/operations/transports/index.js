document.addEventListener('DOMContentLoaded', () => {
  const filters = {
    name: '',
    type: '',
  };

  const applyFilters = () => {
    document.querySelectorAll('[data-transport-row]').forEach((row) => {
      const matchesName = (row.dataset.transportName || '').includes(filters.name);
      const matchesType = (row.dataset.transportType || '').includes(filters.type);

      row.classList.toggle('is-filtered-out', !(matchesName && matchesType));
    });
  };

  document.querySelectorAll('[data-transport-filter]').forEach((input) => {
    input.addEventListener('input', () => {
      filters[input.dataset.transportFilter] = input.value.trim().toLowerCase();
      applyFilters();
    });
  });

  document.querySelectorAll('[data-transport-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const transportName = button.dataset.transportDelete || 'this transportation package';
      const confirmed = window.confirm(`Are you sure you want to remove ${transportName}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
