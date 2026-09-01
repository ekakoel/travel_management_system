/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./resources/backend/js/operations/transports/index.js ***!
  \*************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var filters = {
    name: '',
    type: ''
  };
  var applyFilters = function applyFilters() {
    document.querySelectorAll('[data-transport-row]').forEach(function (row) {
      var matchesName = (row.dataset.transportName || '').includes(filters.name);
      var matchesType = (row.dataset.transportType || '').includes(filters.type);
      row.classList.toggle('is-filtered-out', !(matchesName && matchesType));
    });
  };
  document.querySelectorAll('[data-transport-filter]').forEach(function (input) {
    input.addEventListener('input', function () {
      filters[input.dataset.transportFilter] = input.value.trim().toLowerCase();
      applyFilters();
    });
  });
  document.querySelectorAll('[data-transport-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var transportName = button.dataset.transportDelete || 'this transportation package';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(transportName, "?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;