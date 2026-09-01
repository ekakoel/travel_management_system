/******/ (() => { // webpackBootstrap
/*!**************************************************************!*\
  !*** ./resources/backend/js/operations/transports/detail.js ***!
  \**************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var filters = {
    type: '',
    duration: ''
  };
  var applyFilters = function applyFilters() {
    document.querySelectorAll('[data-transport-price-row]').forEach(function (row) {
      var matchesType = (row.dataset.transportPriceType || '').includes(filters.type);
      var matchesDuration = (row.dataset.transportPriceDuration || '').includes(filters.duration);
      row.classList.toggle('is-filtered-out', !(matchesType && matchesDuration));
    });
  };
  document.querySelectorAll('[data-transport-price-filter]').forEach(function (input) {
    input.addEventListener('input', function () {
      filters[input.dataset.transportPriceFilter] = input.value.trim().toLowerCase();
      applyFilters();
    });
  });
  document.querySelectorAll('[data-transport-price-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var priceName = button.dataset.transportPriceDelete || 'this transport price';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(priceName, "?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;