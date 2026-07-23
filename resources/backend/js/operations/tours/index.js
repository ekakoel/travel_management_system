document.addEventListener('DOMContentLoaded', () => {
  const filters = {
    name: '',
    code: '',
  };

  const applyFilters = () => {
    document.querySelectorAll('[data-tour-row]').forEach((row) => {
      const matchesName = (row.dataset.tourName || '').includes(filters.name);
      const matchesCode = (row.dataset.tourCode || '').includes(filters.code);

      row.classList.toggle('is-filtered-out', !(matchesName && matchesCode));
    });
  };

  document.querySelectorAll('[data-tour-filter]').forEach((input) => {
    input.addEventListener('input', () => {
      filters[input.dataset.tourFilter] = input.value.trim().toLowerCase();
      applyFilters();
    });
  });

  document.querySelectorAll('[data-tour-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const tourName = button.dataset.tourDelete || 'this tour package';
      const confirmed = window.confirm(`Are you sure you want to remove ${tourName} from the tour package list?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
