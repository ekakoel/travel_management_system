/******/ (() => { // webpackBootstrap
/*!******************************************************!*\
  !*** ./resources/backend/js/admin/currency/index.js ***!
  \******************************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-confirm-delete]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      var message = form.getAttribute('data-confirm-delete') || 'Delete this record?';
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;