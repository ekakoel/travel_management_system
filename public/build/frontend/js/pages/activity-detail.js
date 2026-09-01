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
/*!*****************************************************************!*\
  !*** ./resources/frontend/js/landing-page/activities/detail.js ***!
  \*****************************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return generator._invoke = function (innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; }(innerFn, self, context), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; this._invoke = function (method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); }; } function maybeInvokeDelegate(delegate, context) { var method = delegate.iterator[context.method]; if (undefined === method) { if (context.delegate = null, "throw" === context.method) { if (delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method)) return ContinueSentinel; context.method = "throw", context.arg = new TypeError("The iterator does not provide a 'throw' method"); } return ContinueSentinel; } var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) { if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; } return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, define(Gp, "constructor", GeneratorFunctionPrototype), define(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (object) { var keys = []; for (var key in object) { keys.push(key); } return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) { "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); } }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
var _require = __webpack_require__(/*! ../../components/form-submission-guard */ "./resources/frontend/js/components/form-submission-guard.js"),
  createFormSubmissionGuard = _require.createFormSubmissionGuard;
document.addEventListener('DOMContentLoaded', function () {
  var _orderForm$querySelec, _window$bootstrap;
  var orderForm = document.querySelector('[data-activity-order-form]');
  if (!orderForm) {
    return;
  }
  var modalElement = document.getElementById('activityOrderModal');
  var guestInput = orderForm.querySelector('input[name="number_of_guests"]');
  var travelDateInput = orderForm.querySelector('input[name="travel_date"]');
  var pickupLocationInput = orderForm.querySelector('input[name="pickup_location"]');
  var dropoffLocationInput = orderForm.querySelector('input[name="dropoff_location"]');
  var reviewTargets = _toConsumableArray(orderForm.querySelectorAll('[data-activity-order-review]'));
  var guestManifestTableTarget = orderForm.querySelector('[data-activity-order-review-guest-table]');
  var stepPanels = _toConsumableArray(orderForm.querySelectorAll('[data-activity-order-step]'));
  var stepNavItems = _toConsumableArray(orderForm.querySelectorAll('[data-activity-order-nav]'));
  var previousButtons = _toConsumableArray(orderForm.querySelectorAll('[data-activity-order-prev]'));
  var nextButtons = _toConsumableArray(orderForm.querySelectorAll('[data-activity-order-next]'));
  var submitButton = orderForm.querySelector('[data-activity-order-submit]');
  var submitOverlay = orderForm.querySelector('[data-activity-order-overlay]');
  var termsCheckbox = orderForm.querySelector('input[name="terms_accepted"]');
  var guestError = orderForm.querySelector('[data-activity-guest-error]');
  var guestListTarget = orderForm.querySelector('[data-activity-guest-list]');
  var manualGuestsTarget = orderForm.querySelector('[data-activity-manual-guests]');
  var uploadPanel = orderForm.querySelector('[data-activity-upload-panel]');
  var guestListInput = orderForm.querySelector('[data-activity-guest-list-input]');
  var guestListStatus = orderForm.querySelector('[data-activity-guest-list-status]');
  var guestModeLabelTarget = orderForm.querySelector('[data-activity-guest-mode-label]');
  var addGuestButton = orderForm.querySelector('[data-activity-add-guest]');
  var guestProgressTarget = orderForm.querySelector('[data-activity-guest-progress]');
  var pricePerPaxTarget = orderForm.querySelector('[data-activity-order-price="per_pax"]');
  var guestCountPriceTarget = orderForm.querySelector('[data-activity-order-price="guest_count"]');
  var promotionDiscountTarget = orderForm.querySelector('[data-activity-order-price="promotion_discount"]');
  var finalPriceTargets = _toConsumableArray(orderForm.querySelectorAll('[data-activity-order-price="final_total"]'));
  var priceStatusTarget = orderForm.querySelector('[data-activity-order-price-status]');
  var promotionRow = orderForm.querySelector('[data-activity-order-promotion-row]');
  var quoteUrl = orderForm.dataset.quoteUrl || '';
  var csrfToken = ((_orderForm$querySelec = orderForm.querySelector('input[name="_token"]')) === null || _orderForm$querySelec === void 0 ? void 0 : _orderForm$querySelec.value) || '';
  var capacity = Number(orderForm.dataset.capacity || 0);
  var manualGuestThreshold = Number(orderForm.dataset.manualGuestThreshold || 10);
  var currencyCode = orderForm.dataset.currencyCode || 'USD';
  var locale = (orderForm.dataset.locale || document.documentElement.lang || 'en-US').replace('_', '-');
  var submissionGuard = createFormSubmissionGuard(orderForm, {
    storageKey: "activity-order:".concat(window.location.pathname)
  });
  var guestLabel = orderForm.dataset.guestLabel || 'Guest';
  var paxLabel = orderForm.dataset.paxLabel || 'pax';
  var adultLabel = orderForm.dataset.adultLabel || 'Adult';
  var childLabel = orderForm.dataset.childLabel || 'Child';
  var maleLabel = orderForm.dataset.maleLabel || 'Male';
  var femaleLabel = orderForm.dataset.femaleLabel || 'Female';
  var phoneLabel = orderForm.dataset.phoneLabel || 'Phone';
  var reviewEmptyLabel = orderForm.dataset.reviewEmptyLabel || 'No guest details added yet.';
  var tableNoLabel = orderForm.dataset.tableNoLabel || 'No';
  var tableNameLabel = orderForm.dataset.tableNameLabel || 'Name';
  var tableAgeCategoryLabel = orderForm.dataset.tableAgeCategoryLabel || 'Age Category';
  var tableGenderLabel = orderForm.dataset.tableGenderLabel || 'Gender';
  var tablePhoneNumberLabel = orderForm.dataset.tablePhoneNumberLabel || 'Phone Number';
  var guestProgressLabel = orderForm.dataset.guestProgressLabel || ':count guest details added for this booking of :total pax';
  var guestCountMismatchLabel = orderForm.dataset.guestCountMismatchLabel || 'Please provide at least 1 guest detail and no more than the selected pax.';
  var guestListRequiredLabel = orderForm.dataset.guestListRequiredLabel || 'Please upload a guest list for bookings above 10 pax.';
  var guestModeManualLabel = orderForm.dataset.guestModeManualLabel || 'Manual guest details';
  var guestModeUploadLabel = orderForm.dataset.guestModeUploadLabel || 'Guest list upload';
  var guestListSelectedLabel = orderForm.dataset.guestListSelectedLabel || 'Selected: :file';
  var guestListReadyLabel = orderForm.dataset.guestListReadyLabel || 'Ready for review';
  var fileSizeLabel = orderForm.dataset.fileSizeLabel || 'File size';
  var priceUnavailableLabel = orderForm.dataset.priceUnavailableLabel || 'Activity pricing is not available.';
  var priceLoadingLabel = orderForm.dataset.priceLoadingLabel || 'Processing';
  var guestListFormatsLabel = (guestListStatus === null || guestListStatus === void 0 ? void 0 : guestListStatus.textContent) || '';
  var initialStep = Number(orderForm.dataset.initialStep || 0);
  var currencySymbols = {
    USD: '$',
    IDR: 'Rp',
    TWD: 'NT$',
    CNY: 'CNY '
  };
  var activeStep = 0;
  var isSubmitting = false;
  var guests = [];
  var quoteRequestController = null;
  var quoteRequestTimer = null;
  var quoteReady = false;
  try {
    guests = JSON.parse(orderForm.dataset.initialGuests || '[]').filter(function (guest) {
      return Object.values(guest || {}).some(function (value) {
        return value !== null && value !== '';
      });
    }).map(function (guest) {
      return {
        name: String(guest.name || '').trim(),
        age: String(guest.age || '').trim(),
        sex: String(guest.sex || '').trim(),
        phone: String(guest.phone || '').trim()
      };
    });
  } catch (error) {
    guests = [];
  }
  var escapeHtml = function escapeHtml(value) {
    return String(value !== null && value !== void 0 ? value : '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
  };
  var formatCurrency = function formatCurrency(value) {
    var amount = Math.max(Number(value) || 0, 0);
    var symbol = currencySymbols[currencyCode];
    if (!symbol) {
      return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currencyCode,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(amount);
    }
    return "".concat(symbol).concat(amount.toLocaleString('de-DE', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }));
  };
  var formatDateTime = function formatDateTime(value) {
    if (!value) {
      return '-';
    }
    var parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
      return value;
    }
    return new Intl.DateTimeFormat(locale, {
      year: 'numeric',
      month: 'short',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    }).format(parsed);
  };
  var getRequestedGuestCount = function getRequestedGuestCount() {
    var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
      _ref$allowIncomplete = _ref.allowIncomplete,
      allowIncomplete = _ref$allowIncomplete === void 0 ? false : _ref$allowIncomplete;
    var minGuests = Number((guestInput === null || guestInput === void 0 ? void 0 : guestInput.getAttribute('min')) || 1);
    var maxGuests = Number((guestInput === null || guestInput === void 0 ? void 0 : guestInput.getAttribute('max')) || capacity || 200);
    var rawGuestValue = String((guestInput === null || guestInput === void 0 ? void 0 : guestInput.value) || '').trim();
    if (allowIncomplete && rawGuestValue === '') {
      return null;
    }
    var parsedGuests = Number(rawGuestValue || minGuests);
    if (!Number.isFinite(parsedGuests)) {
      return allowIncomplete ? null : minGuests;
    }
    return Math.min(Math.max(Math.trunc(parsedGuests), minGuests), maxGuests);
  };
  var isUploadMode = function isUploadMode() {
    return getRequestedGuestCount() > manualGuestThreshold;
  };
  var clearGuestErrors = function clearGuestErrors() {
    guestListTarget === null || guestListTarget === void 0 ? void 0 : guestListTarget.querySelectorAll('.is-invalid').forEach(function (field) {
      field.classList.remove('is-invalid');
    });
    guestListInput === null || guestListInput === void 0 ? void 0 : guestListInput.classList.remove('is-invalid');
  };
  var setGuestErrorMessage = function setGuestErrorMessage() {
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    var visible = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    if (!guestError) {
      return;
    }
    guestError.hidden = !visible;
    if (visible) {
      guestError.textContent = message;
    }
  };
  var syncGuestStateFromInputs = function syncGuestStateFromInputs() {
    if (!guestListTarget) {
      return;
    }
    guests = _toConsumableArray(guestListTarget.querySelectorAll('[data-activity-guest-card]')).map(function (card) {
      var _card$querySelector, _card$querySelector2, _card$querySelector3, _card$querySelector4;
      return {
        name: String(((_card$querySelector = card.querySelector('[data-activity-guest-field="name"]')) === null || _card$querySelector === void 0 ? void 0 : _card$querySelector.value) || '').trim(),
        age: String(((_card$querySelector2 = card.querySelector('[data-activity-guest-field="age"]')) === null || _card$querySelector2 === void 0 ? void 0 : _card$querySelector2.value) || '').trim(),
        sex: String(((_card$querySelector3 = card.querySelector('[data-activity-guest-field="sex"]')) === null || _card$querySelector3 === void 0 ? void 0 : _card$querySelector3.value) || '').trim(),
        phone: String(((_card$querySelector4 = card.querySelector('[data-activity-guest-field="phone"]')) === null || _card$querySelector4 === void 0 ? void 0 : _card$querySelector4.value) || '').trim()
      };
    });
  };
  var ensureManualGuestRows = function ensureManualGuestRows() {
    var requestedGuestCount = getRequestedGuestCount();
    if (guests.length > requestedGuestCount) {
      guests = guests.slice(0, requestedGuestCount);
    }
    if (guests.length === 0) {
      guests.push({
        name: '',
        age: '',
        sex: '',
        phone: ''
      });
    }
  };
  var formatFileSize = function formatFileSize(bytes) {
    var size = Number(bytes || 0);
    if (size >= 1024 * 1024) {
      return "".concat((size / (1024 * 1024)).toFixed(2), " MB");
    }
    return "".concat(Math.max(Math.round(size / 1024), 1), " KB");
  };
  var setModeFieldsDisabled = function setModeFieldsDisabled() {
    var uploadMode = isUploadMode();
    manualGuestsTarget === null || manualGuestsTarget === void 0 ? void 0 : manualGuestsTarget.querySelectorAll('input, select, textarea, button').forEach(function (field) {
      field.disabled = uploadMode;
    });
    uploadPanel === null || uploadPanel === void 0 ? void 0 : uploadPanel.querySelectorAll('input, select, textarea, button, a').forEach(function (field) {
      if (field.tagName === 'A') {
        field.setAttribute('aria-disabled', uploadMode ? 'false' : 'true');
        return;
      }
      field.disabled = !uploadMode;
    });
    if (!uploadMode && guestListInput !== null && guestListInput !== void 0 && guestListInput.value) {
      guestListInput.value = '';
      if (guestListStatus) guestListStatus.textContent = guestListFormatsLabel;
    }
  };
  var updateGuestMode = function updateGuestMode() {
    var uploadMode = isUploadMode();
    if (manualGuestsTarget) {
      manualGuestsTarget.hidden = uploadMode;
    }
    if (uploadPanel) {
      uploadPanel.hidden = !uploadMode;
    }
    if (guestModeLabelTarget) {
      guestModeLabelTarget.textContent = uploadMode ? guestModeUploadLabel : guestModeManualLabel;
    }
    setModeFieldsDisabled();
  };
  var renderGuestProgress = function renderGuestProgress() {
    var _getRequestedGuestCou;
    if (!guestProgressTarget) {
      return;
    }
    var requestedGuestCount = (_getRequestedGuestCou = getRequestedGuestCount({
      allowIncomplete: true
    })) !== null && _getRequestedGuestCou !== void 0 ? _getRequestedGuestCou : 0;
    if (isUploadMode()) {
      var _guestListInput$files;
      guestProgressTarget.textContent = guestListInput !== null && guestListInput !== void 0 && (_guestListInput$files = guestListInput.files) !== null && _guestListInput$files !== void 0 && _guestListInput$files.length ? "".concat(guestListInput.files[0].name, " \xB7 ").concat(fileSizeLabel, ": ").concat(formatFileSize(guestListInput.files[0].size), " \xB7 ").concat(guestListReadyLabel) : guestListRequiredLabel;
      return;
    }
    var completedGuests = guests.filter(function (guest) {
      return guest.name && guest.age && guest.sex;
    }).length;
    guestProgressTarget.textContent = guestProgressLabel.replace(':count', String(completedGuests)).replace(':total', String(requestedGuestCount));
  };
  var renderGuestFields = function renderGuestFields() {
    if (!guestListTarget) {
      return;
    }
    ensureManualGuestRows();
    guestListTarget.innerHTML = guests.map(function (guest, index) {
      var guestNumber = index + 1;
      return "\n                <section class=\"activity-reservation-guest-card\" data-activity-guest-card data-activity-guest-index=\"".concat(index, "\">\n                    <h4>").concat(escapeHtml(guestLabel), " ").concat(guestNumber, "</h4>\n                    <div class=\"activity-reservation-guest-card__grid\">\n                        <div class=\"activity-reservation-field activity-reservation-field--compact\">\n                            <label for=\"activityGuestName").concat(guestNumber, "\">").concat(escapeHtml(tableNameLabel), " <span class=\"activity-reservation-required\" aria-hidden=\"true\">*</span></label>\n                            <input id=\"activityGuestName").concat(guestNumber, "\" type=\"text\" name=\"guests[").concat(index, "][name]\" class=\"form-control\" value=\"").concat(escapeHtml(guest.name), "\" data-activity-guest-field=\"name\" autocomplete=\"off\" required>\n                        </div>\n                        <div class=\"activity-reservation-field activity-reservation-field--compact\">\n                            <label for=\"activityGuestAge").concat(guestNumber, "\">").concat(escapeHtml(tableAgeCategoryLabel), " <span class=\"activity-reservation-required\" aria-hidden=\"true\">*</span></label>\n                            <select id=\"activityGuestAge").concat(guestNumber, "\" name=\"guests[").concat(index, "][age]\" class=\"form-control\" data-activity-guest-field=\"age\" required>\n                                <option value=\"\">").concat(escapeHtml(orderForm.dataset.selectLabel || 'Select'), "</option>\n                                <option value=\"Adult\"").concat(guest.age === 'Adult' ? ' selected' : '', ">").concat(escapeHtml(adultLabel), "</option>\n                                <option value=\"Child\"").concat(guest.age === 'Child' ? ' selected' : '', ">").concat(escapeHtml(childLabel), "</option>\n                            </select>\n                        </div>\n                        <div class=\"activity-reservation-field activity-reservation-field--compact\">\n                            <label for=\"activityGuestSex").concat(guestNumber, "\">").concat(escapeHtml(tableGenderLabel), " <span class=\"activity-reservation-required\" aria-hidden=\"true\">*</span></label>\n                            <select id=\"activityGuestSex").concat(guestNumber, "\" name=\"guests[").concat(index, "][sex]\" class=\"form-control\" data-activity-guest-field=\"sex\" required>\n                                <option value=\"\">").concat(escapeHtml(orderForm.dataset.selectLabel || 'Select'), "</option>\n                                <option value=\"Male\"").concat(guest.sex === 'Male' ? ' selected' : '', ">").concat(escapeHtml(maleLabel), "</option>\n                                <option value=\"Female\"").concat(guest.sex === 'Female' ? ' selected' : '', ">").concat(escapeHtml(femaleLabel), "</option>\n                            </select>\n                        </div>\n                        <div class=\"activity-reservation-field activity-reservation-field--compact\">\n                            <label for=\"activityGuestPhone").concat(guestNumber, "\">").concat(escapeHtml(phoneLabel), "</label>\n                            <input id=\"activityGuestPhone").concat(guestNumber, "\" type=\"text\" name=\"guests[").concat(index, "][phone]\" class=\"form-control\" value=\"").concat(escapeHtml(guest.phone), "\" data-activity-guest-field=\"phone\" autocomplete=\"off\">\n                        </div>\n                    </div>\n                </section>\n            ");
    }).join('');
    var requestedGuestCount = getRequestedGuestCount();
    if (addGuestButton) {
      addGuestButton.disabled = guests.length >= requestedGuestCount || isUploadMode();
    }
    updateGuestMode();
    renderGuestProgress();
  };
  var validateGuestManifest = function validateGuestManifest() {
    var showMessage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    clearGuestErrors();
    if (isUploadMode()) {
      var _guestListInput$files2;
      var hasFile = Boolean(guestListInput === null || guestListInput === void 0 ? void 0 : (_guestListInput$files2 = guestListInput.files) === null || _guestListInput$files2 === void 0 ? void 0 : _guestListInput$files2.length);
      if (!hasFile) {
        guestListInput === null || guestListInput === void 0 ? void 0 : guestListInput.classList.add('is-invalid');
        setGuestErrorMessage(guestListRequiredLabel, showMessage);
        if (showMessage) guestListInput === null || guestListInput === void 0 ? void 0 : guestListInput.focus();
        return false;
      }
      setGuestErrorMessage('', false);
      return true;
    }
    syncGuestStateFromInputs();
    var requestedGuestCount = getRequestedGuestCount();
    var filledGuests = guests.filter(function (guest) {
      return guest.name || guest.age || guest.sex || guest.phone;
    });
    var firstInvalidField = null;
    var isValid = filledGuests.length >= 1 && filledGuests.length <= requestedGuestCount;
    _toConsumableArray((guestListTarget === null || guestListTarget === void 0 ? void 0 : guestListTarget.querySelectorAll('[data-activity-guest-card]')) || []).forEach(function (card) {
      var hasAnyValue = _toConsumableArray(card.querySelectorAll('[data-activity-guest-field]')).some(function (field) {
        return String(field.value || '').trim();
      });
      ['name', 'age', 'sex'].forEach(function (fieldName) {
        var field = card.querySelector("[data-activity-guest-field=\"".concat(fieldName, "\"]"));
        if (hasAnyValue && field && !String(field.value || '').trim()) {
          field.classList.add('is-invalid');
          firstInvalidField = firstInvalidField || field;
          isValid = false;
        }
      });
    });
    setGuestErrorMessage(guestCountMismatchLabel, showMessage && !isValid);
    if (showMessage && firstInvalidField) {
      firstInvalidField.focus();
    }
    return isValid;
  };
  var renderUnavailablePrice = function renderUnavailablePrice() {
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : priceUnavailableLabel;
    quoteReady = false;
    if (pricePerPaxTarget) pricePerPaxTarget.textContent = '-';
    finalPriceTargets.forEach(function (target) {
      target.textContent = '-';
    });
    if (promotionRow) promotionRow.hidden = true;
    if (priceStatusTarget) priceStatusTarget.textContent = message;
    if (submitButton) submitButton.disabled = true;
  };
  var requestPriceSummary = /*#__PURE__*/function () {
    var _ref2 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var _quoteRequestControll;
      var guestCount, _payload$quote, response, payload;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              guestCount = Math.max(Number((guestInput === null || guestInput === void 0 ? void 0 : guestInput.value) || 0), 0);
              if (!(!quoteUrl || !(travelDateInput !== null && travelDateInput !== void 0 && travelDateInput.value) || guestCount < Number((guestInput === null || guestInput === void 0 ? void 0 : guestInput.min) || 1) || capacity > 0 && guestCount > capacity)) {
                _context.next = 4;
                break;
              }
              renderUnavailablePrice();
              return _context.abrupt("return", false);
            case 4:
              (_quoteRequestControll = quoteRequestController) === null || _quoteRequestControll === void 0 ? void 0 : _quoteRequestControll.abort();
              quoteRequestController = new AbortController();
              renderUnavailablePrice(priceLoadingLabel);
              _context.prev = 7;
              _context.next = 10;
              return fetch(quoteUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                  Accept: 'application/json',
                  'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                  'X-CSRF-TOKEN': csrfToken
                },
                body: new URLSearchParams({
                  number_of_guests: String(guestCount),
                  travel_date: travelDateInput.value || ''
                }).toString(),
                signal: quoteRequestController.signal
              });
            case 10:
              response = _context.sent;
              _context.next = 13;
              return response.json();
            case 13:
              payload = _context.sent;
              if (!(!response.ok || payload.price_available !== true || !payload.display)) {
                _context.next = 17;
                break;
              }
              renderUnavailablePrice(payload.message || priceUnavailableLabel);
              return _context.abrupt("return", false);
            case 17:
              quoteReady = true;
              if (pricePerPaxTarget) pricePerPaxTarget.textContent = formatCurrency(payload.display.unit_price_usd);
              if (promotionDiscountTarget) promotionDiscountTarget.textContent = "- ".concat(formatCurrency(payload.display.discount_total_usd));
              if (promotionRow) promotionRow.hidden = Number(((_payload$quote = payload.quote) === null || _payload$quote === void 0 ? void 0 : _payload$quote.discount_total_usd_minor) || 0) <= 0;
              finalPriceTargets.forEach(function (target) {
                target.textContent = formatCurrency(payload.display.final_total_usd);
              });
              if (priceStatusTarget) priceStatusTarget.textContent = '';
              if (submitButton && !isSubmitting) submitButton.disabled = false;
              return _context.abrupt("return", true);
            case 27:
              _context.prev = 27;
              _context.t0 = _context["catch"](7);
              if (_context.t0.name !== 'AbortError') {
                renderUnavailablePrice();
              }
              return _context.abrupt("return", false);
            case 31:
            case "end":
              return _context.stop();
          }
        }
      }, _callee, null, [[7, 27]]);
    }));
    return function requestPriceSummary() {
      return _ref2.apply(this, arguments);
    };
  }();
  var updatePriceSummary = function updatePriceSummary() {
    var guestCount = Math.max(Number((guestInput === null || guestInput === void 0 ? void 0 : guestInput.value) || 0), 0);
    quoteReady = false;
    if (guestCountPriceTarget) {
      guestCountPriceTarget.textContent = "".concat(guestCount, " ").concat(paxLabel);
    }
    window.clearTimeout(quoteRequestTimer);
    quoteRequestTimer = window.setTimeout(requestPriceSummary, 250);
  };
  var renderGuestManifestTable = function renderGuestManifestTable() {
    if (!guestManifestTableTarget) {
      return;
    }
    if (isUploadMode()) {
      var _guestListInput$files3, _guestListInput$files4;
      var fileName = (guestListInput === null || guestListInput === void 0 ? void 0 : (_guestListInput$files3 = guestListInput.files) === null || _guestListInput$files3 === void 0 ? void 0 : (_guestListInput$files4 = _guestListInput$files3[0]) === null || _guestListInput$files4 === void 0 ? void 0 : _guestListInput$files4.name) || '';
      guestManifestTableTarget.innerHTML = fileName ? "<div class=\"activity-reservation-guest-summary__empty\">".concat(escapeHtml(getRequestedGuestCount()), " ").concat(escapeHtml(paxLabel), " \xB7 ").concat(escapeHtml(guestListSelectedLabel.replace(':file', fileName)), "</div>") : "<div class=\"activity-reservation-guest-summary__empty\">".concat(escapeHtml(guestListRequiredLabel), "</div>");
      return;
    }
    syncGuestStateFromInputs();
    var filledGuests = guests.filter(function (guest) {
      return guest.name || guest.age || guest.sex || guest.phone;
    });
    if (!filledGuests.length) {
      guestManifestTableTarget.innerHTML = "<div class=\"activity-reservation-guest-summary__empty\">".concat(escapeHtml(reviewEmptyLabel), "</div>");
      return;
    }
    var rows = filledGuests.map(function (guest, index) {
      return "\n            <tr>\n                <td>".concat(index + 1, "</td>\n                <td>").concat(escapeHtml(guest.name || "".concat(guestLabel, " ").concat(index + 1)), "</td>\n                <td>").concat(escapeHtml(guest.age || '-'), "</td>\n                <td>").concat(escapeHtml(guest.sex || '-'), "</td>\n                <td>").concat(escapeHtml(guest.phone || '-'), "</td>\n            </tr>\n        ");
    }).join('');
    guestManifestTableTarget.innerHTML = "\n            <div class=\"activity-reservation-guest-summary__table-wrap\">\n                <table class=\"activity-reservation-guest-summary__table\">\n                    <thead>\n                        <tr>\n                            <th>".concat(escapeHtml(tableNoLabel), "</th>\n                            <th>").concat(escapeHtml(tableNameLabel), "</th>\n                            <th>").concat(escapeHtml(tableAgeCategoryLabel), "</th>\n                            <th>").concat(escapeHtml(tableGenderLabel), "</th>\n                            <th>").concat(escapeHtml(tablePhoneNumberLabel), "</th>\n                        </tr>\n                    </thead>\n                    <tbody>").concat(rows, "</tbody>\n                </table>\n            </div>\n        ");
  };
  var updateReview = function updateReview() {
    var _pickupLocationInput$, _dropoffLocationInput;
    syncGuestStateFromInputs();
    var valueMap = {
      activity: (modalElement === null || modalElement === void 0 ? void 0 : modalElement.dataset.activityName) || document.title,
      supplier: (modalElement === null || modalElement === void 0 ? void 0 : modalElement.dataset.activitySupplier) || '-',
      travel_date: formatDateTime((travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.value) || ''),
      number_of_guests: "".concat((guestInput === null || guestInput === void 0 ? void 0 : guestInput.value) || 0, " ").concat(paxLabel),
      pickup_location: (pickupLocationInput === null || pickupLocationInput === void 0 ? void 0 : (_pickupLocationInput$ = pickupLocationInput.value) === null || _pickupLocationInput$ === void 0 ? void 0 : _pickupLocationInput$.trim()) || '-',
      dropoff_location: (dropoffLocationInput === null || dropoffLocationInput === void 0 ? void 0 : (_dropoffLocationInput = dropoffLocationInput.value) === null || _dropoffLocationInput === void 0 ? void 0 : _dropoffLocationInput.trim()) || '-',
      guest_information: isUploadMode() ? guestModeUploadLabel : "".concat(guests.filter(function (guest) {
        return guest.name || guest.age || guest.sex || guest.phone;
      }).length, " ").concat(guestLabel)
    };
    reviewTargets.forEach(function (target) {
      var key = target.dataset.activityOrderReview;
      target.textContent = valueMap[key] || '-';
    });
    renderGuestManifestTable();
    renderGuestProgress();
  };
  var focusFirstInvalidField = function focusFirstInvalidField(container) {
    var invalidField = container === null || container === void 0 ? void 0 : container.querySelector('.is-invalid, :invalid');
    if (invalidField && typeof invalidField.focus === 'function') {
      invalidField.focus({
        preventScroll: false
      });
    }
  };
  var validateField = function validateField(field) {
    if (!field) {
      return true;
    }
    var isValid = field.checkValidity();
    field.classList.toggle('is-invalid', !isValid);
    return isValid;
  };
  var validateStep = /*#__PURE__*/function () {
    var _ref3 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2(stepIndex) {
      var focusInvalid,
        panel,
        isValid,
        fields,
        _args2 = arguments;
      return _regeneratorRuntime().wrap(function _callee2$(_context2) {
        while (1) {
          switch (_context2.prev = _context2.next) {
            case 0:
              focusInvalid = _args2.length > 1 && _args2[1] !== undefined ? _args2[1] : true;
              panel = stepPanels[stepIndex];
              if (panel) {
                _context2.next = 4;
                break;
              }
              return _context2.abrupt("return", true);
            case 4:
              isValid = true;
              fields = _toConsumableArray(panel.querySelectorAll('input, textarea, select')).filter(function (field) {
                return field.type !== 'hidden' && !field.disabled;
              });
              fields.forEach(function (field) {
                isValid = validateField(field) && isValid;
              });
              if (!(stepIndex === 0 && isValid && !quoteReady)) {
                _context2.next = 11;
                break;
              }
              _context2.next = 10;
              return requestPriceSummary();
            case 10:
              isValid = _context2.sent;
            case 11:
              if (panel.querySelector('[data-activity-guest-list], [data-activity-upload-panel]') && !validateGuestManifest(true)) {
                isValid = false;
              }
              if (!isValid && focusInvalid) {
                focusFirstInvalidField(panel);
              }
              return _context2.abrupt("return", isValid);
            case 14:
            case "end":
              return _context2.stop();
          }
        }
      }, _callee2);
    }));
    return function validateStep(_x) {
      return _ref3.apply(this, arguments);
    };
  }();
  var showStep = function showStep(stepIndex) {
    activeStep = Math.min(Math.max(stepIndex, 0), stepPanels.length - 1);
    stepPanels.forEach(function (panel, index) {
      var isActive = index === activeStep;
      panel.hidden = !isActive;
      panel.classList.toggle('is-active', isActive);
    });
    stepNavItems.forEach(function (item, index) {
      item.classList.toggle('is-active', index === activeStep);
      item.classList.toggle('is-complete', index < activeStep);
    });
    updateGuestMode();
    if (activeStep === 1) {
      renderGuestFields();
    }
    if (activeStep === stepPanels.length - 1) {
      updateReview();
    }
  };
  var setSubmittingState = function setSubmittingState(submitting) {
    var processingLabel = (submitButton === null || submitButton === void 0 ? void 0 : submitButton.dataset.processingLabel) || 'Processing...';
    isSubmitting = Boolean(submitting);
    orderForm.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');
    orderForm.toggleAttribute('inert', isSubmitting);
    document.documentElement.classList.toggle('activity-submit-locked', isSubmitting);
    document.body.classList.toggle('activity-submit-locked', isSubmitting);
    document.documentElement.classList.toggle('frontend-order-submit-locked', isSubmitting);
    document.body.classList.toggle('frontend-order-submit-locked', isSubmitting);
    if (submitOverlay) {
      if (isSubmitting && submitOverlay.parentElement !== document.body) {
        document.body.appendChild(submitOverlay);
      }
      submitOverlay.style.setProperty('z-index', '2147483647', 'important');
      submitOverlay.classList.toggle('hidden', !isSubmitting);
      submitOverlay.setAttribute('aria-hidden', isSubmitting ? 'false' : 'true');
    }
    [].concat(_toConsumableArray(previousButtons), _toConsumableArray(nextButtons), [submitButton]).filter(Boolean).forEach(function (button) {
      var originalLabel = button.dataset.originalLabel || button.innerHTML;
      button.dataset.originalLabel = originalLabel;
      button.disabled = isSubmitting;
      button.classList.toggle('is-processing', isSubmitting && button === submitButton);
      button.setAttribute('aria-disabled', isSubmitting ? 'true' : 'false');
      if (button === submitButton) {
        button.innerHTML = isSubmitting ? "<span class=\"frontend-action-spinner\" aria-hidden=\"true\"></span><span>".concat(processingLabel, "</span>") : originalLabel;
      }
    });
  };
  var attemptSubmit = /*#__PURE__*/function () {
    var _ref4 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee3() {
      var index, isStepValid;
      return _regeneratorRuntime().wrap(function _callee3$(_context3) {
        while (1) {
          switch (_context3.prev = _context3.next) {
            case 0:
              if (!isSubmitting) {
                _context3.next = 2;
                break;
              }
              return _context3.abrupt("return");
            case 2:
              syncGuestStateFromInputs();
              index = 0;
            case 4:
              if (!(index < stepPanels.length)) {
                _context3.next = 15;
                break;
              }
              _context3.next = 7;
              return validateStep(index, false);
            case 7:
              isStepValid = _context3.sent;
              if (isStepValid) {
                _context3.next = 12;
                break;
              }
              showStep(index);
              focusFirstInvalidField(stepPanels[index]);
              return _context3.abrupt("return");
            case 12:
              index += 1;
              _context3.next = 4;
              break;
            case 15:
              if (validateField(termsCheckbox)) {
                _context3.next = 19;
                break;
              }
              showStep(stepPanels.length - 1);
              termsCheckbox === null || termsCheckbox === void 0 ? void 0 : termsCheckbox.focus();
              return _context3.abrupt("return");
            case 19:
              setSubmittingState(true);
              submissionGuard.markSubmitted();
              HTMLFormElement.prototype.submit.call(orderForm);
            case 22:
            case "end":
              return _context3.stop();
          }
        }
      }, _callee3);
    }));
    return function attemptSubmit() {
      return _ref4.apply(this, arguments);
    };
  }();
  orderForm.addEventListener('input', function (event) {
    if (event.target.matches('input, textarea, select')) {
      event.target.classList.remove('is-invalid');
      syncGuestStateFromInputs();
      updateReview();
    }
  });
  guestInput === null || guestInput === void 0 ? void 0 : guestInput.addEventListener('input', function () {
    syncGuestStateFromInputs();
    ensureManualGuestRows();
    renderGuestFields();
    updateReview();
    updatePriceSummary();
    validateGuestManifest(false);
  });
  guestInput === null || guestInput === void 0 ? void 0 : guestInput.addEventListener('change', function () {
    syncGuestStateFromInputs();
    ensureManualGuestRows();
    renderGuestFields();
    updateReview();
    updatePriceSummary();
    validateGuestManifest(false);
  });
  addGuestButton === null || addGuestButton === void 0 ? void 0 : addGuestButton.addEventListener('click', function () {
    syncGuestStateFromInputs();
    if (guests.length >= getRequestedGuestCount() || isUploadMode()) {
      return;
    }
    guests.push({
      name: '',
      age: '',
      sex: '',
      phone: ''
    });
    renderGuestFields();
    updateReview();
  });
  guestListInput === null || guestListInput === void 0 ? void 0 : guestListInput.addEventListener('change', function () {
    guestListInput.classList.remove('is-invalid');
    if (guestListStatus) {
      var _guestListInput$files5;
      guestListStatus.textContent = (_guestListInput$files5 = guestListInput.files) !== null && _guestListInput$files5 !== void 0 && _guestListInput$files5.length ? "".concat(guestListInput.files[0].name, " \xB7 ").concat(fileSizeLabel, ": ").concat(formatFileSize(guestListInput.files[0].size), " \xB7 ").concat(guestListReadyLabel) : guestListFormatsLabel;
    }
    renderGuestProgress();
    updateReview();
  });
  travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.addEventListener('change', function () {
    updateReview();
    updatePriceSummary();
  });
  nextButtons.forEach(function (button) {
    button.addEventListener('click', /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
      return _regeneratorRuntime().wrap(function _callee4$(_context4) {
        while (1) {
          switch (_context4.prev = _context4.next) {
            case 0:
              _context4.next = 2;
              return validateStep(activeStep);
            case 2:
              if (_context4.sent) {
                _context4.next = 4;
                break;
              }
              return _context4.abrupt("return");
            case 4:
              showStep(activeStep + 1);
            case 5:
            case "end":
              return _context4.stop();
          }
        }
      }, _callee4);
    })));
  });
  previousButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      showStep(activeStep - 1);
    });
  });
  stepNavItems.forEach(function (item) {
    item.addEventListener('click', /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee5() {
      var targetStep, index;
      return _regeneratorRuntime().wrap(function _callee5$(_context5) {
        while (1) {
          switch (_context5.prev = _context5.next) {
            case 0:
              targetStep = Number(item.dataset.activityOrderNav || 0);
              if (!(targetStep <= activeStep)) {
                _context5.next = 4;
                break;
              }
              showStep(targetStep);
              return _context5.abrupt("return");
            case 4:
              index = activeStep;
            case 5:
              if (!(index < targetStep)) {
                _context5.next = 15;
                break;
              }
              _context5.next = 8;
              return validateStep(index, false);
            case 8:
              if (_context5.sent) {
                _context5.next = 12;
                break;
              }
              showStep(index);
              focusFirstInvalidField(stepPanels[index]);
              return _context5.abrupt("return");
            case 12:
              index += 1;
              _context5.next = 5;
              break;
            case 15:
              showStep(targetStep);
            case 16:
            case "end":
              return _context5.stop();
          }
        }
      }, _callee5);
    })));
  });
  submitButton === null || submitButton === void 0 ? void 0 : submitButton.addEventListener('click', function (event) {
    event.preventDefault();
    attemptSubmit();
  });
  orderForm.addEventListener('submit', function (event) {
    event.preventDefault();
    attemptSubmit();
  });
  modalElement === null || modalElement === void 0 ? void 0 : modalElement.addEventListener('hide.bs.modal', function (event) {
    if (isSubmitting) {
      event.preventDefault();
    }
  });
  modalElement === null || modalElement === void 0 ? void 0 : modalElement.setAttribute('data-activity-name', (modalElement === null || modalElement === void 0 ? void 0 : modalElement.dataset.activityName) || document.title);
  modalElement === null || modalElement === void 0 ? void 0 : modalElement.setAttribute('data-activity-supplier', (modalElement === null || modalElement === void 0 ? void 0 : modalElement.dataset.activitySupplier) || '-');
  if (orderForm.dataset.openOnLoad === 'true' && modalElement && (_window$bootstrap = window.bootstrap) !== null && _window$bootstrap !== void 0 && _window$bootstrap.Modal) {
    window.setTimeout(function () {
      window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }, 120);
  }
  submissionGuard.bindHistoryRestore(function () {
    setSubmittingState(false);
    window.location.reload();
  });
  ensureManualGuestRows();
  renderGuestFields();
  updateGuestMode();
  updateReview();
  updatePriceSummary();
  showStep(Number.isFinite(initialStep) ? initialStep : 0);
});
})();

/******/ })()
;