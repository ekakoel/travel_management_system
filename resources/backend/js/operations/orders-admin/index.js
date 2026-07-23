document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-orders-admin]');

    if (!page) {
        return;
    }

    const search = page.querySelector('[data-orders-search]');
    const rows = Array.from(page.querySelectorAll('[data-order-row]'));
    const sections = Array.from(page.querySelectorAll('[data-orders-section]'));

    let searchFrame = null;
    const rowSearchIndex = new Map(rows.map((row) => [row, row.dataset.orderSearch || '']));

    const syncSectionVisibility = () => {
        sections.forEach((section) => {
            const visibleRows = section.querySelectorAll('[data-order-row]:not([hidden])');
            section.hidden = visibleRows.length === 0;
        });
    };

    const filterOrders = () => {
        const keyword = search.value.trim().toLowerCase();

        rows.forEach((row) => {
            row.hidden = keyword !== '' && !rowSearchIndex.get(row).includes(keyword);
        });

        syncSectionVisibility();
    };

    search?.addEventListener('input', () => {
        if (searchFrame) {
            window.cancelAnimationFrame(searchFrame);
        }

        searchFrame = window.requestAnimationFrame(filterOrders);
    });
});
