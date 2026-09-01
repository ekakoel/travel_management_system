/******/ (() => { // webpackBootstrap
/*!*************************************!*\
  !*** ./resources/backend/js/app.js ***!
  \*************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return generator._invoke = function (innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; }(innerFn, self, context), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; this._invoke = function (method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); }; } function maybeInvokeDelegate(delegate, context) { var method = delegate.iterator[context.method]; if (undefined === method) { if (context.delegate = null, "throw" === context.method) { if (delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method)) return ContinueSentinel; context.method = "throw", context.arg = new TypeError("The iterator does not provide a 'throw' method"); } return ContinueSentinel; } var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) { if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; } return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, define(Gp, "constructor", GeneratorFunctionPrototype), define(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (object) { var keys = []; for (var key in object) { keys.push(key); } return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) { "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); } }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
/**
 * Backend shared entry point.
 *
 * Keep this bundle isolated from the frontend/Vue app. The legacy backend
 * layout already loads jQuery and panel plugins globally; importing
 * resources/js/app here would replace window.jQuery and detach plugins such
 * as Select2, DataTables, Datepicker, Slick, FullCalendar, and custom scrollbars.
 */

var RICHTEXT_SELECTOR = ['body.sidebar-light .main-container textarea:not([data-backend-richtext="false"])', 'body.sidebar-light .modal textarea:not([data-backend-richtext="false"])', 'textarea.textarea_editor', 'textarea[data-backend-richtext="true"]'].join(', ');
var BACKEND_DATE_PICKER_SELECTOR = '[data-backend-picker="date"]';
var BACKEND_STATUS_TOGGLE_SELECTOR = '[data-backend-status-toggle]';
var BACKEND_MONEY_INPUT_SELECTOR = 'input:not([type="hidden"]):not([type="submit"]):not([type="button"])';
var BACKEND_MONEY_UNIT_BY_NAME = Object.freeze({
  additional_guest_rate: 'USD',
  additional_service_price: 'USD',
  agent_rate: 'USD',
  arrangement_price: 'USD',
  basic_price: 'USD',
  contract_rate: 'IDR',
  contract_rate_idr: 'IDR',
  fee: 'USD',
  holiday_price: 'USD',
  kick_back: 'USD',
  markup: 'USD',
  price: 'USD',
  public_rate: 'USD',
  publish_rate: 'USD',
  rate: 'USD',
  tax: '%',
  week_day_price: 'USD'
});
var BACKEND_REQUIRED_CONTROL_SELECTOR = ['input[required]:not([type="hidden"])', 'select[required]', 'textarea[required]'].join(', ');
var BACKEND_MUTATION_FORM_SELECTOR = 'form:not([data-backend-submit-guard="false"])';
function showBackendModal(modal) {
  var _window$bootstrap, _window$jQuery, _window$jQuery$fn;
  if (!(modal instanceof Element)) {
    return false;
  }
  var bootstrapModal = (_window$bootstrap = window.bootstrap) === null || _window$bootstrap === void 0 ? void 0 : _window$bootstrap.Modal;
  if (typeof (bootstrapModal === null || bootstrapModal === void 0 ? void 0 : bootstrapModal.getOrCreateInstance) === 'function') {
    bootstrapModal.getOrCreateInstance(modal).show();
    return true;
  }
  if ((_window$jQuery = window.jQuery) !== null && _window$jQuery !== void 0 && (_window$jQuery$fn = _window$jQuery.fn) !== null && _window$jQuery$fn !== void 0 && _window$jQuery$fn.modal) {
    window.jQuery(modal).modal('show');
    return true;
  }
  return false;
}
function closeBackendModal(modal) {
  var _window$bootstrap2, _bootstrapModal$getIn, _window$jQuery2, _window$jQuery2$fn;
  if (!(modal instanceof Element)) {
    return false;
  }
  var bootstrapModal = (_window$bootstrap2 = window.bootstrap) === null || _window$bootstrap2 === void 0 ? void 0 : _window$bootstrap2.Modal;
  var bootstrapInstance = bootstrapModal === null || bootstrapModal === void 0 ? void 0 : (_bootstrapModal$getIn = bootstrapModal.getInstance) === null || _bootstrapModal$getIn === void 0 ? void 0 : _bootstrapModal$getIn.call(bootstrapModal, modal);
  if (bootstrapInstance) {
    bootstrapInstance.hide();
    return true;
  }
  if ((_window$jQuery2 = window.jQuery) !== null && _window$jQuery2 !== void 0 && (_window$jQuery2$fn = _window$jQuery2.fn) !== null && _window$jQuery2$fn !== void 0 && _window$jQuery2$fn.modal) {
    window.jQuery(modal).modal('hide');
    return true;
  }
  modal.classList.remove('show');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
  if (!document.querySelector('.modal.show')) {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
      return backdrop.remove();
    });
  }
  return true;
}
function handleBackendModalClose(event) {
  if (!(event.target instanceof Element)) {
    return;
  }
  var closeControl = event.target.closest('[data-backend-modal-close]');
  if (!closeControl) {
    return;
  }
  event.preventDefault();
  closeBackendModal(closeControl.closest('.modal'));
}
function backendFormSubmitControls(form) {
  return Array.from(document.querySelectorAll(['button:not([type])', 'button[type="submit"]', 'input[type="submit"]', 'input[type="image"]'].join(', '))).filter(function (control) {
    return control.form === form;
  });
}
function setBackendActionLoading(control) {
  var loading = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  if (!control) {
    return;
  }
  control.classList.toggle('is-submitting', loading);
  if (!loading) {
    control.removeAttribute('aria-disabled');
    control.removeAttribute('aria-busy');
    var spinner = control.querySelector ? control.querySelector('[data-backend-action-spinner]') : null;
    if (spinner) {
      spinner.remove();
    }
    control.style.removeProperty('--backend-action-spinner-color');
    if (control.tagName === 'INPUT' && control.dataset.backendOriginalValue !== undefined) {
      control.value = control.dataset.backendOriginalValue;
      delete control.dataset.backendOriginalValue;
    }
    return;
  }
  control.setAttribute('aria-disabled', 'true');
  control.setAttribute('aria-busy', 'true');
  if (window.getComputedStyle) {
    control.style.setProperty('--backend-action-spinner-color', window.getComputedStyle(control).color);
  }
  if (control.tagName === 'INPUT') {
    if (control.dataset.backendOriginalValue === undefined) {
      control.dataset.backendOriginalValue = control.value;
    }
    control.value = control.dataset.loadingLabel || 'Processing...';
    return;
  }
  if (!control.querySelector('[data-backend-action-spinner]')) {
    var _spinner = document.createElement('span');
    _spinner.className = 'backend-action-spinner';
    _spinner.setAttribute('aria-hidden', 'true');
    _spinner.setAttribute('data-backend-action-spinner', 'true');
    control.append(_spinner);
  }
}
function resetBackendSubmittingState(form) {
  if (!form) {
    return;
  }
  if (form.backendSubmitDisableTimer) {
    window.clearTimeout(form.backendSubmitDisableTimer);
    form.backendSubmitDisableTimer = null;
  }
  if (form.backendSubmitValidationTimer) {
    window.clearTimeout(form.backendSubmitValidationTimer);
    form.backendSubmitValidationTimer = null;
  }
  delete form.dataset.backendSubmitting;
  delete form.dataset.backendSubmitPending;
  form.backendActiveSubmitter = null;
  form.removeAttribute('aria-busy');
  backendFormSubmitControls(form).forEach(function (control) {
    control.disabled = control.dataset.backendOriginalDisabled === 'true';
    delete control.dataset.backendOriginalDisabled;
    setBackendActionLoading(control, false);
  });
}
function showBackendFormLoading(form, submitter) {
  form.setAttribute('aria-busy', 'true');
  form.backendActiveSubmitter = submitter || form.backendActiveSubmitter || null;
  backendFormSubmitControls(form).forEach(function (control) {
    if (control.dataset.backendOriginalDisabled === undefined) {
      control.dataset.backendOriginalDisabled = control.disabled ? 'true' : 'false';
    }
    control.setAttribute('aria-disabled', 'true');
  });
  setBackendActionLoading(form.backendActiveSubmitter, true);
}
function primeBackendSubmitting(form, submitter) {
  form.dataset.backendSubmitPending = 'true';
  showBackendFormLoading(form, submitter);
  form.backendSubmitValidationTimer = window.setTimeout(function () {
    if (form.dataset.backendSubmitting !== 'true') {
      resetBackendSubmittingState(form);
    }
  }, 0);
}
function commitBackendSubmitting(form, submitter) {
  delete form.dataset.backendSubmitPending;
  form.dataset.backendSubmitting = 'true';
  showBackendFormLoading(form, submitter);

  // Defer the native disabled state until the browser has captured the
  // successful submitter, while the dataset guard blocks repeated submits now.
  form.backendSubmitDisableTimer = window.setTimeout(function () {
    backendFormSubmitControls(form).forEach(function (control) {
      control.disabled = true;
    });
  }, 0);
}
function bindBackendSubmitGuard(form) {
  var method = (form.getAttribute('method') || 'get').toLowerCase();
  if (form.dataset.backendSubmitGuardReady === 'true' || method === 'get' || method === 'dialog') {
    return;
  }
  form.dataset.backendSubmitGuardReady = 'true';
  form.addEventListener('submit', function (event) {
    if (form.dataset.backendSubmitting === 'true') {
      event.preventDefault();
      event.stopImmediatePropagation();
      return;
    }
    if (event.defaultPrevented) {
      resetBackendSubmittingState(form);
      return;
    }
    commitBackendSubmitting(form, event.submitter || form.backendActiveSubmitter);
    window.queueMicrotask(function () {
      if (event.defaultPrevented) {
        resetBackendSubmittingState(form);
      }
    });
  });
}
function backendSubmitControlFromEvent(event) {
  if (!(event.target instanceof Element)) {
    return null;
  }
  return event.target.closest(['button:not([type])', 'button[type="submit"]', 'input[type="submit"]', 'input[type="image"]'].join(', '));
}
function handleBackendActionClick(event) {
  var submitter = backendSubmitControlFromEvent(event);
  if (submitter && submitter.form) {
    var form = submitter.form;
    var method = (form.getAttribute('method') || 'get').toLowerCase();
    if (method !== 'get' && method !== 'dialog' && form.matches(BACKEND_MUTATION_FORM_SELECTOR)) {
      if (form.dataset.backendSubmitting === 'true' || form.dataset.backendSubmitPending === 'true') {
        event.preventDefault();
        event.stopImmediatePropagation();
        return;
      }
      if (!event.defaultPrevented) {
        primeBackendSubmitting(form, submitter);
        window.queueMicrotask(function () {
          if (event.defaultPrevented) {
            resetBackendSubmittingState(form);
          }
        });
      }
      return;
    }
  }
  var standaloneAction = event.target instanceof Element ? event.target.closest('[data-backend-action-loading]') : null;
  if (!standaloneAction || event.defaultPrevented) {
    return;
  }
  if (standaloneAction.classList.contains('is-submitting')) {
    event.preventDefault();
    event.stopImmediatePropagation();
    return;
  }
  setBackendActionLoading(standaloneAction, true);
  window.queueMicrotask(function () {
    if (event.defaultPrevented) {
      setBackendActionLoading(standaloneAction, false);
    }
  });
}
function backendCsrfToken() {
  var _document$querySelect;
  return ((_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.getAttribute('content')) || '';
}
function updateBackendStatusBadge(badge, status, tone) {
  if (!badge) {
    return;
  }
  Array.from(badge.classList).filter(function (className) {
    return className.startsWith('backend-status-badge--');
  }).forEach(function (className) {
    return badge.classList.remove(className);
  });
  badge.classList.add("backend-status-badge--".concat(tone || status.toLowerCase()));
  badge.textContent = status;
}
function updateBackendStatusToggle(toggle, status, nextStatus, tone) {
  var isActive = status === 'Active';
  var label = toggle.querySelector('[data-backend-status-toggle-label]');
  var activeLabel = toggle.dataset.backendStatusLabelActive || 'Active';
  var draftLabel = toggle.dataset.backendStatusLabelDraft || 'Draft';
  toggle.dataset.backendStatusCurrent = status;
  toggle.dataset.backendStatusNext = nextStatus || (isActive ? 'Draft' : 'Active');
  toggle.classList.toggle('is-active', isActive);
  toggle.setAttribute('aria-pressed', isActive ? 'true' : 'false');
  toggle.setAttribute('title', isActive ? activeLabel : draftLabel);
  if (tone) {
    toggle.dataset.backendStatusTone = tone;
  }
  if (label) {
    label.textContent = isActive ? activeLabel : draftLabel;
  }
}
function handleBackendStatusToggleClick(_x) {
  return _handleBackendStatusToggleClick.apply(this, arguments);
}
function _handleBackendStatusToggleClick() {
  _handleBackendStatusToggleClick = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee(event) {
    var toggle, url, nextStatus, response, payload, badgeTarget;
    return _regeneratorRuntime().wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            toggle = event.target instanceof Element ? event.target.closest(BACKEND_STATUS_TOGGLE_SELECTOR) : null;
            if (toggle) {
              _context.next = 3;
              break;
            }
            return _context.abrupt("return");
          case 3:
            event.preventDefault();
            if (!toggle.classList.contains('is-submitting')) {
              _context.next = 6;
              break;
            }
            return _context.abrupt("return");
          case 6:
            url = toggle.dataset.backendStatusUrl;
            nextStatus = toggle.dataset.backendStatusNext;
            if (!(!url || !nextStatus)) {
              _context.next = 10;
              break;
            }
            return _context.abrupt("return");
          case 10:
            setBackendActionLoading(toggle, true);
            _context.prev = 11;
            _context.next = 14;
            return window.fetch(url, {
              method: 'PATCH',
              headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': backendCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                status: nextStatus
              })
            });
          case 14:
            response = _context.sent;
            _context.next = 17;
            return response.json()["catch"](function () {
              return {};
            });
          case 17:
            payload = _context.sent;
            if (response.ok) {
              _context.next = 20;
              break;
            }
            throw new Error(payload.message || 'Status could not be updated.');
          case 20:
            updateBackendStatusToggle(toggle, payload.status, payload.next_status, payload.tone);
            badgeTarget = toggle.dataset.backendStatusBadgeTarget;
            if (badgeTarget) {
              document.querySelectorAll(badgeTarget).forEach(function (badge) {
                updateBackendStatusBadge(badge, payload.status, payload.tone);
              });
            }
            _context.next = 28;
            break;
          case 25:
            _context.prev = 25;
            _context.t0 = _context["catch"](11);
            window.alert(_context.t0.message || 'Status could not be updated.');
          case 28:
            _context.prev = 28;
            setBackendActionLoading(toggle, false);
            return _context.finish(28);
          case 31:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, null, [[11, 25, 28, 31]]);
  }));
  return _handleBackendStatusToggleClick.apply(this, arguments);
}
function initBackendSubmitGuards() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  var forms = [];
  if (root instanceof HTMLFormElement && root.matches(BACKEND_MUTATION_FORM_SELECTOR)) {
    forms.push(root);
  }
  if (typeof root.querySelectorAll === 'function') {
    forms.push.apply(forms, _toConsumableArray(root.querySelectorAll(BACKEND_MUTATION_FORM_SELECTOR)));
  }
  forms.forEach(bindBackendSubmitGuard);
}
function backendControlLabel(control) {
  if (control.labels && control.labels.length) {
    return control.labels[0];
  }
  var field = control.closest('.backend-form-field, .form-group');
  return field ? field.querySelector('label') : null;
}
function ensureBackendRequiredMarker(control) {
  var label = backendControlLabel(control);
  if (!label) {
    return;
  }
  var existingMarker = Array.from(label.querySelectorAll('span, b')).find(function (candidate) {
    return candidate.textContent.trim() === '*';
  });
  if (existingMarker) {
    existingMarker.classList.add('backend-required-marker');
    existingMarker.setAttribute('aria-hidden', 'true');
    return;
  }
  var marker = document.createElement('span');
  marker.className = 'backend-required-marker';
  marker.setAttribute('aria-hidden', 'true');
  marker.setAttribute('data-backend-required-generated', 'true');
  marker.textContent = '*';
  label.append(document.createTextNode(' '), marker);
}
function initBackendRequiredMarkers() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  var controls = [];
  if (root instanceof Element && root.matches(BACKEND_REQUIRED_CONTROL_SELECTOR)) {
    controls.push(root);
  }
  if (typeof root.querySelectorAll === 'function') {
    controls.push.apply(controls, _toConsumableArray(root.querySelectorAll(BACKEND_REQUIRED_CONTROL_SELECTOR)));
  }
  controls.forEach(ensureBackendRequiredMarker);
}
function backendMoneyFieldName(control) {
  return (control.getAttribute('name') || '').replace(/\[[^\]]*\]/g, '').trim();
}
function backendMoneyUnitMap(control) {
  return (control.dataset.backendMoneyUnitMap || '').split('|').reduce(function (units, entry) {
    var separator = entry.indexOf(':');
    if (separator > 0) {
      units[entry.slice(0, separator)] = entry.slice(separator + 1);
    }
    return units;
  }, {});
}
function backendMoneyUnit(control) {
  if (control.dataset.backendMoneyUnitSource) {
    var scope = control.closest('form') || document;
    var source = scope.querySelector(control.dataset.backendMoneyUnitSource);
    var mappedUnit = source ? backendMoneyUnitMap(control)[source.value] : null;
    if (mappedUnit) {
      return mappedUnit;
    }
  }
  return control.dataset.backendMoneyUnit || BACKEND_MONEY_UNIT_BY_NAME[backendMoneyFieldName(control)] || '';
}
function backendMoneyHelpText(unit) {
  var _document$body;
  var template = ((_document$body = document.body) === null || _document$body === void 0 ? void 0 : _document$body.dataset.backendMoneyHint) || ':unit';
  return template.replace(':unit', unit);
}
function normalizeBackendMoneyValue(value, unit) {
  var displayValue = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var normalized = String(value !== null && value !== void 0 ? value : '').trim().replace(/\s+/g, '').replace(/[^\d.,-]/g, '');
  if (!normalized) {
    return '';
  }
  if (displayValue && unit === 'IDR') {
    normalized = normalized.replace(/\./g, '').replace(',', '.');
  } else {
    normalized = normalized.replace(/,/g, '');
  }
  var negative = normalized.startsWith('-');
  var unsigned = normalized.replace(/-/g, '');
  var decimalPosition = unsigned.indexOf('.');
  var integer = decimalPosition >= 0 ? unsigned.slice(0, decimalPosition) : unsigned;
  var fraction = decimalPosition >= 0 ? unsigned.slice(decimalPosition + 1).replace(/\./g, '') : null;
  integer = integer.replace(/^0+(?=\d)/, '') || '0';
  return "".concat(negative ? '-' : '').concat(integer).concat(fraction !== null ? ".".concat(fraction) : '');
}
function formatBackendMoneyValue(rawValue, unit) {
  if (rawValue === '') {
    return '';
  }
  var negative = rawValue.startsWith('-');
  var unsigned = negative ? rawValue.slice(1) : rawValue;
  var _unsigned$split = unsigned.split('.', 2),
    _unsigned$split2 = _slicedToArray(_unsigned$split, 2),
    _unsigned$split2$ = _unsigned$split2[0],
    integer = _unsigned$split2$ === void 0 ? '0' : _unsigned$split2$,
    fraction = _unsigned$split2[1];
  var groupingSeparator = unit === 'IDR' ? '.' : ',';
  var decimalSeparator = unit === 'IDR' ? ',' : '.';
  var groupedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, groupingSeparator);
  return "".concat(negative ? '-' : '').concat(groupedInteger).concat(fraction !== undefined ? "".concat(decimalSeparator).concat(fraction) : '');
}
function backendMoneyRawValue(control) {
  var unit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : backendMoneyUnit(control);
  var isFormatted = control.dataset.backendMoneyFormatted === 'true';
  return normalizeBackendMoneyValue(control.value, unit, isFormatted);
}
function setBackendMoneyCaret(control, digitOffset, wasAtEnd) {
  if (wasAtEnd) {
    control.setSelectionRange(control.value.length, control.value.length);
    return;
  }
  if (digitOffset <= 0) {
    control.setSelectionRange(0, 0);
    return;
  }
  var digitsSeen = 0;
  var caret = control.value.length;
  for (var index = 0; index < control.value.length; index += 1) {
    if (/\d/.test(control.value[index])) {
      digitsSeen += 1;
    }
    if (digitsSeen === digitOffset) {
      caret = index + 1;
      break;
    }
  }
  control.setSelectionRange(caret, caret);
}
function formatBackendMoneyInput(control) {
  var unit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : backendMoneyUnit(control);
  var preserveCaret = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var caretPosition = preserveCaret ? control.selectionStart : null;
  var wasAtEnd = preserveCaret && caretPosition === control.value.length;
  var digitOffset = preserveCaret ? (control.value.slice(0, caretPosition !== null && caretPosition !== void 0 ? caretPosition : 0).match(/\d/g) || []).length : 0;
  var rawValue = backendMoneyRawValue(control, unit);
  control.dataset.backendMoneyRawValue = rawValue;
  control.dataset.backendMoneyCurrentUnit = unit;
  control.dataset.backendMoneyFormatted = 'true';
  control.setAttribute('type', 'text');
  control.setAttribute('inputmode', 'decimal');
  control.value = formatBackendMoneyValue(rawValue, unit);
  if (preserveCaret && document.activeElement === control) {
    setBackendMoneyCaret(control, digitOffset, wasAtEnd);
  }
}
function restoreBackendMoneyInput(control) {
  var unit = control.dataset.backendMoneyCurrentUnit || backendMoneyUnit(control);
  var rawValue = backendMoneyRawValue(control, unit).replace(/\.$/, '');
  control.dataset.backendMoneyRawValue = rawValue;
  control.dataset.backendMoneyFormatted = 'false';
  control.setAttribute('type', control.dataset.backendMoneyOriginalType || 'number');
  control.value = rawValue;
}
function bindBackendMoneyInput(control) {
  if (control.dataset.backendMoneyFormattingReady === 'true') {
    return;
  }
  control.dataset.backendMoneyFormattingReady = 'true';
  control.dataset.backendMoneyOriginalType = control.getAttribute('type') || 'text';
  control.addEventListener('input', function () {
    formatBackendMoneyInput(control, backendMoneyUnit(control), true);
  });
  control.addEventListener('blur', function () {
    formatBackendMoneyInput(control);
  });
}
function updateBackendMoneyInput(control) {
  var _document$body2, _help;
  var unit = backendMoneyUnit(control);
  if (!unit) {
    return;
  }
  var previousUnit = control.dataset.backendMoneyCurrentUnit;
  if (previousUnit && previousUnit !== unit && control.dataset.backendMoneyFormatted === 'true') {
    control.value = backendMoneyRawValue(control, previousUnit);
    control.dataset.backendMoneyFormatted = 'false';
  }
  var shell = control.closest('[data-backend-money-shell]');
  if (!shell) {
    shell = document.createElement('div');
    shell.className = 'backend-money-control';
    shell.dataset.backendMoneyShell = 'true';
    control.parentNode.insertBefore(shell, control);
    shell.append(control);
  }
  var unitLabel = shell.querySelector('[data-backend-money-unit-label]');
  if (!unitLabel) {
    unitLabel = document.createElement('span');
    unitLabel.className = 'backend-money-control__unit';
    unitLabel.dataset.backendMoneyUnitLabel = 'true';
    shell.prepend(unitLabel);
  }
  unitLabel.textContent = unit;
  unitLabel.setAttribute('aria-label', ((_document$body2 = document.body) === null || _document$body2 === void 0 ? void 0 : _document$body2.dataset.backendMoneyLabel) || unit);
  control.dataset.backendMoneyReady = 'true';
  var legacyWrapper = shell.parentElement;
  if (legacyWrapper !== null && legacyWrapper !== void 0 && legacyWrapper.classList.contains('btn-icon')) {
    Array.from(legacyWrapper.children).forEach(function (child) {
      if (child !== shell && child.tagName === 'SPAN') {
        child.hidden = true;
      }
    });
  }
  var help = shell.nextElementSibling;
  if (!((_help = help) !== null && _help !== void 0 && _help.matches('[data-backend-money-help]'))) {
    help = document.createElement('small');
    help.className = 'backend-form-help backend-money-help';
    help.dataset.backendMoneyHelp = 'true';
    shell.after(help);
  }
  if (!help.id) {
    help.id = "backendMoneyHelp".concat(Math.random().toString(36).slice(2, 10));
  }
  help.textContent = backendMoneyHelpText(unit);
  var describedBy = new Set((control.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
  describedBy.add(help.id);
  control.setAttribute('aria-describedby', Array.from(describedBy).join(' '));
  bindBackendMoneyInput(control);
  formatBackendMoneyInput(control, unit, document.activeElement === control);
}
function initBackendMoneyInputs() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  var controls = [];
  if (root instanceof HTMLInputElement && root.matches(BACKEND_MONEY_INPUT_SELECTOR)) {
    controls.push(root);
  }
  if (typeof root.querySelectorAll === 'function') {
    controls.push.apply(controls, _toConsumableArray(root.querySelectorAll(BACKEND_MONEY_INPUT_SELECTOR)));
  }
  controls.forEach(updateBackendMoneyInput);
}
function initBackendDatePickers() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.datepicker) {
    return;
  }
  window.jQuery(root).find(BACKEND_DATE_PICKER_SELECTOR).addBack(BACKEND_DATE_PICKER_SELECTOR).each(function initDatePicker() {
    var input = window.jQuery(this);
    if (input.data('backend-date-picker-ready') || input.data('datepicker')) {
      return;
    }
    input.data('backend-date-picker-ready', true).datepicker({
      language: 'en',
      autoClose: true,
      dateFormat: input.data('backend-picker-format') || 'yyyy-mm-dd'
    });
  });
}
function initBackendRichText() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.summernote) {
    return;
  }
  window.jQuery(root).find(RICHTEXT_SELECTOR).addBack(RICHTEXT_SELECTOR).each(function initEditor() {
    var textarea = window.jQuery(this);
    if (textarea.data('backend-richtext-ready') || textarea.next('.note-editor').length) {
      return;
    }
    textarea.addClass('backend-richtext-control').attr('data-backend-richtext', 'true').data('backend-richtext-ready', true).summernote({
      height: Number(textarea.data('backend-richtext-height')) || 180,
      toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['font', ['fontsize']], ['para', ['ul', 'ol', 'paragraph']], ['insert', ['link']], ['view', ['codeview']]],
      fontSizes: ['10', '11', '12', '14', '16', '18', '20', '24', '28', '32'],
      dialogsInBody: true
    });
  });
}
function setBackendRichTextValue(element) {
  var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  if (!element) {
    return;
  }
  if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.summernote) {
    element.value = value;
    return;
  }
  var textarea = window.jQuery(element);
  if (textarea.next('.note-editor').length) {
    textarea.summernote('code', value);
    return;
  }
  element.value = value;
}
window.initBackendRichText = initBackendRichText;
window.setBackendRichTextValue = setBackendRichTextValue;
window.initBackendDatePickers = initBackendDatePickers;
window.initBackendRequiredMarkers = initBackendRequiredMarkers;
window.initBackendMoneyInputs = initBackendMoneyInputs;
window.setBackendActionLoading = setBackendActionLoading;
window.showBackendModal = showBackendModal;
window.closeBackendModal = closeBackendModal;
function initBackendSharedForms() {
  var root = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
  initBackendSubmitGuards(root);
  initBackendRequiredMarkers(root);
  initBackendMoneyInputs(root);
  initBackendRichText(root);
  initBackendDatePickers(root);
}
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function () {
    initBackendSharedForms(document);
  });
} else {
  initBackendSharedForms(document);
}
document.addEventListener('shown.bs.modal', function (event) {
  initBackendSharedForms(event.target);
});
document.addEventListener('click', handleBackendActionClick);
document.addEventListener('click', handleBackendStatusToggleClick);
document.addEventListener('click', handleBackendModalClose);
document.addEventListener('change', function (event) {
  if (event.target instanceof Element && event.target.matches('[data-backend-money-unit-source-target]')) {
    initBackendMoneyInputs(event.target.closest('form') || document);
  }
});
window.addEventListener('pageshow', function () {
  document.querySelectorAll('[data-backend-submitting="true"]').forEach(resetBackendSubmittingState);
  initBackendMoneyInputs(document);
});
document.addEventListener('submit', function (event) {
  if (!(event.target instanceof HTMLFormElement)) {
    return;
  }
  var controls = Array.from(event.target.querySelectorAll(BACKEND_MONEY_INPUT_SELECTOR)).filter(function (control) {
    return backendMoneyUnit(control);
  });
  controls.forEach(restoreBackendMoneyInput);
  if (!event.target.checkValidity()) {
    event.preventDefault();
  }
  queueMicrotask(function () {
    if (event.defaultPrevented) {
      controls.forEach(function (control) {
        return formatBackendMoneyInput(control);
      });
    }
  });
}, true);
document.addEventListener('formdata', function (event) {
  if (!(event.target instanceof HTMLFormElement)) {
    return;
  }
  var controlsByName = Array.from(event.target.querySelectorAll(BACKEND_MONEY_INPUT_SELECTOR)).filter(function (control) {
    return control.name && backendMoneyUnit(control);
  }).reduce(function (groups, control) {
    groups[control.name] = [].concat(_toConsumableArray(groups[control.name] || []), [control]);
    return groups;
  }, {});
  Object.entries(controlsByName).forEach(function (_ref) {
    var _ref2 = _slicedToArray(_ref, 2),
      name = _ref2[0],
      controls = _ref2[1];
    event.formData["delete"](name);
    controls.forEach(function (control) {
      event.formData.append(name, backendMoneyRawValue(control));
    });
  });
});
var backendRequiredObserver = new MutationObserver(function (mutations) {
  mutations.forEach(function (mutation) {
    if (mutation.type === 'attributes') {
      initBackendRequiredMarkers(mutation.target);
      return;
    }
    mutation.addedNodes.forEach(function (node) {
      if (node instanceof Element) {
        initBackendSubmitGuards(node);
        initBackendRequiredMarkers(node);
        initBackendMoneyInputs(node);
      }
    });
  });
});
backendRequiredObserver.observe(document.documentElement, {
  attributeFilter: ['required', 'data-backend-money-unit'],
  attributes: true,
  childList: true,
  subtree: true
});
/******/ })()
;