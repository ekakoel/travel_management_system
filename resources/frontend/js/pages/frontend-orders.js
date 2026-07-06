document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('ordersSearchInput');
    var filterButtons = Array.from(document.querySelectorAll('[data-order-filter]'));
    var sections = Array.from(document.querySelectorAll('[data-order-section]'));

    if (!sections.length) {
        return;
    }

    function applyOrdersFilter() {
        var query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        var activeFilter = document.querySelector('[data-order-filter].is-active');
        var activeScope = activeFilter ? activeFilter.getAttribute('data-order-filter') : 'all';

        sections.forEach(function (section) {
            var sectionScope = section.getAttribute('data-order-section');
            var cards = Array.from(section.querySelectorAll('[data-order-card]'));
            var defaultEmpty = section.querySelector('[data-empty-default]');
            var searchEmpty = section.querySelector('[data-empty-search]');
            var visibleCards = 0;
            var sectionAllowed = activeScope === 'all' || activeScope === sectionScope;

            cards.forEach(function (card) {
                var haystack = card.getAttribute('data-order-search') || '';
                var matchesSearch = query === '' || haystack.indexOf(query) !== -1;
                var shouldShow = sectionAllowed && matchesSearch;

                card.classList.toggle('d-none', !shouldShow);
                if (shouldShow) {
                    visibleCards += 1;
                }
            });

            section.classList.toggle('d-none', !sectionAllowed);

            if (!sectionAllowed) {
                return;
            }

            if (defaultEmpty) {
                defaultEmpty.classList.toggle('d-none', query !== '' || cards.length > 0);
            }

            if (searchEmpty) {
                searchEmpty.classList.toggle('d-none', query === '' || visibleCards > 0);
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyOrdersFilter);
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            filterButtons.forEach(function (item) {
                item.classList.remove('is-active');
            });
            button.classList.add('is-active');
            applyOrdersFilter();
        });
    });

    applyOrdersFilter();
});
