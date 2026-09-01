/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./resources/backend/js/operations/activities/forms.js ***!
  \*************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var previewUrls = new WeakMap();
  var parsePricingNumber = function parsePricingNumber(value) {
    var normalized = String(value || '').replace(/[^\d,.-]/g, '').replace(/\.(?=\d{3}(\D|$))/g, '').replace(',', '.');
    var parsed = Number.parseFloat(normalized);
    return Number.isFinite(parsed) ? parsed : 0;
  };
  var formatUsd = function formatUsd(value) {
    return "$".concat(value.toLocaleString('id-ID', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }));
  };
  var formatIdr = function formatIdr(value) {
    return "Rp".concat(Math.round(value).toLocaleString('id-ID'));
  };
  var initializePricingPreview = function initializePricingPreview() {
    var preview = document.querySelector('[data-activity-pricing-preview]');
    if (!preview) {
      return;
    }
    var contractRateInput = document.querySelector('#contract_rate');
    var markupInput = document.querySelector('#markup');
    var usdTarget = preview.querySelector('[data-activity-pricing-preview-usd]');
    var idrTarget = preview.querySelector('[data-activity-pricing-preview-idr]');
    var messageTarget = preview.querySelector('[data-activity-pricing-preview-message]');
    var rate = parsePricingNumber(preview.dataset.activityPricingPreviewRate);
    var tax = parsePricingNumber(preview.dataset.activityPricingPreviewTax);
    var unavailableMessage = preview.dataset.activityPricingPreviewUnavailable || 'Price cannot be calculated.';
    if (!contractRateInput || !markupInput || !usdTarget || !idrTarget || !messageTarget) {
      return;
    }
    var updatePreview = function updatePreview() {
      var contractRateIdr = parsePricingNumber(contractRateInput.value);
      var markupUsd = parsePricingNumber(markupInput.value);
      if (contractRateIdr <= 0 || rate <= 0 || !Number.isFinite(tax)) {
        usdTarget.textContent = '-';
        idrTarget.textContent = '-';
        messageTarget.textContent = unavailableMessage;
        return;
      }
      var subtotalUsd = contractRateIdr / rate + markupUsd;
      var sellingPriceUsd = Math.ceil(subtotalUsd + subtotalUsd * tax / 100);
      usdTarget.textContent = formatUsd(sellingPriceUsd);
      idrTarget.textContent = formatIdr(sellingPriceUsd * rate);
      messageTarget.textContent = 'Live preview only. Final price is recalculated by the server when saved.';
    };
    contractRateInput.addEventListener('input', updatePreview);
    markupInput.addEventListener('input', updatePreview);
    contractRateInput.addEventListener('change', updatePreview);
    markupInput.addEventListener('change', updatePreview);
    updatePreview();
  };
  var revokePreviewUrls = function revokePreviewUrls(input) {
    var urls = previewUrls.get(input) || [];
    urls.forEach(function (url) {
      return URL.revokeObjectURL(url);
    });
    previewUrls["delete"](input);
  };
  document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-activity-gallery-delete]');
    if (!button) {
      return;
    }
    var activityName = button.dataset.activityGalleryDelete || 'this activity';
    var confirmed = window.confirm("Delete this gallery image from ".concat(activityName, "?"));
    if (!confirmed) {
      event.preventDefault();
    }
  });
  document.addEventListener('change', function (event) {
    var input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.matches('[data-activity-file-input]')) {
      return;
    }
    var target = document.querySelector(input.dataset.activityFileInputTarget || '');
    if (!target) {
      return;
    }
    var files = Array.from(input.files || []);
    var isCoverInput = input.hasAttribute('data-activity-cover-input');
    var preview = isCoverInput ? document.querySelector(input.dataset.activityCoverPreviewTarget || '[data-activity-cover-preview]') : null;
    var imageFile = files[0] || null;
    revokePreviewUrls(input);
    target.textContent = files.length > 0 ? "".concat(files.length, " file").concat(files.length === 1 ? '' : 's', " selected") : target.dataset.activityFileInputDefault || 'No file selected';
    var galleryPreview = input.dataset.activityGalleryPreviewTarget ? document.querySelector(input.dataset.activityGalleryPreviewTarget) : null;
    if (galleryPreview) {
      galleryPreview.replaceChildren();
      var imageFiles = files.filter(function (file) {
        return file.type && file.type.startsWith('image/');
      });
      var nextPreviewUrls = [];
      if (imageFiles.length === 0) {
        var empty = document.createElement('div');
        empty.className = 'activity-gallery-preview__empty';
        empty.innerHTML = '<i class="fa fa-images"></i><span>No selected images to preview.</span>';
        galleryPreview.appendChild(empty);
      } else {
        imageFiles.forEach(function (file) {
          var previewUrl = URL.createObjectURL(file);
          nextPreviewUrls.push(previewUrl);
          var item = document.createElement('div');
          item.className = 'activity-gallery-preview__item';
          var image = document.createElement('img');
          image.src = previewUrl;
          image.alt = file.name;
          item.appendChild(image);
          galleryPreview.appendChild(item);
        });
      }
      previewUrls.set(input, nextPreviewUrls);
    }
    if (!isCoverInput || !preview || !imageFile) {
      return;
    }
    if (!imageFile.type || !imageFile.type.startsWith('image/')) {
      target.textContent = 'Selected file is not a valid image';
      return;
    }
    var nextPreviewUrl = URL.createObjectURL(imageFile);
    previewUrls.set(input, [nextPreviewUrl]);
    var image = document.createElement('img');
    image.src = nextPreviewUrl;
    image.alt = imageFile.name;
    preview.replaceChildren(image);
  });
  initializePricingPreview();
});
/******/ })()
;