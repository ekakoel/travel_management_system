/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./resources/frontend/js/pages/transport-booking.js ***!
  \**********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var page = document.querySelector('[data-transport-booking-page]');
  if (!page) {
    return;
  }
  var form = page.querySelector('[data-transport-booking-form]');
  var durationInput = page.querySelector('[data-transport-duration]');
  var shuttleTypeInput = page.querySelector('[data-airport-shuttle-type]');
  var arrivalFields = page.querySelector('[data-airport-arrival-fields]');
  var departureFields = page.querySelector('[data-airport-departure-fields]');
  var finalPriceTarget = page.querySelector('#final_price');
  var normalPriceTarget = page.querySelector('#normal_price');
  var goBackButton = page.querySelector('[data-transport-go-back]');
  var overlay = page.querySelector('[data-form-submit-overlay]');
  var submitButtons = form ? Array.from(form.querySelectorAll('button[type="submit"], [data-submit-button]')) : [];
  var transportPrice = parseFloat(page.getAttribute('data-transport-price') || '0');
  var bookingDiscount = parseFloat(page.getAttribute('data-booking-discount') || '0');
  var promotionDiscount = parseFloat(page.getAttribute('data-promotion-discount') || '0');
  var orderType = page.getAttribute('data-order-type') || '';
  var processingLabel = page.getAttribute('data-processing-label') || 'Processing...';
  var submittedWarning = page.getAttribute('data-submitted-warning') || 'This order has already been submitted.';
  var isSubmitting = false;
  function formatCurrency(amount) {
    return amount.toLocaleString('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }
  function calculateNormalPrice() {
    var duration = durationInput ? parseInt(durationInput.value || '1', 10) : 1;
    if (Number.isNaN(duration) || duration < 1) {
      duration = 1;
    }
    if (orderType === 'Daily Rent') {
      return transportPrice * duration;
    }
    return transportPrice;
  }
  function renderPricing() {
    var normalPrice = calculateNormalPrice();
    var finalPrice = Math.max(0, normalPrice - bookingDiscount - promotionDiscount);
    if (normalPriceTarget) {
      normalPriceTarget.textContent = formatCurrency(normalPrice);
    }
    if (finalPriceTarget) {
      finalPriceTarget.textContent = formatCurrency(finalPrice);
    }
  }
  function toggleAirportFields() {
    if (!shuttleTypeInput || !arrivalFields || !departureFields) {
      return;
    }
    var showArrival = shuttleTypeInput.value !== 'Departure';
    arrivalFields.hidden = !showArrival;
    departureFields.hidden = showArrival;
    arrivalFields.querySelectorAll('input').forEach(function (input) {
      input.required = showArrival && input.name === 'arrival_time';
    });
    departureFields.querySelectorAll('input').forEach(function (input) {
      input.required = !showArrival && input.name === 'departure_time';
    });
  }
  function setSubmittingState(submitting) {
    isSubmitting = submitting;
    if (overlay) {
      overlay.classList.toggle('hidden', !submitting);
      overlay.setAttribute('aria-hidden', submitting ? 'false' : 'true');
    }
    submitButtons.forEach(function (button) {
      var originalHtml = button.getAttribute('data-original-html');
      if (!originalHtml) {
        originalHtml = button.innerHTML;
        button.setAttribute('data-original-html', originalHtml);
      }
      button.disabled = submitting;
      button.classList.toggle('is-processing', submitting);
      button.setAttribute('aria-disabled', submitting ? 'true' : 'false');
      button.innerHTML = submitting ? '<span class="frontend-action-spinner" aria-hidden="true"></span><span>' + processingLabel + '</span>' : originalHtml;
    });
  }
  function getStorageKey() {
    var orderNoInput = form ? form.querySelector('input[name="orderno"]') : null;
    var orderNo = orderNoInput ? orderNoInput.value : '';
    return 'transportBookingSubmitted:' + window.location.pathname + ':' + orderNo;
  }
  function markSubmitted() {
    try {
      window.sessionStorage.setItem(getStorageKey(), String(Date.now()));
    } catch (error) {
      return;
    }
  }
  function wasSubmitted() {
    try {
      return !!window.sessionStorage.getItem(getStorageKey());
    } catch (error) {
      return false;
    }
  }
  function disableResubmissionNotice() {
    if (!form || form.querySelector('[data-booking-submitted-warning]')) {
      return;
    }
    var notice = document.createElement('div');
    notice.className = 'alert alert-warning';
    notice.setAttribute('role', 'alert');
    notice.setAttribute('data-booking-submitted-warning', 'true');
    notice.textContent = submittedWarning;
    form.prepend(notice);
    submitButtons.forEach(function (button) {
      button.disabled = true;
      button.setAttribute('aria-disabled', 'true');
    });
  }
  page.querySelectorAll('[data-booking-datetime]').forEach(function (input) {
    if (window.FrontendPickerSystem) {
      input.dataset.uiPicker = input.dataset.uiPicker || 'datetime';
      input.dataset.uiPickerFormat = input.dataset.uiPickerFormat || 'YYYY-MM-DD HH:mm';
      window.FrontendPickerSystem.initPicker(input);
      return;
    }
    if (typeof window.flatpickr === 'function') {
      var tomorrow = new Date();
      tomorrow.setHours(0, 0, 0, 0);
      tomorrow.setDate(tomorrow.getDate() + 1);
      window.flatpickr(input, {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        minuteIncrement: 5,
        minDate: tomorrow,
        allowInput: true
      });
    }
  });
  renderPricing();
  toggleAirportFields();
  if (durationInput) {
    durationInput.addEventListener('input', renderPricing);
  }
  if (shuttleTypeInput) {
    shuttleTypeInput.addEventListener('change', toggleAirportFields);
  }
  if (goBackButton) {
    goBackButton.addEventListener('click', function () {
      window.history.back();
    });
  }
  if (form) {
    form.addEventListener('submit', function (event) {
      if (isSubmitting) {
        event.preventDefault();
        return;
      }
      isSubmitting = true;
      setSubmittingState(true);
      markSubmitted();
    });
    window.addEventListener('pageshow', function (event) {
      var navigation = window.performance && window.performance.getEntriesByType ? window.performance.getEntriesByType('navigation')[0] : null;
      var isHistoryRestore = !!event.persisted || !!(navigation && navigation.type === 'back_forward');
      if (isHistoryRestore && wasSubmitted()) {
        setSubmittingState(false);
        disableResubmissionNotice();
      }
    });
  }
});
/******/ })()
;