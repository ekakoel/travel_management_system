/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./resources/backend/js/operations/hotels/detail.js ***!
  \**********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var filterControls = Array.from(document.querySelectorAll('[data-hotel-detail-filter]'));
  var deleteButtons = document.querySelectorAll('[data-hotel-detail-delete]');
  var filterRows = function filterRows(input) {
    var target = input.dataset.hotelDetailFilter;
    var query = input.value.trim().toLowerCase();
    var rows = document.querySelectorAll("[data-hotel-detail-row=\"".concat(target, "\"]"));
    rows.forEach(function (row) {
      var haystack = row.dataset.hotelDetailSearch || '';
      var isVisible = haystack.includes(query);
      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };
  filterControls.forEach(function (input) {
    input.addEventListener('input', function () {
      return filterRows(input);
    });
  });
  deleteButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var label = button.dataset.hotelDetailDelete || 'this item';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(label, "?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;