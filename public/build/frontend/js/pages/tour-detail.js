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
/*!************************************************************!*\
  !*** ./resources/frontend/js/landing-page/tours/detail.js ***!
  \************************************************************/
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return generator._invoke = function (innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; }(innerFn, self, context), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; this._invoke = function (method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); }; } function maybeInvokeDelegate(delegate, context) { var method = delegate.iterator[context.method]; if (undefined === method) { if (context.delegate = null, "throw" === context.method) { if (delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method)) return ContinueSentinel; context.method = "throw", context.arg = new TypeError("The iterator does not provide a 'throw' method"); } return ContinueSentinel; } var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) { if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; } return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, define(Gp, "constructor", GeneratorFunctionPrototype), define(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (object) { var keys = []; for (var key in object) { keys.push(key); } return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) { "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); } }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
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
  var _orderForm$querySelec, _window$bootstrap2;
  var formatCurrency = function formatCurrency(value) {
    var amount = Math.max(Number(value) || 0, 0);
    return "$".concat(amount.toLocaleString('de-DE', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }));
  };
  var mapElement = document.getElementById('tourRouteMap');
  var mapDataElement = document.querySelector('[data-tour-route-locations]');
  var routeMarkers = new Map();
  var routeLocations = [];
  var routeMap = null;
  var routePolyline = null;
  document.querySelectorAll('.tour-gallery-modal').forEach(function (modal) {
    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
    modal.addEventListener('show.bs.modal', function () {
      document.body.classList.add('tour-gallery-modal-open');
      document.documentElement.classList.add('tour-gallery-modal-open');
    });
    modal.addEventListener('hidden.bs.modal', function () {
      if (!document.querySelector('.tour-gallery-modal.show')) {
        document.body.classList.remove('tour-gallery-modal-open');
        document.documentElement.classList.remove('tour-gallery-modal-open');
      }
    });
    modal.addEventListener('click', function (event) {
      var _window$bootstrap;
      if (event.target !== modal || !((_window$bootstrap = window.bootstrap) !== null && _window$bootstrap !== void 0 && _window$bootstrap.Modal)) {
        return;
      }
      window.bootstrap.Modal.getOrCreateInstance(modal).hide();
    });
  });
  document.querySelectorAll('[data-tour-gallery-showcase]').forEach(function (gallery) {
    var mainButton = gallery.querySelector('[data-tour-gallery-main]');
    var mainImage = gallery.querySelector('[data-tour-gallery-main-image]');
    var currentLabel = gallery.querySelector('[data-tour-gallery-current]');
    var captionLabel = gallery.querySelector('[data-tour-gallery-caption]');
    var thumbs = _toConsumableArray(gallery.querySelectorAll('[data-tour-gallery-thumb]'));
    var prevButton = gallery.querySelector('[data-tour-gallery-prev]');
    var nextButton = gallery.querySelector('[data-tour-gallery-next]');
    var activeIndex = thumbs.findIndex(function (thumb) {
      return thumb.classList.contains('is-active');
    });
    if (!mainButton || !mainImage || !thumbs.length) {
      return;
    }
    activeIndex = activeIndex >= 0 ? activeIndex : 0;
    var activateGalleryImage = function activateGalleryImage(nextIndex) {
      var normalizedIndex = (nextIndex + thumbs.length) % thumbs.length;
      var activeThumb = thumbs[normalizedIndex];
      if (!activeThumb) {
        return;
      }
      activeIndex = normalizedIndex;
      thumbs.forEach(function (thumb, index) {
        var isActive = index === activeIndex;
        thumb.classList.toggle('is-active', isActive);
        thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
      var nextImage = activeThumb.dataset.galleryMain;
      var nextModal = activeThumb.dataset.galleryModal;
      if (nextImage && mainImage.getAttribute('src') !== nextImage) {
        mainImage.src = nextImage;
      }
      if (nextModal) {
        mainButton.setAttribute('data-bs-target', nextModal);
      }
      if (currentLabel) {
        currentLabel.textContent = String(activeIndex + 1).padStart(2, '0');
      }
      if (captionLabel) {
        captionLabel.textContent = activeThumb.dataset.galleryCaption || '';
      }
      activeThumb.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'center'
      });
    };
    thumbs.forEach(function (thumb, index) {
      thumb.addEventListener('click', function () {
        return activateGalleryImage(index);
      });
    });
    prevButton === null || prevButton === void 0 ? void 0 : prevButton.addEventListener('click', function () {
      return activateGalleryImage(activeIndex - 1);
    });
    nextButton === null || nextButton === void 0 ? void 0 : nextButton.addEventListener('click', function () {
      return activateGalleryImage(activeIndex + 1);
    });
  });
  var keepActivePopupInView = function keepActivePopupInView() {
    if (!routeMap) {
      return;
    }
    window.requestAnimationFrame(function () {
      var mapContainer = routeMap.getContainer();
      var popup = mapContainer.querySelector('.leaflet-popup');
      if (!popup) {
        return;
      }
      var mapRect = mapContainer.getBoundingClientRect();
      var popupRect = popup.getBoundingClientRect();
      var popupCenterX = popupRect.left + popupRect.width / 2;
      var popupCenterY = popupRect.top + popupRect.height / 2;
      var mapCenterX = mapRect.left + mapRect.width / 2;
      var mapCenterY = mapRect.top + mapRect.height / 2;
      routeMap.panBy([popupCenterX - mapCenterX, popupCenterY - mapCenterY], {
        animate: true,
        duration: 0.28
      });
    });
  };
  var highlightRouteStop = function highlightRouteStop(order) {
    document.querySelectorAll('[data-tour-route-stop]').forEach(function (card) {
      card.classList.toggle('is-active', card.dataset.tourRouteStop === String(order));
    });
    document.querySelectorAll('.tour-route-map__pin.is-highlighted').forEach(function (pin) {
      pin.classList.remove('is-highlighted');
    });
    var marker = routeMarkers.get(String(order));
    if (!marker || !routeMap) {
      return;
    }
    var markerElement = marker.getElement();
    var markerPin = markerElement ? markerElement.querySelector('.tour-route-map__pin') : null;
    if (markerPin) {
      markerPin.classList.add('is-highlighted');
    }
    routeMap.setView(marker.getLatLng(), Math.max(routeMap.getZoom(), 13), {
      animate: true,
      duration: 0.45
    });
    marker.openPopup();
    keepActivePopupInView();
    window.setTimeout(keepActivePopupInView, 480);
  };
  var locationsForDay = function locationsForDay(day) {
    if (day === 'all') {
      return routeLocations;
    }
    return routeLocations.filter(function (location) {
      return String(location.day) === String(day);
    });
  };
  var getMarkerLatLng = function getMarkerLatLng(location) {
    var _location$marker_lat, _location$marker_lng;
    return [Number((_location$marker_lat = location.marker_lat) !== null && _location$marker_lat !== void 0 ? _location$marker_lat : location.lat), Number((_location$marker_lng = location.marker_lng) !== null && _location$marker_lng !== void 0 ? _location$marker_lng : location.lng)];
  };
  var applyMarkerOffsets = function applyMarkerOffsets(locations) {
    var groups = locations.reduce(function (accumulator, location) {
      var key = "".concat(Number(location.lat).toFixed(5), ":").concat(Number(location.lng).toFixed(5));
      if (!accumulator.has(key)) {
        accumulator.set(key, []);
      }
      accumulator.get(key).push(location);
      return accumulator;
    }, new Map());
    groups.forEach(function (group) {
      if (group.length === 1) {
        group[0].marker_lat = group[0].lat;
        group[0].marker_lng = group[0].lng;
        return;
      }
      var centerLat = Number(group[0].lat);
      var centerLng = Number(group[0].lng);
      var radiusMeters = Math.min(16 + group.length * 2, 28);
      var latMeters = 111320;
      var lngMeters = Math.max(111320 * Math.cos(centerLat * Math.PI / 180), 1);
      group.forEach(function (location, index) {
        var angle = Math.PI * 2 * index / group.length - Math.PI / 2;
        location.marker_lat = centerLat + Math.sin(angle) * radiusMeters / latMeters;
        location.marker_lng = centerLng + Math.cos(angle) * radiusMeters / lngMeters;
      });
    });
    return locations;
  };
  var fitRouteLocations = function fitRouteLocations(locations) {
    if (!routeMap || !locations.length) {
      return;
    }
    var bounds = locations.map(getMarkerLatLng);
    if (bounds.length > 1) {
      routeMap.fitBounds(bounds, {
        padding: [48, 48],
        animate: true
      });
      return;
    }
    routeMap.setView(bounds[0], Math.max(routeMap.getZoom(), 13), {
      animate: true,
      duration: 0.35
    });
  };
  var syncRouteDay = function syncRouteDay(day) {
    var shouldFit = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
    if (!routeMap) {
      return;
    }
    var activeDay = day || (mapElement === null || mapElement === void 0 ? void 0 : mapElement.dataset.activeDay) || 'all';
    var activeLocations = locationsForDay(activeDay);
    var activeOrders = new Set(activeLocations.map(function (location) {
      return String(location.order);
    }));
    if (mapElement) {
      mapElement.dataset.activeDay = activeDay;
    }
    routeMap.closePopup();
    document.querySelectorAll('[data-tour-route-day-tab]').forEach(function (tab) {
      var isActive = tab.dataset.tourRouteDayTab === String(activeDay);
      tab.classList.toggle('is-active', isActive);
      tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
    document.querySelectorAll('[data-tour-route-day-panel]').forEach(function (panel) {
      panel.classList.toggle('is-active', panel.dataset.tourRouteDayPanel === String(activeDay));
    });
    document.querySelectorAll('[data-tour-route-stop]').forEach(function (card) {
      card.classList.remove('is-active');
    });
    routeMarkers.forEach(function (marker, order) {
      var shouldShow = activeOrders.has(order);
      if (shouldShow && !routeMap.hasLayer(marker)) {
        marker.addTo(routeMap);
      }
      if (!shouldShow && routeMap.hasLayer(marker)) {
        routeMap.removeLayer(marker);
      }
    });
    if (routePolyline) {
      routeMap.removeLayer(routePolyline);
      routePolyline = null;
    }
    if (activeLocations.length > 1 && window.L) {
      routePolyline = window.L.polyline(activeLocations.map(function (location) {
        return [location.lat, location.lng];
      }), {
        color: '#0f766e',
        opacity: 0.82,
        weight: 4,
        dashArray: '8 10'
      }).addTo(routeMap);
    }
    if (shouldFit) {
      fitRouteLocations(activeLocations);
    }
  };
  var getMarkerHtml = function getMarkerHtml(location) {
    var color = location.color || '#0f766e';
    var displayOrder = location.display_order || location.visit_order || location.order;
    return "<span class=\"tour-route-map__pin tour-route-map__pin--number\" style=\"--tour-marker-color:".concat(color, "\"><strong>").concat(displayOrder, "</strong></span>");
  };
  var createMarkerPopup = function createMarkerPopup(location) {
    var popup = document.createElement('div');
    popup.className = 'tour-route-map__popup-card tour-route-map__popup-card--compact';
    var icon = document.createElement('span');
    icon.className = 'tour-route-map__popup-icon';
    icon.style.setProperty('--tour-marker-color', location.color || '#0f766e');
    icon.innerHTML = "<i class=\"fa ".concat(location.icon || 'fa-landmark', "\" aria-hidden=\"true\"></i>");
    var title = document.createElement('div');
    title.className = 'tour-route-map__popup-title';
    title.textContent = location.visit_time ? "".concat(location.name, " (").concat(location.visit_time, ")") : location.name;
    popup.appendChild(icon);
    popup.appendChild(title);
    return popup;
  };
  if (mapElement && mapDataElement && window.L && mapElement.dataset.initialized !== 'true') {
    var locations = [];
    try {
      locations = JSON.parse(mapDataElement.textContent || '[]');
    } catch (error) {
      locations = [];
    }
    if (locations.length > 0) {
      locations = applyMarkerOffsets(locations);
      mapElement.dataset.initialized = 'true';
      var map = L.map(mapElement, {
        scrollWheelZoom: false,
        zoomControl: true
      });
      routeMap = map;
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
      }).addTo(map);
      locations.forEach(function (location) {
        var latLng = getMarkerLatLng(location);
        var marker = L.marker(latLng, {
          icon: L.divIcon({
            className: '',
            html: getMarkerHtml(location),
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -18]
          })
        }).addTo(map);
        marker._tourRouteLocation = location;
        routeMarkers.set(String(location.order), marker);
        var popup = document.createElement('div');
        var title = document.createElement('div');
        var meta = document.createElement('p');
        title.className = 'tour-route-map__popup-title';
        title.textContent = location.name;
        meta.className = 'tour-route-map__popup-meta';
        meta.textContent = ["".concat(mapElement.dataset.dayLabel || '', " ").concat(location.day), "".concat(mapElement.dataset.stopLabel || '', " ").concat(location.visit_order), location.visit_time ? "".concat(mapElement.dataset.timeLabel || '', " ").concat(location.visit_time) : null].filter(Boolean).join(' · ');
        popup.appendChild(title);
        popup.appendChild(meta);
        marker.bindPopup(createMarkerPopup(location), {
          closeButton: false,
          minWidth: 230,
          autoPan: true,
          autoPanPadding: [28, 28],
          keepInView: true
        });
      });
      routeLocations = locations;
      syncRouteDay(mapElement.dataset.activeDay || 'all', true);
      window.setTimeout(function () {
        return map.invalidateSize();
      }, 250);
    }
  }
  function ensureTourRouteMap() {
    return _ensureTourRouteMap.apply(this, arguments);
  }
  function _ensureTourRouteMap() {
    _ensureTourRouteMap = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
      var locations, fallbackCss, fallback, map;
      return _regeneratorRuntime().wrap(function _callee2$(_context2) {
        while (1) {
          switch (_context2.prev = _context2.next) {
            case 0:
              if (!(!mapElement || !mapDataElement || mapElement.dataset.initialized === 'true')) {
                _context2.next = 2;
                break;
              }
              return _context2.abrupt("return");
            case 2:
              locations = [];
              try {
                locations = JSON.parse(mapDataElement.textContent || '[]');
              } catch (error) {
                locations = [];
              }
              if (locations.length) {
                _context2.next = 6;
                break;
              }
              return _context2.abrupt("return");
            case 6:
              if (window.L) {
                _context2.next = 25;
                break;
              }
              _context2.prev = 7;
              fallbackCss = document.createElement('link');
              fallbackCss.rel = 'stylesheet';
              fallbackCss.href = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css';
              document.head.appendChild(fallbackCss);
              _context2.next = 14;
              return new Promise(function (resolve, reject) {
                var fallbackScript = document.createElement('script');
                fallbackScript.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js';
                fallbackScript.onload = resolve;
                fallbackScript.onerror = reject;
                document.head.appendChild(fallbackScript);
              });
            case 14:
              _context2.next = 25;
              break;
            case 16:
              _context2.prev = 16;
              _context2.t0 = _context2["catch"](7);
              mapElement.classList.add('tour-route-map__canvas--fallback');
              mapElement.innerHTML = '';
              fallback = document.createElement('div');
              fallback.className = 'tour-route-map__fallback';
              locations.forEach(function (location) {
                var item = document.createElement('div');
                item.className = 'tour-route-map__fallback-marker';
                var avatar = document.createElement('span');
                avatar.className = 'tour-route-map__pin';
                var number = document.createElement('strong');
                number.textContent = location.order;
                var body = document.createElement('div');
                var title = document.createElement('p');
                var meta = document.createElement('small');
                title.textContent = location.name;
                meta.textContent = "".concat(mapElement.dataset.dayLabel || '', " ").concat(location.day, " - ").concat(mapElement.dataset.stopLabel || '', " ").concat(location.visit_order);
                avatar.classList.add('tour-route-map__pin--number');
                avatar.style.setProperty('--tour-marker-color', location.color || '#0f766e');
                avatar.appendChild(number);
                body.appendChild(title);
                body.appendChild(meta);
                item.appendChild(avatar);
                item.appendChild(body);
                fallback.appendChild(item);
              });
              mapElement.appendChild(fallback);
              return _context2.abrupt("return");
            case 25:
              if (window.L) {
                _context2.next = 27;
                break;
              }
              return _context2.abrupt("return");
            case 27:
              mapElement.dataset.initialized = 'true';
              mapElement.innerHTML = '';
              map = L.map(mapElement, {
                scrollWheelZoom: false,
                zoomControl: true
              });
              routeMap = map;
              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
              }).addTo(map);
              locations = applyMarkerOffsets(locations);
              locations.forEach(function (location) {
                var latLng = getMarkerLatLng(location);
                var marker = L.marker(latLng, {
                  icon: L.divIcon({
                    className: '',
                    html: getMarkerHtml(location),
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -18]
                  })
                }).addTo(map);
                marker._tourRouteLocation = location;
                routeMarkers.set(String(location.order), marker);
                var popup = document.createElement('div');
                var title = document.createElement('div');
                var meta = document.createElement('p');
                title.className = 'tour-route-map__popup-title';
                title.textContent = location.name;
                meta.className = 'tour-route-map__popup-meta';
                meta.textContent = ["".concat(mapElement.dataset.dayLabel || '', " ").concat(location.day), "".concat(mapElement.dataset.stopLabel || '', " ").concat(location.visit_order), location.visit_time ? "".concat(mapElement.dataset.timeLabel || '', " ").concat(location.visit_time) : null].filter(Boolean).join(' - ');
                popup.appendChild(title);
                popup.appendChild(meta);
                marker.bindPopup(createMarkerPopup(location), {
                  closeButton: false,
                  minWidth: 230,
                  autoPan: true,
                  autoPanPadding: [28, 28],
                  keepInView: true
                });
              });
              routeLocations = locations;
              syncRouteDay(mapElement.dataset.activeDay || 'all', true);
              window.setTimeout(function () {
                return map.invalidateSize();
              }, 250);
            case 37:
            case "end":
              return _context2.stop();
          }
        }
      }, _callee2, null, [[7, 16]]);
    }));
    return _ensureTourRouteMap.apply(this, arguments);
  }
  ensureTourRouteMap();
  document.addEventListener('click', function (event) {
    var dayTab = event.target.closest('[data-tour-route-day-tab]');
    if (dayTab) {
      syncRouteDay(dayTab.dataset.tourRouteDayTab, true);
      window.setTimeout(function () {
        var _routeMap;
        return (_routeMap = routeMap) === null || _routeMap === void 0 ? void 0 : _routeMap.invalidateSize();
      }, 80);
      return;
    }
    var stopCard = event.target.closest('[data-tour-route-stop]');
    if (!stopCard) {
      return;
    }
    highlightRouteStop(stopCard.dataset.tourRouteStop);
  });
  var orderForm = document.querySelector('[data-tour-order-form]');
  var reservationModalElement = document.getElementById('tourReservationModal');
  if (!orderForm) {
    return;
  }
  var travelDateInput = orderForm.querySelector('[name="travel_date"]');
  var selectedPriceId = orderForm.querySelector('[data-tour-price-id]');
  var pricePerPaxTargets = _toConsumableArray(orderForm.querySelectorAll('[data-tour-price-per-pax]'));
  var totalPriceTargets = _toConsumableArray(orderForm.querySelectorAll('[data-tour-total-price]'));
  var priceNoteTargets = _toConsumableArray(orderForm.querySelectorAll('[data-tour-price-note]'));
  var priceCardLabel = orderForm.querySelector('[data-tour-price-card-label]');
  var priceCardValue = orderForm.querySelector('[data-tour-price-card-value]');
  var submitButton = orderForm.querySelector('button[type="submit"]');
  var guestError = orderForm.querySelector('[data-tour-guest-error]');
  var guestTableBody = orderForm.querySelector('[data-tour-guest-table-body]');
  var guestEmptyRow = orderForm.querySelector('[data-tour-guest-empty-row]');
  var guestInputsTarget = orderForm.querySelector('[data-tour-guest-inputs]');
  var guestProgressTarget = orderForm.querySelector('[data-tour-guest-progress]');
  var reviewGuestTableBody = orderForm.querySelector('[data-tour-review-guest-table-body]');
  var reviewGuestEmptyRow = orderForm.querySelector('[data-tour-review-guest-empty-row]');
  var guestEditIndexInput = orderForm.querySelector('[data-tour-guest-edit-index]');
  var guestSaveButton = orderForm.querySelector('[data-tour-guest-save]');
  var guestCancelButton = orderForm.querySelector('[data-tour-guest-cancel]');
  var guestFieldElements = {
    name: orderForm.querySelector('[data-tour-guest-field="name"]'),
    phone: orderForm.querySelector('[data-tour-guest-field="phone"]'),
    age: orderForm.querySelector('[data-tour-guest-field="age"]'),
    sex: orderForm.querySelector('[data-tour-guest-field="sex"]')
  };
  var reviewFields = _toConsumableArray(orderForm.querySelectorAll('[data-tour-review-field]'));
  var reviewValues = _toConsumableArray(orderForm.querySelectorAll('[data-tour-review-value]'));
  var wizardSteps = _toConsumableArray(orderForm.querySelectorAll('[data-tour-wizard-step]'));
  var wizardNavItems = _toConsumableArray(orderForm.querySelectorAll('[data-tour-wizard-nav]'));
  var previousStepButtons = _toConsumableArray(orderForm.querySelectorAll('[data-tour-wizard-prev]'));
  var nextStepButtons = _toConsumableArray(orderForm.querySelectorAll('[data-tour-wizard-next]'));
  var priceRequiredNextButtons = _toConsumableArray(orderForm.querySelectorAll('[data-tour-requires-price]'));
  var wizardSubmitButtons = _toConsumableArray(orderForm.querySelectorAll('[data-tour-wizard-submit]'));
  var wizardSubmitButton = wizardSubmitButtons[0] || null;
  var submitOverlay = orderForm.querySelector('[data-form-submit-overlay]');
  var quoteUrl = orderForm.dataset.quoteUrl || '';
  var csrfToken = ((_orderForm$querySelec = orderForm.querySelector('[name="_token"]')) === null || _orderForm$querySelec === void 0 ? void 0 : _orderForm$querySelec.value) || '';
  var submissionGuard = createFormSubmissionGuard(orderForm, {
    storageKey: orderForm.dataset.submissionKey || "tour-order:".concat(window.location.pathname)
  });
  var guestLabel = orderForm.dataset.guestLabel || '';
  var adultLabel = orderForm.dataset.adultLabel || '';
  var childLabel = orderForm.dataset.childLabel || '';
  var maleLabel = orderForm.dataset.maleLabel || '';
  var femaleLabel = orderForm.dataset.femaleLabel || '';
  var editLabel = orderForm.dataset.editLabel || '';
  var removeLabel = orderForm.dataset.removeLabel || '';
  var addGuestLabel = orderForm.dataset.addGuestLabel || '';
  var updateGuestLabel = orderForm.dataset.updateGuestLabel || '';
  var cancelEditLabel = orderForm.dataset.cancelEditLabel || '';
  var guestTableEmptyLabel = orderForm.dataset.guestTableEmptyLabel || '';
  var guestProgressLabel = orderForm.dataset.guestProgressLabel || '';
  var guestSummaryLabel = orderForm.dataset.guestSummaryLabel || '';
  var guestCountMismatchLabel = orderForm.dataset.guestCountMismatchLabel || '';
  var priceUnavailableLabel = orderForm.dataset.priceUnavailableLabel || orderForm.dataset.noRateLabel || '';
  var priceUnavailableOnDateLabel = orderForm.dataset.priceUnavailableOnDateLabel || priceUnavailableLabel;
  var priceFromLabel = orderForm.dataset.priceFromLabel || '';
  var pricePaxSuffix = orderForm.dataset.pricePaxSuffix || '';
  var loadingPriceLabel = orderForm.dataset.loadingPriceLabel || '';
  var initialPriceCardValue = (priceCardValue === null || priceCardValue === void 0 ? void 0 : priceCardValue.textContent) || '-';
  var minGuests = Number(orderForm.dataset.minGuests || 2);
  var maxGuests = Number(orderForm.dataset.maxGuests || 200);
  var guests = [];
  var isSubmitting = false;
  var quoteState = {
    fingerprint: '',
    available: false,
    loading: false
  };
  try {
    guests = JSON.parse(orderForm.dataset.initialGuests || '[]').filter(function (guest) {
      return Object.values(guest || {}).some(function (value) {
        return value !== null && value !== '';
      });
    }).map(function (guest) {
      return {
        name: String(guest.name || '').trim(),
        phone: String(guest.phone || '').trim(),
        age: String(guest.age || '').trim(),
        sex: String(guest.sex || '').trim()
      };
    });
  } catch (error) {
    guests = [];
  }
  var activeWizardStep = 0;
  var focusFirstInvalidField = function focusFirstInvalidField(container) {
    var invalidField = container === null || container === void 0 ? void 0 : container.querySelector('.is-invalid, :invalid');
    if (invalidField && typeof invalidField.focus === 'function') {
      invalidField.focus({
        preventScroll: false
      });
    }
  };
  if (reservationModalElement && orderForm.dataset.openOnLoad === 'true' && (_window$bootstrap2 = window.bootstrap) !== null && _window$bootstrap2 !== void 0 && _window$bootstrap2.Modal) {
    window.setTimeout(function () {
      window.bootstrap.Modal.getOrCreateInstance(reservationModalElement).show();
    }, 120);
  }
  var setSubmittingState = function setSubmittingState(submitting) {
    var processingLabel = orderForm.dataset.processingLabel || '';
    isSubmitting = Boolean(submitting);
    orderForm.dataset.isSubmitting = isSubmitting ? 'true' : 'false';
    orderForm.setAttribute('aria-busy', isSubmitting ? 'true' : 'false');
    orderForm.toggleAttribute('inert', isSubmitting);
    document.documentElement.classList.toggle('tour-submit-locked', isSubmitting);
    document.body.classList.toggle('tour-submit-locked', isSubmitting);
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
    [].concat(_toConsumableArray(previousStepButtons), _toConsumableArray(nextStepButtons), _toConsumableArray(wizardSubmitButtons)).forEach(function (button) {
      var originalHtml = button.dataset.originalHtml || button.innerHTML;
      button.dataset.originalHtml = originalHtml;
      button.disabled = isSubmitting;
      button.classList.toggle('is-processing', isSubmitting && button === wizardSubmitButton);
      if (button !== wizardSubmitButton) {
        return;
      }
      button.innerHTML = isSubmitting ? '<span class="frontend-action-spinner" aria-hidden="true"></span><span>' + processingLabel + '</span>' : originalHtml;
    });
  };
  var attemptOrderSubmit = function attemptOrderSubmit() {
    if (isSubmitting) {
      return;
    }
    var _loop = function _loop(index) {
      if (!validateWizardStep(index, false)) {
        showWizardStep(index);
        window.setTimeout(function () {
          return focusFirstInvalidField(wizardSteps[index]);
        }, 80);
        return {
          v: void 0
        };
      }
    };
    for (var index = 0; index < wizardSteps.length; index += 1) {
      var _ret = _loop(index);
      if (_typeof(_ret) === "object") return _ret.v;
    }
    if (!hasValidQuoteForCurrentInput()) {
      showWizardStep(wizardSteps.length - 1);
      updatePricePreview();
      return;
    }
    if (wizardSubmitButton !== null && wizardSubmitButton !== void 0 && wizardSubmitButton.disabled) {
      showWizardStep(wizardSteps.length - 1);
      return;
    }
    setSubmittingState(true);
    submissionGuard.markSubmitted();
    var submitForm = function submitForm() {
      if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype.submit) {
        HTMLFormElement.prototype.submit.call(orderForm);
        return;
      }
      orderForm.submit();
    };
    if (typeof window.requestAnimationFrame === 'function') {
      window.requestAnimationFrame(function () {
        window.setTimeout(submitForm, 0);
      });
      return;
    }
    window.setTimeout(submitForm, 0);
  };
  var validateWizardStep = function validateWizardStep(stepIndex) {
    var focusInvalid = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
    var step = wizardSteps[stepIndex];
    if (!step) {
      return true;
    }
    var isValid = true;
    var fields = _toConsumableArray(step.querySelectorAll('input, select, textarea')).filter(function (field) {
      return !field.disabled && field.type !== 'hidden';
    });
    fields.forEach(function (field) {
      var fieldIsValid = field.checkValidity();
      field.classList.toggle('is-invalid', !fieldIsValid);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    if (step !== null && step !== void 0 && step.querySelector('[data-tour-guest-table-body]')) {
      isValid = validateGuestManifest(true) && isValid;
    }
    if (stepIndex === 0 && isValid && !hasValidQuoteForCurrentInput()) {
      updatePricePreview();
      isValid = false;
    }
    if (stepIndex === 1 && isValid && !hasValidQuoteForCurrentInput()) {
      updatePricePreview();
      setGuestErrorMessage(quoteState.loading ? loadingPriceLabel : priceUnavailableLabel, true);
      isValid = false;
    }
    if (!isValid && focusInvalid) {
      focusFirstInvalidField(step);
    }
    return isValid;
  };
  var showWizardStep = function showWizardStep(stepIndex) {
    var _wizardSteps$activeWi;
    if (!wizardSteps.length) {
      return;
    }
    activeWizardStep = Math.min(Math.max(stepIndex, 0), wizardSteps.length - 1);
    wizardSteps.forEach(function (step, index) {
      var isActive = index === activeWizardStep;
      step.hidden = !isActive;
      step.classList.toggle('is-active', isActive);
    });
    wizardNavItems.forEach(function (item, index) {
      item.classList.toggle('is-active', index === activeWizardStep);
      item.classList.toggle('is-complete', index < activeWizardStep);
    });
    if (activeWizardStep === wizardSteps.length - 1) {
      updateReservationReview();
      updatePricePreview();
    }
    (_wizardSteps$activeWi = wizardSteps[activeWizardStep]) === null || _wizardSteps$activeWi === void 0 ? void 0 : _wizardSteps$activeWi.scrollIntoView({
      block: 'start',
      behavior: 'smooth'
    });
  };
  orderForm.addEventListener('input', function (event) {
    if (event.target.matches('input, select, textarea')) {
      event.target.classList.remove('is-invalid');
    }
  });
  nextStepButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var currentPanel = button.closest('[data-tour-wizard-step]');
      var currentStep = currentPanel ? wizardSteps.indexOf(currentPanel) : activeWizardStep;
      if (!validateWizardStep(currentStep)) {
        return;
      }
      showWizardStep(currentStep + 1);
    });
  });
  previousStepButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var currentPanel = button.closest('[data-tour-wizard-step]');
      var currentStep = currentPanel ? wizardSteps.indexOf(currentPanel) : activeWizardStep;
      showWizardStep(currentStep - 1);
    });
  });
  wizardNavItems.forEach(function (item) {
    item.addEventListener('click', function () {
      var targetStep = Number(item.dataset.tourWizardNav || 0);
      if (targetStep <= activeWizardStep) {
        showWizardStep(targetStep);
        return;
      }
      var _loop2 = function _loop2(index) {
        if (!validateWizardStep(index, false)) {
          showWizardStep(index);
          window.setTimeout(function () {
            return focusFirstInvalidField(wizardSteps[index]);
          }, 80);
          return {
            v: void 0
          };
        }
      };
      for (var index = activeWizardStep; index < targetStep; index += 1) {
        var _ret2 = _loop2(index);
        if (_typeof(_ret2) === "object") return _ret2.v;
      }
      showWizardStep(targetStep);
    });
  });
  var escapeHtml = function escapeHtml(value) {
    return String(value !== null && value !== void 0 ? value : '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
  };
  var localizeGuestAge = function localizeGuestAge(value) {
    return value === 'Adult' ? adultLabel : value === 'Child' ? childLabel : value;
  };
  var localizeGuestSex = function localizeGuestSex(value) {
    return value === 'Male' ? maleLabel : value === 'Female' ? femaleLabel : value;
  };
  var getEditingIndex = function getEditingIndex() {
    var value = (guestEditIndexInput === null || guestEditIndexInput === void 0 ? void 0 : guestEditIndexInput.value) || '';
    return value === '' ? null : Number(value);
  };
  var setEditingIndex = function setEditingIndex(index) {
    if (guestEditIndexInput) {
      guestEditIndexInput.value = Number.isInteger(index) ? String(index) : '';
    }
  };
  var clearGuestFormErrors = function clearGuestFormErrors() {
    Object.values(guestFieldElements).forEach(function (field) {
      return field === null || field === void 0 ? void 0 : field.classList.remove('is-invalid');
    });
  };
  var resetGuestForm = function resetGuestForm() {
    clearGuestFormErrors();
    Object.values(guestFieldElements).forEach(function (field) {
      if (!field) return;
      field.value = '';
    });
    setEditingIndex(null);
    if (guestSaveButton) guestSaveButton.textContent = addGuestLabel;
    if (guestCancelButton) {
      guestCancelButton.textContent = cancelEditLabel;
      guestCancelButton.hidden = true;
    }
  };
  var fillGuestForm = function fillGuestForm() {
    var guest = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    Object.entries(guestFieldElements).forEach(function (_ref) {
      var _ref2 = _slicedToArray(_ref, 2),
        key = _ref2[0],
        field = _ref2[1];
      if (!field) return;
      field.value = guest[key] || '';
    });
  };
  var getGuestDraft = function getGuestDraft() {
    var _guestFieldElements$n, _guestFieldElements$p, _guestFieldElements$a, _guestFieldElements$s;
    return {
      name: String(((_guestFieldElements$n = guestFieldElements.name) === null || _guestFieldElements$n === void 0 ? void 0 : _guestFieldElements$n.value) || '').trim(),
      phone: String(((_guestFieldElements$p = guestFieldElements.phone) === null || _guestFieldElements$p === void 0 ? void 0 : _guestFieldElements$p.value) || '').trim(),
      age: String(((_guestFieldElements$a = guestFieldElements.age) === null || _guestFieldElements$a === void 0 ? void 0 : _guestFieldElements$a.value) || '').trim(),
      sex: String(((_guestFieldElements$s = guestFieldElements.sex) === null || _guestFieldElements$s === void 0 ? void 0 : _guestFieldElements$s.value) || '').trim()
    };
  };
  var validateGuestDraft = function validateGuestDraft() {
    var focusInvalid = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var draft = getGuestDraft();
    var requiredFields = ['name', 'age', 'sex'];
    var firstInvalidField = null;
    clearGuestFormErrors();
    requiredFields.forEach(function (fieldName) {
      if (!draft[fieldName] && guestFieldElements[fieldName]) {
        guestFieldElements[fieldName].classList.add('is-invalid');
        firstInvalidField = firstInvalidField || guestFieldElements[fieldName];
      }
    });
    if (focusInvalid && firstInvalidField) firstInvalidField.focus();
    return !firstInvalidField;
  };
  var setGuestErrorMessage = function setGuestErrorMessage() {
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    var visible = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    if (!guestError) return;
    guestError.hidden = !visible;
    if (visible) guestError.textContent = message;
  };
  var renderGuestProgress = function renderGuestProgress() {
    if (!guestProgressTarget) return;
    guestProgressTarget.textContent = guestProgressLabel.replace(':count', String(guests.length)).replace(':min', String(minGuests));
  };
  var renderGuestHiddenInputs = function renderGuestHiddenInputs() {
    if (!guestInputsTarget) return;
    guestInputsTarget.innerHTML = guests.map(function (guest, index) {
      return "\n            <input type=\"hidden\" name=\"guests[".concat(index, "][name]\" value=\"").concat(escapeHtml(guest.name), "\">\n            <input type=\"hidden\" name=\"guests[").concat(index, "][phone]\" value=\"").concat(escapeHtml(guest.phone), "\">\n            <input type=\"hidden\" name=\"guests[").concat(index, "][age]\" value=\"").concat(escapeHtml(guest.age), "\">\n            <input type=\"hidden\" name=\"guests[").concat(index, "][sex]\" value=\"").concat(escapeHtml(guest.sex), "\">\n        ");
    }).join('');
  };
  var renderGuestTable = function renderGuestTable() {
    if (!guestTableBody) return;
    guestTableBody.querySelectorAll('[data-tour-guest-row]').forEach(function (row) {
      return row.remove();
    });
    guests.forEach(function (guest, index) {
      var row = document.createElement('tr');
      row.setAttribute('data-tour-guest-row', 'true');
      row.innerHTML = "\n                <td>".concat(index + 1, "</td>\n                <td>").concat(escapeHtml(guest.name || "".concat(guestLabel, " ").concat(index + 1)), "</td>\n                <td>").concat(escapeHtml(localizeGuestAge(guest.age) || '-'), "</td>\n                <td>").concat(escapeHtml(localizeGuestSex(guest.sex) || '-'), "</td>\n                <td>").concat(escapeHtml(guest.phone || '-'), "</td>\n                <td><div class=\"tour-guest-table__actions\">\n                    <button type=\"button\" class=\"tour-guest-table__action\" data-tour-guest-edit=\"").concat(index, "\"><i class=\"fa fa-edit\" aria-hidden=\"true\"></i><span>").concat(escapeHtml(editLabel), "</span></button>\n                    <button type=\"button\" class=\"tour-guest-table__action tour-guest-table__action--danger\" data-tour-guest-remove=\"").concat(index, "\"><i class=\"fa fa-trash-alt\" aria-hidden=\"true\"></i><span>").concat(escapeHtml(removeLabel), "</span></button>\n                </div></td>\n            ");
      guestTableBody.appendChild(row);
    });
    if (guestEmptyRow) {
      guestEmptyRow.hidden = guests.length > 0;
      var emptyCell = guestEmptyRow.querySelector('td');
      if (emptyCell) emptyCell.textContent = guestTableEmptyLabel;
    }
    renderGuestHiddenInputs();
    renderGuestProgress();
  };
  var renderReviewGuestTable = function renderReviewGuestTable() {
    if (!reviewGuestTableBody) return;
    reviewGuestTableBody.querySelectorAll('[data-tour-review-guest-row]').forEach(function (row) {
      return row.remove();
    });
    guests.forEach(function (guest, index) {
      var row = document.createElement('tr');
      row.setAttribute('data-tour-review-guest-row', 'true');
      row.innerHTML = "\n                <td>".concat(index + 1, "</td>\n                <td>").concat(escapeHtml(guest.name || "".concat(guestLabel, " ").concat(index + 1)), "</td>\n                <td>").concat(escapeHtml(localizeGuestAge(guest.age) || '-'), "</td>\n                <td>").concat(escapeHtml(localizeGuestSex(guest.sex) || '-'), "</td>\n                <td>").concat(escapeHtml(guest.phone || '-'), "</td>\n            ");
      reviewGuestTableBody.appendChild(row);
    });
    if (reviewGuestEmptyRow) {
      reviewGuestEmptyRow.hidden = guests.length > 0;
      var emptyCell = reviewGuestEmptyRow.querySelector('td');
      if (emptyCell) emptyCell.textContent = guestTableEmptyLabel;
    }
  };
  var formatDateTime = function formatDateTime(value) {
    if (!value) {
      return '-';
    }
    var parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
      return value;
    }
    var pad = function pad(part) {
      return String(part).padStart(2, '0');
    };
    return [parsed.getFullYear(), pad(parsed.getMonth() + 1), pad(parsed.getDate())].join('-') + ' ' + [pad(parsed.getHours()), pad(parsed.getMinutes())].join(':');
  };
  var validateGuestManifest = function validateGuestManifest() {
    var showMessage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var isValid = guests.length >= minGuests && guests.length <= maxGuests;
    setGuestErrorMessage(guestCountMismatchLabel, showMessage && !isValid);
    return isValid;
  };
  var updateReservationReview = function updateReservationReview() {
    var valueMap = reviewFields.reduce(function (values, field) {
      values[field.dataset.tourReviewField] = field.dataset.tourReviewFormat === 'datetime' ? formatDateTime(field.value) : field.value || '-';
      return values;
    }, {});
    valueMap.guestCount = String(guests.length);
    valueMap.guestManifest = guests.length ? guestSummaryLabel.replace(':count', String(guests.length)) : '-';
    reviewValues.forEach(function (target) {
      target.textContent = valueMap[target.dataset.tourReviewValue] || '-';
    });
    renderReviewGuestTable();
    renderGuestProgress();
  };
  var persistGuestDraft = function persistGuestDraft() {
    if (!validateGuestDraft(true)) return;
    var editingIndex = getEditingIndex();
    var draft = getGuestDraft();
    if (editingIndex !== null && guests[editingIndex]) guests[editingIndex] = draft;else guests.push(draft);
    resetGuestForm();
    setGuestErrorMessage('', false);
    renderGuestTable();
    updateReservationReview();
    updatePricePreview();
  };
  guestSaveButton === null || guestSaveButton === void 0 ? void 0 : guestSaveButton.addEventListener('click', persistGuestDraft);
  guestCancelButton === null || guestCancelButton === void 0 ? void 0 : guestCancelButton.addEventListener('click', resetGuestForm);
  guestTableBody === null || guestTableBody === void 0 ? void 0 : guestTableBody.addEventListener('click', function (event) {
    var editButton = event.target.closest('[data-tour-guest-edit]');
    var removeButton = event.target.closest('[data-tour-guest-remove]');
    if (editButton) {
      var _guestFieldElements$n2;
      var index = Number(editButton.dataset.tourGuestEdit);
      if (!Number.isInteger(index) || !guests[index]) return;
      setEditingIndex(index);
      fillGuestForm(guests[index]);
      if (guestSaveButton) guestSaveButton.textContent = updateGuestLabel;
      if (guestCancelButton) guestCancelButton.hidden = false;
      (_guestFieldElements$n2 = guestFieldElements.name) === null || _guestFieldElements$n2 === void 0 ? void 0 : _guestFieldElements$n2.focus();
      return;
    }
    if (removeButton) {
      var _index = Number(removeButton.dataset.tourGuestRemove);
      if (!Number.isInteger(_index) || !guests[_index]) return;
      guests.splice(_index, 1);
      resetGuestForm();
      renderGuestTable();
      updateReservationReview();
      validateGuestManifest(false);
      updatePricePreview();
    }
  });
  reviewFields.forEach(function (field) {
    field.addEventListener('input', updateReservationReview);
    field.addEventListener('change', updateReservationReview);
  });
  wizardSubmitButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      attemptOrderSubmit();
    });
  });
  var quoteRequestController = null;
  var quoteRequestTimer = null;
  var quotedGuestCount = function quotedGuestCount() {
    return guests.length >= minGuests ? guests.length : minGuests;
  };
  var quoteFingerprint = function quoteFingerprint() {
    var _orderForm$querySelec2, _orderForm$querySelec3;
    var bookingCode = ((_orderForm$querySelec2 = orderForm.querySelector('[name="booking_code"]')) === null || _orderForm$querySelec2 === void 0 ? void 0 : _orderForm$querySelec2.value) || '';
    var promotionId = ((_orderForm$querySelec3 = orderForm.querySelector('[name="promotion_id"]')) === null || _orderForm$querySelec3 === void 0 ? void 0 : _orderForm$querySelec3.value) || '';
    return [String(quotedGuestCount()), String((travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.value) || '').trim(), String(bookingCode).trim(), String(promotionId).trim()].join('|');
  };
  var hasValidQuoteForCurrentInput = function hasValidQuoteForCurrentInput() {
    return quoteState.available && !quoteState.loading && quoteState.fingerprint === quoteFingerprint() && Boolean(selectedPriceId === null || selectedPriceId === void 0 ? void 0 : selectedPriceId.value);
  };
  var setTextTargets = function setTextTargets(targets, value) {
    targets.forEach(function (target) {
      target.textContent = value;
    });
  };
  var currentTravelDateLabel = function currentTravelDateLabel() {
    var travelDate = String((travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.value) || '').trim();
    return travelDate ? formatDateTime(travelDate) : '';
  };
  var unavailablePriceCardLabel = function unavailablePriceCardLabel() {
    var travelDate = currentTravelDateLabel();
    if (!travelDate) {
      return priceFromLabel;
    }
    return priceUnavailableOnDateLabel.replace(':date', travelDate);
  };
  var renderPriceCardUnavailable = function renderPriceCardUnavailable() {
    if (priceCardLabel) priceCardLabel.textContent = unavailablePriceCardLabel();
    if (priceCardValue) priceCardValue.textContent = currentTravelDateLabel() ? '-' : initialPriceCardValue;
  };
  var renderPriceCardLoading = function renderPriceCardLoading() {
    if (priceCardLabel) {
      priceCardLabel.textContent = currentTravelDateLabel() ? loadingPriceLabel || priceFromLabel : priceFromLabel;
    }
    if (priceCardValue) priceCardValue.textContent = currentTravelDateLabel() ? '-' : initialPriceCardValue;
  };
  var renderPriceCardAvailable = function renderPriceCardAvailable() {
    var unitPriceUsd = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    if (priceCardLabel) priceCardLabel.textContent = priceFromLabel;
    if (priceCardValue) priceCardValue.textContent = unitPriceUsd ? "USD ".concat(unitPriceUsd).concat(pricePaxSuffix) : initialPriceCardValue;
  };
  var syncPriceControls = function syncPriceControls() {
    var hasValidQuote = hasValidQuoteForCurrentInput();
    priceRequiredNextButtons.forEach(function (button) {
      button.disabled = isSubmitting || !hasValidQuote;
    });
    if (submitButton) {
      submitButton.disabled = isSubmitting || !hasValidQuote;
    }
  };
  var setQuoteUnavailable = function setQuoteUnavailable() {
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    quoteState = {
      fingerprint: quoteFingerprint(),
      available: false,
      loading: false
    };
    renderPriceCardUnavailable();
    renderUnavailablePrice(message);
  };
  var setQuoteLoading = function setQuoteLoading() {
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    quoteState = {
      fingerprint: quoteFingerprint(),
      available: false,
      loading: true
    };
    renderPriceCardLoading();
    renderUnavailablePrice(message);
  };
  var renderUnavailablePrice = function renderUnavailablePrice() {
    var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    setTextTargets(pricePerPaxTargets, '-');
    setTextTargets(totalPriceTargets, '-');
    setTextTargets(priceNoteTargets, message || priceUnavailableLabel);
    if (selectedPriceId) selectedPriceId.value = '';
    syncPriceControls();
  };
  var requestPricePreview = /*#__PURE__*/function () {
    var _ref3 = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var _quoteRequestControll, _orderForm$querySelec4, _orderForm$querySelec5;
      var guestCount, travelDate, requestFingerprint, requestBody, bookingCode, promotionId, response, responsePayload, payload;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              guestCount = quotedGuestCount();
              travelDate = String((travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.value) || '').trim();
              if (!(!quoteUrl || guestCount > maxGuests || guests.length > maxGuests || !travelDate)) {
                _context.next = 5;
                break;
              }
              setQuoteUnavailable(guests.length > maxGuests ? guestCountMismatchLabel : '');
              return _context.abrupt("return");
            case 5:
              (_quoteRequestControll = quoteRequestController) === null || _quoteRequestControll === void 0 ? void 0 : _quoteRequestControll.abort();
              quoteRequestController = new AbortController();
              requestFingerprint = quoteFingerprint();
              setQuoteLoading(loadingPriceLabel);
              requestBody = new URLSearchParams({
                number_of_guests: String(guestCount),
                travel_date: travelDate
              });
              bookingCode = (_orderForm$querySelec4 = orderForm.querySelector('[name="booking_code"]')) === null || _orderForm$querySelec4 === void 0 ? void 0 : _orderForm$querySelec4.value;
              promotionId = (_orderForm$querySelec5 = orderForm.querySelector('[name="promotion_id"]')) === null || _orderForm$querySelec5 === void 0 ? void 0 : _orderForm$querySelec5.value;
              if (bookingCode) requestBody.set('booking_code', bookingCode);
              if (promotionId) requestBody.set('promotion_id', promotionId);
              _context.prev = 14;
              _context.next = 17;
              return fetch(quoteUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                  Accept: 'application/json',
                  'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                  'X-CSRF-TOKEN': csrfToken
                },
                body: requestBody.toString(),
                signal: quoteRequestController.signal
              });
            case 17:
              response = _context.sent;
              _context.next = 20;
              return response.json();
            case 20:
              responsePayload = _context.sent;
              // Accept the previous Laravel Resource wrapper while cached assets/responses expire.
              payload = (responsePayload === null || responsePayload === void 0 ? void 0 : responsePayload.data) || responsePayload;
              if (!(!response.ok || payload.price_available !== true || !payload.quote || !payload.display)) {
                _context.next = 25;
                break;
              }
              setQuoteUnavailable(payload.message || priceUnavailableLabel);
              return _context.abrupt("return");
            case 25:
              if (!(requestFingerprint !== quoteFingerprint())) {
                _context.next = 27;
                break;
              }
              return _context.abrupt("return");
            case 27:
              if (payload.quote.price_id) {
                _context.next = 30;
                break;
              }
              setQuoteUnavailable(priceUnavailableLabel);
              return _context.abrupt("return");
            case 30:
              if (selectedPriceId) selectedPriceId.value = payload.quote.price_id || '';
              setTextTargets(pricePerPaxTargets, "USD ".concat(payload.display.unit_price_usd));
              setTextTargets(totalPriceTargets, "USD ".concat(payload.display.final_total_usd));
              setTextTargets(priceNoteTargets, '');
              renderPriceCardAvailable(payload.display.unit_price_usd);
              setGuestErrorMessage('', false);
              quoteState = {
                fingerprint: requestFingerprint,
                available: Boolean(payload.quote.price_id),
                loading: false
              };
              syncPriceControls();
              _context.next = 43;
              break;
            case 40:
              _context.prev = 40;
              _context.t0 = _context["catch"](14);
              if (_context.t0.name !== 'AbortError') {
                setQuoteUnavailable(priceUnavailableLabel);
              }
            case 43:
            case "end":
              return _context.stop();
          }
        }
      }, _callee, null, [[14, 40]]);
    }));
    return function requestPricePreview() {
      return _ref3.apply(this, arguments);
    };
  }();
  var updatePricePreview = function updatePricePreview() {
    quoteState = {
      fingerprint: quoteFingerprint(),
      available: false,
      loading: false
    };
    if (selectedPriceId) selectedPriceId.value = '';
    renderPriceCardLoading();
    renderUnavailablePrice(loadingPriceLabel);
    window.clearTimeout(quoteRequestTimer);
    quoteRequestTimer = window.setTimeout(requestPricePreview, 250);
  };
  travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.addEventListener('input', updatePricePreview);
  travelDateInput === null || travelDateInput === void 0 ? void 0 : travelDateInput.addEventListener('change', updatePricePreview);
  updatePricePreview();
  renderGuestTable();
  updateReservationReview();
  validateGuestManifest(false);
  var initialWizardStep = Number(orderForm.dataset.initialStep || 0);
  showWizardStep(Number.isFinite(initialWizardStep) ? initialWizardStep : 0);
  orderForm.addEventListener('submit', function (event) {
    if (isSubmitting) {
      return;
    }
    event.preventDefault();
    attemptOrderSubmit();
  });
  reservationModalElement === null || reservationModalElement === void 0 ? void 0 : reservationModalElement.addEventListener('hide.bs.modal', function (event) {
    if (isSubmitting) {
      event.preventDefault();
    }
  });
  submissionGuard.bindHistoryRestore(function () {
    setSubmittingState(false);
    window.location.reload();
  });
});
})();

/******/ })()
;