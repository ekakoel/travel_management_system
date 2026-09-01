/******/ (() => { // webpackBootstrap
/*!*********************************************!*\
  !*** ./resources/frontend/js/pages/auth.js ***!
  \*********************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
      var field = document.getElementById(button.getAttribute('data-password-toggle'));
      if (!field) {
        return;
      }
      var isVisible = field.getAttribute('type') === 'text';
      field.setAttribute('type', isVisible ? 'password' : 'text');
      button.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
      button.querySelector('i').className = isVisible ? 'fa fa-eye-slash' : 'fa fa-eye';
    });
  });
  document.querySelectorAll('[data-auth-terms]').forEach(function (checkbox) {
    var target = document.querySelector(checkbox.getAttribute('data-auth-terms'));
    if (!target) {
      return;
    }
    function syncTermsState() {
      target.disabled = !checkbox.checked;
    }
    checkbox.addEventListener('change', syncTermsState);
    syncTermsState();
  });
});
/******/ })()
;