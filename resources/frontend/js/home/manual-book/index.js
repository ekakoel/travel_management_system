(() => {
    const page = document.querySelector('[data-manual-book-page]');

    if (!page) {
        return;
    }

    const searchInput = page.querySelector('[data-manual-search]');
    const languageSelect = page.querySelector('[data-manual-language]');
    const items = Array.from(page.querySelectorAll('[data-manual-item]'));
    const emptyState = page.querySelector('[data-manual-empty]');

    const normalize = (value) => (value || '').toString().trim().toLowerCase();

    const filterManuals = () => {
        const query = normalize(searchInput?.value);
        const language = languageSelect?.value || 'all';
        let visibleCount = 0;

        items.forEach((item) => {
            const searchValue = item.dataset.manualSearchValue || '';
            const languageValue = item.dataset.manualLanguageValue || '';
            const matchesSearch = query === '' || searchValue.includes(query);
            const matchesLanguage = language === 'all' || languageValue === language;
            const isVisible = matchesSearch && matchesLanguage;

            item.classList.toggle('is-hidden', !isVisible);

            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount > 0);
        }
    };

    searchInput?.addEventListener('input', filterManuals);
    languageSelect?.addEventListener('change', filterManuals);
})();
