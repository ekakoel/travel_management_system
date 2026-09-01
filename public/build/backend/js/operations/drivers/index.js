/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./resources/backend/js/operations/drivers/index.js ***!
  \**********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var nameInput = document.querySelector('[data-driver-filter="name"]');
  var licenseInput = document.querySelector('[data-driver-filter="license"]');
  var rows = Array.from(document.querySelectorAll('[data-driver-row]'));
  var deleteButtons = document.querySelectorAll('[data-driver-delete]');
  var filterRows = function filterRows() {
    var nameQuery = ((nameInput === null || nameInput === void 0 ? void 0 : nameInput.value) || '').trim().toLowerCase();
    var licenseQuery = ((licenseInput === null || licenseInput === void 0 ? void 0 : licenseInput.value) || '').trim().toLowerCase();
    rows.forEach(function (row) {
      var driverName = row.dataset.driverName || '';
      var driverLicense = row.dataset.driverLicense || '';
      var isVisible = driverName.includes(nameQuery) && driverLicense.includes(licenseQuery);
      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };
  nameInput === null || nameInput === void 0 ? void 0 : nameInput.addEventListener('input', filterRows);
  licenseInput === null || licenseInput === void 0 ? void 0 : licenseInput.addEventListener('input', filterRows);
  deleteButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var driverName = button.dataset.driverDelete || 'this driver';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(driverName, " from the driver list?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;