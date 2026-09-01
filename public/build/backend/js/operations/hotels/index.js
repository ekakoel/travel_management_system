/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/backend/js/operations/hotels/index.js ***!
  \*********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var nameInput = document.querySelector('[data-hotel-filter="name"]');
  var locationInput = document.querySelector('[data-hotel-filter="location"]');
  var rows = Array.from(document.querySelectorAll('[data-hotel-row]'));
  var deleteButtons = document.querySelectorAll('[data-hotel-delete]');
  var filterRows = function filterRows() {
    var nameQuery = ((nameInput === null || nameInput === void 0 ? void 0 : nameInput.value) || '').trim().toLowerCase();
    var locationQuery = ((locationInput === null || locationInput === void 0 ? void 0 : locationInput.value) || '').trim().toLowerCase();
    rows.forEach(function (row) {
      var hotelName = row.dataset.hotelName || '';
      var hotelLocation = row.dataset.hotelLocation || '';
      var isVisible = hotelName.includes(nameQuery) && hotelLocation.includes(locationQuery);
      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };
  nameInput === null || nameInput === void 0 ? void 0 : nameInput.addEventListener('input', filterRows);
  locationInput === null || locationInput === void 0 ? void 0 : locationInput.addEventListener('input', filterRows);
  deleteButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var hotelName = button.dataset.hotelDelete || 'this hotel';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(hotelName, " from the hotel list?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;