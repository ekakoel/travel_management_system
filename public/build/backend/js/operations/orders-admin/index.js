/******/ (() => { // webpackBootstrap
/*!***************************************************************!*\
  !*** ./resources/backend/js/operations/orders-admin/index.js ***!
  \***************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var page = document.querySelector('[data-orders-admin]');
  if (!page) {
    return;
  }
  var search = page.querySelector('[data-orders-search]');
  var rows = Array.from(page.querySelectorAll('[data-order-row]'));
  var sections = Array.from(page.querySelectorAll('[data-orders-section]'));
  var searchFrame = null;
  var rowSearchIndex = new Map(rows.map(function (row) {
    return [row, row.dataset.orderSearch || ''];
  }));
  var syncSectionVisibility = function syncSectionVisibility() {
    sections.forEach(function (section) {
      var visibleRows = section.querySelectorAll('[data-order-row]:not([hidden])');
      section.hidden = visibleRows.length === 0;
    });
  };
  var filterOrders = function filterOrders() {
    var keyword = search.value.trim().toLowerCase();
    rows.forEach(function (row) {
      row.hidden = keyword !== '' && !rowSearchIndex.get(row).includes(keyword);
    });
    syncSectionVisibility();
  };
  search === null || search === void 0 ? void 0 : search.addEventListener('input', function () {
    if (searchFrame) {
      window.cancelAnimationFrame(searchFrame);
    }
    searchFrame = window.requestAnimationFrame(filterOrders);
  });
});
/******/ })()
;