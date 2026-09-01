/******/ (() => { // webpackBootstrap
/*!******************************************************************!*\
  !*** ./resources/frontend/js/home/booking/hotel-availability.js ***!
  \******************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var detailModal = document.getElementById('hotelRateDetailModal');
  var loadedImageSources = new Set();
  function getImageSource(image) {
    return image.currentSrc || image.getAttribute('src') || image.src || '';
  }
  function bindProgressiveImages(scope) {
    scope.querySelectorAll('.availability-progressive-image').forEach(function (image) {
      var markLoaded = function markLoaded() {
        var imageSource = getImageSource(image);
        if (imageSource) {
          loadedImageSources.add(imageSource);
        }
        image.classList.add('is-loaded');
        if (image.parentElement) {
          image.parentElement.classList.add('is-image-loaded');
        }
      };
      var imageSource = getImageSource(image);
      if (imageSource && loadedImageSources.has(imageSource)) {
        markLoaded();
        return;
      }
      if (image.complete && image.naturalWidth > 0) {
        markLoaded();
        return;
      }
      image.addEventListener('load', markLoaded, {
        once: true
      });
      image.addEventListener('error', markLoaded, {
        once: true
      });
    });
  }
  bindProgressiveImages(document);
  if (!detailModal) {
    return;
  }
  var eyebrowElement = detailModal.querySelector('[data-detail-modal-eyebrow]');
  var titleElement = detailModal.querySelector('[data-detail-modal-title]');
  var iconElement = detailModal.querySelector('[data-detail-modal-icon]');
  var bodyElement = detailModal.querySelector('[data-detail-modal-body]');
  document.querySelectorAll('[data-detail-trigger="hotel-rate-detail"]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var sourceSelector = trigger.getAttribute('data-detail-source');
      var sourceTemplate = sourceSelector ? document.querySelector(sourceSelector) : null;
      if (!sourceTemplate) {
        bodyElement.innerHTML = '';
        bodyElement.removeAttribute('data-current-detail-source');
        return;
      }
      var sourceContent = sourceTemplate.content ? sourceTemplate.content.firstElementChild : null;
      if (!sourceContent) {
        bodyElement.innerHTML = '';
        bodyElement.removeAttribute('data-current-detail-source');
        return;
      }
      var eyebrow = sourceContent.getAttribute('data-detail-eyebrow') || '';
      var title = sourceContent.getAttribute('data-detail-title') || '';
      var icon = sourceContent.getAttribute('data-detail-icon') || 'fa-check-circle-o';
      titleElement.textContent = title;
      iconElement.className = 'fa ' + icon;
      if (bodyElement.getAttribute('data-current-detail-source') !== sourceSelector) {
        bodyElement.innerHTML = sourceContent.innerHTML;
        bodyElement.setAttribute('data-current-detail-source', sourceSelector);
        bindProgressiveImages(bodyElement);
      }
      if (eyebrow) {
        eyebrowElement.textContent = eyebrow;
        eyebrowElement.classList.remove('d-none');
      } else {
        eyebrowElement.textContent = '';
        eyebrowElement.classList.add('d-none');
      }
    });
  });
});
/******/ })()
;