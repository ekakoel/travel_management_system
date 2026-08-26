document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.tour-detail-page');
  const priceFormContext = page?.dataset.tourPriceFormContext;

  const updateMarkupInput = (typeSelect) => {
    const form = typeSelect.closest('form');
    const amountInput = form?.querySelector('[data-tour-markup-amount]');
    const label = form?.querySelector('[data-tour-markup-label]');
    const help = form?.querySelector('[data-tour-markup-help]');

    if (!amountInput || !label || !help) {
      return;
    }

    const config = {
      percentage: {
        label: 'Markup Percentage *',
        help: 'Percentage of the contract rate per pax (maximum 100%).',
        placeholder: '10.00',
        step: '0.01',
      },
      usd: {
        label: 'Markup USD *',
        help: 'USD amount per pax; maximum two decimal places.',
        placeholder: '20.00',
        step: '0.01',
      },
      idr: {
        label: 'Markup IDR *',
        help: 'Whole rupiah amount per pax.',
        placeholder: '250000',
        step: '1',
      },
    }[typeSelect.value] || null;

    if (!config) {
      return;
    }

    label.textContent = config.label;
    help.textContent = config.help;
    amountInput.placeholder = config.placeholder;
    amountInput.step = config.step;
  };

  document.querySelectorAll('[data-tour-markup-type]').forEach((typeSelect) => {
    updateMarkupInput(typeSelect);
    typeSelect.addEventListener('change', () => updateMarkupInput(typeSelect));
  });

  if (priceFormContext && window.jQuery?.fn?.modal) {
    const updateContext = /^update:(\d+)$/.exec(priceFormContext);
    const modalSelector = priceFormContext === 'create'
      ? '#add-price'
      : updateContext
        ? `#update-price-${updateContext[1]}`
        : null;

    if (modalSelector && document.querySelector(modalSelector)) {
      window.jQuery(modalSelector).modal('show');
    }
  }

  document.querySelectorAll('[data-tour-price-delete]').forEach((button) => {
    button.addEventListener('click', (event) => {
      const capacity = button.dataset.tourPriceDelete || 'this price row';
      const confirmed = window.confirm(`Are you sure you want to delete ${capacity}?`);

      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
