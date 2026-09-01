/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./resources/backend/js/operations/activities/index.js ***!
  \*************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-activity-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var activityName = button.dataset.activityDelete || 'this activity';
      var confirmed = window.confirm("Are you sure you want to remove ".concat(activityName, " from the activity list?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;