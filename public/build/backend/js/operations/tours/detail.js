/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/backend/js/operations/tours/detail.js ***!
  \*********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var _window$jQuery, _window$jQuery$fn;
  var page = document.querySelector('.tour-detail-page');
  var priceFormContext = page === null || page === void 0 ? void 0 : page.dataset.tourPriceFormContext;
  var updateMarkupInput = function updateMarkupInput(typeSelect) {
    var form = typeSelect.closest('form');
    var amountInput = form === null || form === void 0 ? void 0 : form.querySelector('[data-tour-markup-amount]');
    var label = form === null || form === void 0 ? void 0 : form.querySelector('[data-tour-markup-label]');
    var help = form === null || form === void 0 ? void 0 : form.querySelector('[data-tour-markup-help]');
    if (!amountInput || !label || !help) {
      return;
    }
    var config = {
      percentage: {
        label: 'Markup Percentage *',
        help: 'Percentage of the contract rate per pax (maximum 100%).',
        placeholder: '10.00',
        step: '0.01'
      },
      usd: {
        label: 'Markup USD *',
        help: 'USD amount per pax; maximum two decimal places.',
        placeholder: '20.00',
        step: '0.01'
      },
      idr: {
        label: 'Markup IDR *',
        help: 'Whole rupiah amount per pax.',
        placeholder: '250000',
        step: '1'
      }
    }[typeSelect.value] || null;
    if (!config) {
      return;
    }
    label.textContent = config.label;
    help.textContent = config.help;
    amountInput.placeholder = config.placeholder;
    amountInput.step = config.step;
  };
  document.querySelectorAll('[data-tour-markup-type]').forEach(function (typeSelect) {
    updateMarkupInput(typeSelect);
    typeSelect.addEventListener('change', function () {
      return updateMarkupInput(typeSelect);
    });
  });
  if (priceFormContext && (_window$jQuery = window.jQuery) !== null && _window$jQuery !== void 0 && (_window$jQuery$fn = _window$jQuery.fn) !== null && _window$jQuery$fn !== void 0 && _window$jQuery$fn.modal) {
    var updateContext = /^update:(\d+)$/.exec(priceFormContext);
    var modalSelector = priceFormContext === 'create' ? '#add-price' : updateContext ? "#update-price-".concat(updateContext[1]) : null;
    if (modalSelector && document.querySelector(modalSelector)) {
      window.jQuery(modalSelector).modal('show');
    }
  }
  document.querySelectorAll('[data-tour-price-delete]').forEach(function (button) {
    button.addEventListener('click', function (event) {
      var capacity = button.dataset.tourPriceDelete || 'this price row';
      var confirmed = window.confirm("Are you sure you want to delete ".concat(capacity, "?"));
      if (!confirmed) {
        event.preventDefault();
      }
    });
  });
});
/******/ })()
;