/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/backend/js/operations/guides/index.js ***!
  \*********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var nameInput = document.querySelector('[data-guide-filter="name"]');
  var languageInput = document.querySelector('[data-guide-filter="language"]');
  var rows = Array.from(document.querySelectorAll('[data-guide-row]'));
  var deleteButtons = document.querySelectorAll('[data-guide-delete]');
  var filterRows = function filterRows() {
    var nameQuery = ((nameInput === null || nameInput === void 0 ? void 0 : nameInput.value) || '').trim().toLowerCase();
    var languageQuery = ((languageInput === null || languageInput === void 0 ? void 0 : languageInput.value) || '').trim().toLowerCase();
    rows.forEach(function (row) {
      var guideName = row.dataset.guideName || '';
      var guideLanguage = row.dataset.guideLanguage || '';
      var isVisible = guideName.includes(nameQuery) && guideLanguage.includes(languageQuery);
      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };
  nameInput === null || nameInput === void 0 ? void 0 : nameInput.addEventListener('input', filterRows);
  languageInput === null || languageInput === void 0 ? void 0 : languageInput.addEventListener('input', filterRows);
  deleteButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var guideName = button.dataset.guideDelete || 'this guide';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(guideName, " from the guide list?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;