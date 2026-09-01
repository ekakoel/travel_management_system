/******/ (() => { // webpackBootstrap
/*!************************************************************!*\
  !*** ./resources/backend/js/admin/company-profile/edit.js ***!
  \************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-company-logo-input]').forEach(function (input) {
    input.addEventListener('change', function () {
      var preview = document.querySelector('[data-company-logo-preview="' + input.dataset.companyLogoInput + '"]');
      var file = input.files && input.files[0];
      if (!preview || !file || !file.type.match(/^image\//)) {
        return;
      }
      var imageUrl = URL.createObjectURL(file);
      preview.innerHTML = '';
      var image = document.createElement('img');
      image.src = imageUrl;
      image.alt = 'Selected logo preview';
      image.onload = function () {
        URL.revokeObjectURL(imageUrl);
      };
      preview.appendChild(image);
    });
  });
});
/******/ })()
;