/******/ (() => { // webpackBootstrap
/*!***********************************************************************!*\
  !*** ./resources/backend/js/operations/transport-management/index.js ***!
  \***********************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var archiveTableSelector = '#spkArchived';
  var filterInput = document.getElementById('filter_order_no');
  var debounce = function debounce(callback) {
    var delay = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 300;
    var timer = null;
    return function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }
      window.clearTimeout(timer);
      timer = window.setTimeout(function () {
        return callback.apply(void 0, args);
      }, delay);
    };
  };
  var initArchiveTable = function initArchiveTable() {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) {
      return null;
    }
    var $ = window.jQuery;
    if (!document.querySelector(archiveTableSelector)) {
      return null;
    }
    if ($.fn.dataTable.isDataTable(archiveTableSelector)) {
      return $(archiveTableSelector).DataTable();
    }
    var table = $(archiveTableSelector).DataTable({
      responsive: true,
      order: [[1, 'desc']],
      pageLength: 10,
      autoWidth: false
    });
    if (filterInput) {
      var handleFilter = debounce(function () {
        table.column(2).search(filterInput.value).draw();
      });
      filterInput.addEventListener('input', handleFilter);
    }
    return table;
  };
  window.initTransportManagementArchiveTable = initArchiveTable;
  initArchiveTable();
});
/******/ })()
;