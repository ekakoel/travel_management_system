document.addEventListener('DOMContentLoaded', function () {
    var page = document.querySelector('[data-transport-detail-page]');

    if (!page) {
        return;
    }

    var rates = [];
    var form = page.querySelector('[data-transport-reservation-form]');
    var typeSelect = page.querySelector('[data-transport-price-type]');
    var destinationGroup = page.querySelector('[data-transport-destination-group]');
    var destinationSelect = page.querySelector('[data-transport-price-destination]');
    var priceTarget = page.querySelector('[data-selected-rate-price]');
    var routeTarget = page.querySelector('[data-selected-rate-route]');
    var durationTarget = page.querySelector('[data-selected-rate-duration]');
    var cards = Array.from(page.querySelectorAll('[data-rate-card]'));
    var groups = Array.from(page.querySelectorAll('[data-rate-group]'));

    try {
        rates = JSON.parse(page.getAttribute('data-transport-rates') || '[]');
    } catch (error) {
        rates = [];
    }

    if (!rates.length || !form || !typeSelect) {
        return;
    }

    function getRatesByType(type) {
        return rates.filter(function (rate) {
            return rate.type === type;
        });
    }

    function getSelectedDestination() {
        return destinationSelect ? destinationSelect.value : '';
    }

    function setVisibleRateCards(type, destination) {
        cards.forEach(function (card) {
            var matchesType = card.getAttribute('data-rate-type') === type;
            var matchesDestination = type !== 'Airport Shuttle' || card.getAttribute('data-rate-dst') === destination;

            card.hidden = !(matchesType && matchesDestination);
        });

        groups.forEach(function (group) {
            var visibleCards = Array.from(group.querySelectorAll('[data-rate-card]')).filter(function (card) {
                return !card.hidden;
            });

            group.hidden = group.getAttribute('data-rate-group-type') !== type || visibleCards.length === 0;
        });
    }

    function getFirstRateByTypeAndDestination(type, destination) {
        return rates.find(function (rate) {
            return rate.type === type && (type !== 'Airport Shuttle' || rate.dst === destination);
        });
    }

    function setDestinationOptions(type, selectedDestination) {
        if (!destinationSelect || !destinationGroup) {
            return;
        }

        var typeRates = getRatesByType(type);
        var shouldShowDestination = type === 'Airport Shuttle';
        var destinations = [];

        destinationGroup.hidden = !shouldShowDestination;
        destinationSelect.innerHTML = '';

        typeRates.forEach(function (rate) {
            if (destinations.indexOf(rate.dst) === -1) {
                destinations.push(rate.dst);
            }
        });

        destinations.forEach(function (destination) {
            var option = document.createElement('option');

            option.value = destination;
            option.textContent = destination;

            if (destination === selectedDestination) {
                option.selected = true;
            }

            destinationSelect.appendChild(option);
        });
    }

    function setSelectedRate(rate) {
        if (!rate) {
            return;
        }

        var selectedDestination = rate.type === 'Airport Shuttle' ? rate.dst : '';

        form.setAttribute('action', rate.action);

        if (priceTarget) {
            priceTarget.textContent = rate.price;
        }

        if (routeTarget) {
            routeTarget.textContent = rate.route;
        }

        if (durationTarget) {
            durationTarget.textContent = rate.duration;
        }

        cards.forEach(function (card) {
            card.classList.toggle('is-selected', String(card.getAttribute('data-rate-id')) === String(rate.id));
        });

        if (typeSelect.value !== rate.type) {
            typeSelect.value = rate.type;
        }

        setDestinationOptions(rate.type, selectedDestination);
        setVisibleRateCards(rate.type, selectedDestination);
    }

    function selectFirstRateByType(type) {
        setDestinationOptions(type, '');

        var destination = type === 'Airport Shuttle' ? getSelectedDestination() : '';

        setSelectedRate(getFirstRateByTypeAndDestination(type, destination) || getRatesByType(type)[0]);
    }

    typeSelect.addEventListener('change', function () {
        selectFirstRateByType(typeSelect.value);
    });

    if (destinationSelect) {
        destinationSelect.addEventListener('change', function () {
            var selectedRate = getFirstRateByTypeAndDestination(typeSelect.value, destinationSelect.value);

            setSelectedRate(selectedRate);
        });
    }

    page.addEventListener('click', function (event) {
        var button = event.target.closest('[data-select-transport-rate]');

        if (!button) {
            return;
        }

        var selectedRate = rates.find(function (rate) {
            return String(rate.id) === String(button.getAttribute('data-select-transport-rate'));
        });

        setSelectedRate(selectedRate);

        if (form.scrollIntoView) {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    setSelectedRate(rates[0]);
});
