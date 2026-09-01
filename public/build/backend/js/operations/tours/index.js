/******/ (() => { // webpackBootstrap
/*!********************************************************!*\
  !*** ./resources/backend/js/operations/tours/index.js ***!
  \********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var filters = {
    name: '',
    code: ''
  };
  var applyFilters = function applyFilters() {
    document.querySelectorAll('[data-tour-row]').forEach(function (row) {
      var matchesName = (row.dataset.tourName || '').includes(filters.name);
      var matchesCode = (row.dataset.tourCode || '').includes(filters.code);
      row.classList.toggle('is-filtered-out', !(matchesName && matchesCode));
    });
  };
  document.querySelectorAll('[data-tour-filter]').forEach(function (input) {
    input.addEventListener('input', function () {
      filters[input.dataset.tourFilter] = input.value.trim().toLowerCase();
      applyFilters();
    });
  });
  document.querySelectorAll('[data-tour-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var tourName = button.dataset.tourDelete || 'this tour package';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(tourName, " from the tour package list?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;