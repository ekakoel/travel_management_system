/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/frontend/js/components/form-submission-guard.js":
/*!*******************************************************************!*\
  !*** ./resources/frontend/js/components/form-submission-guard.js ***!
  \*******************************************************************/
/***/ ((module) => {

function getSafeSessionStorage() {
  try {
    return window.sessionStorage;
  } catch (error) {
    return null;
  }
}
function isHistoryRestore(event) {
  var navigation = window.performance && window.performance.getEntriesByType ? window.performance.getEntriesByType('navigation')[0] : null;
  return !!(event && event.persisted) || !!(navigation && navigation.type === 'back_forward');
}
function createFormSubmissionGuard(form, options) {
  var settings = options || {};
  var storage = getSafeSessionStorage();
  var storageKey = settings.storageKey || form.dataset.submissionKey || 'form-submit:' + window.location.pathname + ':' + (form.getAttribute('action') || '');
  function markSubmitted() {
    if (!storage) {
      return;
    }
    storage.setItem(storageKey, String(Date.now()));
  }
  function clearSubmitted() {
    if (!storage) {
      return;
    }
    storage.removeItem(storageKey);
  }
  function wasSubmitted() {
    return !!(storage && storage.getItem(storageKey));
  }
  function bindHistoryRestore(handler) {
    window.addEventListener('pageshow', function (event) {
      if (typeof settings.onPageShow === 'function') {
        settings.onPageShow(event);
      }
      if (!isHistoryRestore(event) || !wasSubmitted()) {
        return;
      }
      clearSubmitted();
      if (typeof handler === 'function') {
        handler(event);
        return;
      }
      if (settings.reloadOnHistoryRestore === false) {
        return;
      }
      window.location.reload();
    });
  }
  return {
    bindHistoryRestore: bindHistoryRestore,
    clearSubmitted: clearSubmitted,
    markSubmitted: markSubmitted,
    storageKey: storageKey,
    wasSubmitted: wasSubmitted
  };
}
module.exports = {
  createFormSubmissionGuard: createFormSubmissionGuard,
  getSafeSessionStorage: getSafeSessionStorage,
  isHistoryRestore: isHistoryRestore
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!***************************************************!*\
  !*** ./resources/frontend/js/home/orders/edit.js ***!
  \***************************************************/
var _require = __webpack_require__(/*! ../../components/form-submission-guard */ "./resources/frontend/js/components/form-submission-guard.js"),
  createFormSubmissionGuard = _require.createFormSubmissionGuard;
(function () {
  'use strict';

  function formatUsd(value) {
    return '$' + Number(value || 0).toLocaleString('de-DE', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('[data-order-edit-form]');
    if (!form) {
      return;
    }
    var rates = [];
    try {
      rates = JSON.parse(form.dataset.rates || '[]');
    } catch (error) {
      rates = [];
    }
    var guestInput = form.querySelector('[data-order-edit-guests]');
    var submitButton = form.querySelector('[data-order-edit-submit]');
    var submitOverlay = form.querySelector('[data-form-submit-overlay]');
    var processingLabel = form.dataset.processingLabel || '';
    var submissionGuard = createFormSubmissionGuard(form, {
      storageKey: form.dataset.submissionKey || 'tour-order-edit:' + window.location.pathname
    });
    var reviewMap = {
      travelDate: document.querySelector('[data-order-edit-summary-date]'),
      guestCount: document.querySelector('[data-order-edit-summary-guests]'),
      pickup: document.querySelector('[data-order-edit-summary-pickup]'),
      dropoff: document.querySelector('[data-order-edit-summary-dropoff]'),
      total: document.querySelector('[data-order-edit-summary-total]')
    };
    var pricePerPax = form.querySelector('[data-order-edit-price-per-pax]');
    var priceGuests = form.querySelector('[data-order-edit-price-guests]');
    var priceTotal = form.querySelector('[data-order-edit-price-total]');
    var priceNote = form.querySelector('[data-order-edit-price-note]');
    var isSubmitting = false;
    function setSubmittingState(submitting) {
      isSubmitting = Boolean(submitting);
      form.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');
      document.documentElement.classList.toggle('tour-submit-locked', isSubmitting);
      document.body.classList.toggle('tour-submit-locked', isSubmitting);
      if (submitOverlay) {
        if (isSubmitting && submitOverlay.parentElement !== document.body) {
          document.body.appendChild(submitOverlay);
        }
        submitOverlay.classList.toggle('hidden', !isSubmitting);
        submitOverlay.setAttribute('aria-hidden', isSubmitting ? 'false' : 'true');
      }
      if (submitButton) {
        submitButton.disabled = isSubmitting;
        submitButton.classList.toggle('is-processing', isSubmitting);
        if (!submitButton.dataset.originalHtml) {
          submitButton.dataset.originalHtml = submitButton.innerHTML;
        }
        submitButton.innerHTML = isSubmitting ? '<span class="frontend-action-spinner" aria-hidden="true"></span><span>' + processingLabel + '</span>' : submitButton.dataset.originalHtml;
      }
    }
    function updateReviewSummary() {
      var travelDateField = form.querySelector('[data-order-edit-field="travelDate"]');
      var guestField = form.querySelector('[data-order-edit-field="guestCount"]');
      var pickupField = form.querySelector('[data-order-edit-field="pickup"]');
      var dropoffField = form.querySelector('[data-order-edit-field="dropoff"]');
      if (reviewMap.travelDate) reviewMap.travelDate.textContent = travelDateField && travelDateField.value ? travelDateField.value : '-';
      if (reviewMap.guestCount) reviewMap.guestCount.textContent = guestField && guestField.value ? guestField.value : '-';
      if (reviewMap.pickup) reviewMap.pickup.textContent = pickupField && pickupField.value ? pickupField.value : '-';
      if (reviewMap.dropoff) reviewMap.dropoff.textContent = dropoffField && dropoffField.value ? dropoffField.value : '-';
    }
    function updatePricePreview() {
      var guestCount = Number(guestInput && guestInput.value ? guestInput.value : 0);
      var matchedRate = rates.find(function (rate) {
        return guestCount >= Number(rate.min_qty) && guestCount <= Number(rate.max_qty);
      });
      if (!matchedRate && guestCount >= 2 && rates.length) {
        matchedRate = rates.slice().sort(function (left, right) {
          return Number(right.max_qty) - Number(left.max_qty);
        })[0];
      }
      if (!matchedRate || guestCount < 2 || guestCount > 200) {
        if (pricePerPax) pricePerPax.textContent = '-';
        if (priceGuests) priceGuests.textContent = guestCount > 0 ? String(guestCount) : '-';
        if (priceTotal) priceTotal.textContent = '-';
        if (reviewMap.total) reviewMap.total.textContent = '-';
        if (priceNote) priceNote.textContent = form.dataset.noRateLabel || '';
        if (submitButton) submitButton.disabled = true;
        return;
      }
      var unitPrice = Number(matchedRate.price || 0);
      var total = unitPrice * guestCount;
      if (pricePerPax) pricePerPax.textContent = formatUsd(unitPrice);
      if (priceGuests) priceGuests.textContent = String(guestCount);
      if (priceTotal) priceTotal.textContent = formatUsd(total);
      if (reviewMap.total) reviewMap.total.textContent = formatUsd(total);
      if (priceNote) priceNote.textContent = '';
      if (submitButton) submitButton.disabled = false;
    }
    form.addEventListener('input', function (event) {
      if (event.target.matches('input, textarea, select')) {
        event.target.classList.remove('is-invalid');
      }
      updateReviewSummary();
      updatePricePreview();
    });
    form.addEventListener('submit', function () {
      if (isSubmitting) {
        return;
      }
      submissionGuard.markSubmitted();
      setSubmittingState(true);
    });
    submissionGuard.bindHistoryRestore(function () {
      setSubmittingState(false);
      window.location.reload();
    });
    updateReviewSummary();
    updatePricePreview();
  });
})();
})();

/******/ })()
;