/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/backend/js/operations/hotels/forms.js ***!
  \*********************************************************/
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return generator._invoke = function (innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; }(innerFn, self, context), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; this._invoke = function (method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); }; } function maybeInvokeDelegate(delegate, context) { var method = delegate.iterator[context.method]; if (undefined === method) { if (context.delegate = null, "throw" === context.method) { if (delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method)) return ContinueSentinel; context.method = "throw", context.arg = new TypeError("The iterator does not provide a 'throw' method"); } return ContinueSentinel; } var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) { if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; } return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, define(Gp, "constructor", GeneratorFunctionPrototype), define(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (object) { var keys = []; for (var key in object) { keys.push(key); } return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) { "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); } }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
document.addEventListener('DOMContentLoaded', function () {
  var coverPreviewUrls = new WeakMap();
  var galleryPreviewUrls = new WeakMap();
  var clearCoverPreview = function clearCoverPreview(input, preview) {
    var existingUrl = coverPreviewUrls.get(input);
    if (existingUrl) {
      URL.revokeObjectURL(existingUrl);
      coverPreviewUrls["delete"](input);
    }
    preview === null || preview === void 0 ? void 0 : preview.replaceChildren();
  };
  document.addEventListener('change', function (event) {
    var _input$files;
    var input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.matches('[data-hotel-cover-input]')) {
      return;
    }
    var preview = document.querySelector(input.dataset.hotelCoverPreviewTarget || '[data-hotel-cover-preview]');
    var status = document.querySelector('[data-hotel-cover-status]');
    var file = ((_input$files = input.files) === null || _input$files === void 0 ? void 0 : _input$files[0]) || null;
    clearCoverPreview(input, preview);
    if (!file) {
      if (status) {
        status.textContent = status.dataset.hotelCoverStatusDefault || 'No cover selected';
      }
      return;
    }
    if (!file.type.startsWith('image/')) {
      if (status) {
        status.textContent = 'Selected file is not a valid image';
      }
      return;
    }
    var previewUrl = URL.createObjectURL(file);
    var image = document.createElement('img');
    image.src = previewUrl;
    image.alt = file.name;
    coverPreviewUrls.set(input, previewUrl);
    preview === null || preview === void 0 ? void 0 : preview.replaceChildren(image);
    if (status) {
      status.textContent = file.name;
    }
  });
  var clearGalleryPreview = function clearGalleryPreview(input, preview) {
    var existingUrls = galleryPreviewUrls.get(input) || [];
    existingUrls.forEach(function (url) {
      return URL.revokeObjectURL(url);
    });
    galleryPreviewUrls["delete"](input);
    preview === null || preview === void 0 ? void 0 : preview.replaceChildren();
  };
  document.addEventListener('change', function (event) {
    var input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.matches('[data-hotel-gallery-input]')) {
      return;
    }
    var preview = document.querySelector(input.dataset.hotelGalleryPreviewTarget || '[data-hotel-gallery-preview]');
    var status = document.querySelector(input.dataset.hotelGalleryStatusTarget || '[data-hotel-gallery-status]');
    var files = Array.from(input.files || []);
    clearGalleryPreview(input, preview);
    if (!files.length) {
      if (status) {
        status.textContent = 'No gallery files selected';
      }
      return;
    }
    var previewUrls = [];
    var fragment = document.createDocumentFragment();
    files.slice(0, 12).forEach(function (file) {
      var item = document.createElement('figure');
      var caption = document.createElement('figcaption');
      item.className = 'hotel-gallery-upload-preview__item';
      caption.textContent = file.name;
      if (file.type.startsWith('image/')) {
        var previewUrl = URL.createObjectURL(file);
        var image = document.createElement('img');
        image.src = previewUrl;
        image.alt = file.name;
        previewUrls.push(previewUrl);
        item.appendChild(image);
      }
      item.appendChild(caption);
      fragment.appendChild(item);
    });
    galleryPreviewUrls.set(input, previewUrls);
    preview === null || preview === void 0 ? void 0 : preview.replaceChildren(fragment);
    if (status) {
      status.textContent = "".concat(files.length, " file").concat(files.length === 1 ? '' : 's', " selected");
    }
  });
  var repeater = document.querySelector('[data-hotel-price-repeater]');
  if (repeater) {
    var list = repeater.querySelector('[data-hotel-price-list]');
    var template = repeater.querySelector('[data-hotel-price-template]');
    var addButton = repeater.querySelector('[data-hotel-price-add]');
    addButton === null || addButton === void 0 ? void 0 : addButton.addEventListener('click', function () {
      var _window$initBackendRe, _window, _window$initBackendMo, _window2, _window$initBackendDa, _window3;
      if (!list || !template) {
        return;
      }
      var clone = template.content.firstElementChild.cloneNode(true);
      clone.querySelectorAll('input, select, textarea').forEach(function (field) {
        if (field.type !== 'hidden') {
          field.value = '';
        }
      });
      list.appendChild(clone);
      (_window$initBackendRe = (_window = window).initBackendRequiredMarkers) === null || _window$initBackendRe === void 0 ? void 0 : _window$initBackendRe.call(_window, clone);
      (_window$initBackendMo = (_window2 = window).initBackendMoneyInputs) === null || _window$initBackendMo === void 0 ? void 0 : _window$initBackendMo.call(_window2, clone);
      (_window$initBackendDa = (_window3 = window).initBackendDatePickers) === null || _window$initBackendDa === void 0 ? void 0 : _window$initBackendDa.call(_window3, clone);
    });
    list === null || list === void 0 ? void 0 : list.addEventListener('click', function (event) {
      var _removeButton$closest;
      var removeButton = event.target.closest('[data-hotel-price-remove]');
      if (!removeButton) {
        return;
      }
      var rows = list.querySelectorAll('[data-hotel-price-row]');
      if (rows.length <= 1) {
        return;
      }
      (_removeButton$closest = removeButton.closest('[data-hotel-price-row]')) === null || _removeButton$closest === void 0 ? void 0 : _removeButton$closest.remove();
    });
  }
  document.querySelectorAll('[data-hotel-autocomplete]').forEach(function (input) {
    var target = document.querySelector(input.dataset.hotelAutocompleteTarget || '');
    var resultKey = input.dataset.hotelAutocompleteResults;
    var url = input.dataset.hotelAutocompleteUrl;
    if (!target || !url || !resultKey) {
      return;
    }
    input.addEventListener('keyup', /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var query, endpoint, response, payload, suggestions;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              query = input.value.trim();
              if (!(query.length < 2)) {
                _context.next = 5;
                break;
              }
              target.hidden = true;
              target.innerHTML = '';
              return _context.abrupt("return");
            case 5:
              endpoint = new URL(url, window.location.origin);
              endpoint.searchParams.set('query', query);
              _context.prev = 7;
              _context.next = 10;
              return fetch(endpoint.toString(), {
                headers: {
                  Accept: 'application/json'
                }
              });
            case 10:
              response = _context.sent;
              _context.next = 13;
              return response.json();
            case 13:
              payload = _context.sent;
              suggestions = payload[resultKey] || [];
              target.innerHTML = suggestions.filter(function (item) {
                return item.name;
              }).map(function (item) {
                return "<button type=\"button\" class=\"hotel-form-suggestion\" data-value=\"".concat(item.name, "\">").concat(item.name, "</button>");
              }).join('');
              target.hidden = suggestions.length === 0;
              _context.next = 23;
              break;
            case 19:
              _context.prev = 19;
              _context.t0 = _context["catch"](7);
              target.hidden = true;
              target.innerHTML = '';
            case 23:
            case "end":
              return _context.stop();
          }
        }
      }, _callee, null, [[7, 19]]);
    })));
    target.addEventListener('click', function (event) {
      var item = event.target.closest('[data-value]');
      if (!item) {
        return;
      }
      input.value = item.dataset.value || '';
      target.hidden = true;
    });
    document.addEventListener('click', function (event) {
      if (!input.contains(event.target) && !target.contains(event.target)) {
        target.hidden = true;
      }
    });
  });
});
/******/ })()
;