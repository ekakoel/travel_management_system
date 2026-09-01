/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./resources/backend/js/operations/transports/forms.js ***!
  \*************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-transport-form]').forEach(function (form) {
    form.dataset.transportFormReady = 'true';
  });
  document.querySelectorAll('[data-transport-cover-input]').forEach(function (input) {
    input.addEventListener('change', function () {
      var file = input.files && input.files[0] ? input.files[0] : null;
      var preview = document.querySelector('[data-transport-cover-preview]');
      var status = document.querySelector('[data-transport-cover-status]');
      if (status) {
        status.textContent = file ? file.name : status.dataset.transportFileInputDefault || 'No cover selected';
      }
      if (!file || !preview || !file.type.startsWith('image/')) {
        return;
      }
      var reader = new FileReader();
      reader.addEventListener('load', function () {
        var image = document.createElement('img');
        image.src = reader.result;
        image.alt = 'Selected transport cover preview';
        preview.replaceChildren(image);
      });
      reader.readAsDataURL(file);
    });
  });
  document.querySelectorAll('[data-transport-gallery-input]').forEach(function (input) {
    input.addEventListener('change', function () {
      var files = Array.from(input.files || []);
      var statusSelector = input.dataset.transportFileInputTarget;
      var status = statusSelector ? document.querySelector(statusSelector) : null;
      var preview = document.querySelector('[data-transport-gallery-preview]');
      if (status) {
        var defaultText = status.dataset.transportFileInputDefault || 'No gallery images selected';
        status.textContent = files.length ? "".concat(files.length, " image").concat(files.length === 1 ? '' : 's', " selected") : defaultText;
      }
      if (!preview) {
        return;
      }
      preview.innerHTML = '';
      files.filter(function (file) {
        return file.type.startsWith('image/');
      }).slice(0, 8).forEach(function (file) {
        var reader = new FileReader();
        reader.addEventListener('load', function () {
          var item = document.createElement('figure');
          item.className = 'transport-gallery-preview__item';
          var image = document.createElement('img');
          image.src = reader.result;
          image.alt = file.name;
          var caption = document.createElement('figcaption');
          caption.textContent = file.name;
          item.append(image, caption);
          preview.appendChild(item);
        });
        reader.readAsDataURL(file);
      });
    });
  });
  document.querySelectorAll('[data-transport-gallery-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var transportName = button.dataset.transportGalleryDelete || 'this transport gallery image';
      var confirmed = window.confirm("Are you sure you want to remove a gallery image from ".concat(transportName, "?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;