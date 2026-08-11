(function () {
    'use strict';

    var page = document.querySelector('[data-invoice-index]');

    if (!page) {
        return;
    }

    var searchInput = page.querySelector('[data-invoice-filter="search"]');
    var stateInput = page.querySelector('[data-invoice-filter="state"]');
    var currencyInput = page.querySelector('[data-invoice-filter="currency"]');
    var dueInput = page.querySelector('[data-invoice-filter="due"]');
    var resetButton = page.querySelector('[data-invoice-filter-reset]');
    var rows = Array.prototype.slice.call(page.querySelectorAll('[data-invoice-row]'));
    var emptyState = page.querySelector('[data-invoice-filter-empty]');
    var visibleCount = page.querySelector('[data-invoice-visible-count]');

    function applyFilters() {
        var search = (searchInput ? searchInput.value : '').trim().toLowerCase();
        var state = stateInput ? stateInput.value : '';
        var currency = currencyInput ? currencyInput.value : '';
        var due = dueInput ? dueInput.value : '';
        var visibleCards = 0;

        rows.forEach(function (row) {
            var matches = (row.dataset.invoiceSearch || '').includes(search)
                && (!state || row.dataset.invoiceState === state)
                && (!currency || row.dataset.invoiceCurrency === currency)
                && (!due || row.dataset.invoiceDue === due);

            row.hidden = !matches;

            if (matches && row.matches('article')) {
                visibleCards += 1;
            }
        });

        if (visibleCount) {
            visibleCount.textContent = String(visibleCards);
        }

        if (emptyState) {
            emptyState.hidden = visibleCards > 0 || rows.length === 0;
        }
    }

    [searchInput, stateInput, currencyInput, dueInput].forEach(function (control) {
        if (control) {
            control.addEventListener(control.tagName === 'SELECT' ? 'change' : 'input', applyFilters);
        }
    });

    if (resetButton) {
        resetButton.addEventListener('click', function () {
            [searchInput, stateInput, currencyInput, dueInput].forEach(function (control) {
                if (control) {
                    control.value = '';
                }
            });
            applyFilters();
            if (searchInput) {
                searchInput.focus();
            }
        });
    }
}());
