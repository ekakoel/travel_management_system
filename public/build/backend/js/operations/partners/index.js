/******/ (() => { // webpackBootstrap
/*!***********************************************************!*\
  !*** ./resources/backend/js/operations/partners/index.js ***!
  \***********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var nameInput = document.querySelector('[data-partner-filter="name"]');
  var typeInput = document.querySelector('[data-partner-filter="type"]');
  var rows = Array.from(document.querySelectorAll('[data-partner-row]'));
  var deleteButtons = document.querySelectorAll('[data-partner-delete]');
  var previewUrls = new WeakMap();
  var filterRows = function filterRows() {
    var nameQuery = ((nameInput === null || nameInput === void 0 ? void 0 : nameInput.value) || '').trim().toLowerCase();
    var typeQuery = ((typeInput === null || typeInput === void 0 ? void 0 : typeInput.value) || '').trim().toLowerCase();
    rows.forEach(function (row) {
      var partnerName = row.dataset.partnerName || '';
      var partnerType = row.dataset.partnerType || '';
      var isVisible = partnerName.includes(nameQuery) && partnerType.includes(typeQuery);
      row.hidden = !isVisible;
      row.classList.toggle('is-filtered-out', !isVisible);
    });
  };
  nameInput === null || nameInput === void 0 ? void 0 : nameInput.addEventListener('input', filterRows);
  typeInput === null || typeInput === void 0 ? void 0 : typeInput.addEventListener('input', filterRows);
  deleteButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      var partnerName = button.dataset.partnerDelete || 'this partner';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(partnerName, " from the partner list?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
  document.addEventListener('change', function (event) {
    var _input$files;
    var input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.matches('[data-partner-cover-input]')) {
      return;
    }
    var previewUrl = previewUrls.get(input);
    if (previewUrl) {
      URL.revokeObjectURL(previewUrl);
      previewUrls["delete"](input);
    }
    var previewKey = input.dataset.partnerCoverInput;
    var preview = document.querySelector("[data-partner-cover-preview=\"".concat(previewKey, "\"]"));
    var wrapper = document.querySelector("[data-partner-cover-preview-wrapper=\"".concat(previewKey, "\"]"));
    var file = (_input$files = input.files) === null || _input$files === void 0 ? void 0 : _input$files[0];
    if (!file || !preview || !wrapper || !file.type.startsWith('image/')) {
      return;
    }
    var nextPreviewUrl = URL.createObjectURL(file);
    previewUrls.set(input, nextPreviewUrl);
    preview.src = nextPreviewUrl;
    wrapper.style.display = '';
  });
});
/******/ })()
;