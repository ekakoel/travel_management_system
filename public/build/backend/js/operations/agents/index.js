/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/backend/js/operations/agents/index.js ***!
  \*********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var page = document.querySelector('.agents-admin-page');
  if (!page) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Agent Index Filters
  |--------------------------------------------------------------------------
  */

  var companyFilter = page.querySelector('[data-agent-filter="company"]');
  var contactFilter = page.querySelector('[data-agent-filter="contact"]');
  var statusFilter = page.querySelector('[data-agent-filter="status"]');
  var rows = Array.from(page.querySelectorAll('[data-agent-row]'));
  var applyFilters = function applyFilters() {
    var company = ((companyFilter === null || companyFilter === void 0 ? void 0 : companyFilter.value) || '').trim().toLowerCase();
    var contact = ((contactFilter === null || contactFilter === void 0 ? void 0 : contactFilter.value) || '').trim().toLowerCase();
    var status = ((statusFilter === null || statusFilter === void 0 ? void 0 : statusFilter.value) || '').trim().toLowerCase();
    rows.forEach(function (row) {
      var rowCompany = (row.dataset.company || '').toLowerCase();
      var rowContact = (row.dataset.contact || '').toLowerCase();
      var rowStatus = (row.dataset.status || '').toLowerCase();
      var matchCompany = !company || rowCompany.includes(company);
      var matchContact = !contact || rowContact.includes(contact);
      var matchStatus = !status || rowStatus === status;
      row.style.display = matchCompany && matchContact && matchStatus ? '' : 'none';
    });
  };
  companyFilter === null || companyFilter === void 0 ? void 0 : companyFilter.addEventListener('input', applyFilters);
  contactFilter === null || contactFilter === void 0 ? void 0 : contactFilter.addEventListener('input', applyFilters);
  statusFilter === null || statusFilter === void 0 ? void 0 : statusFilter.addEventListener('change', applyFilters);

  /*
  |--------------------------------------------------------------------------
  | Reset Filters
  |--------------------------------------------------------------------------
  */

  page.querySelectorAll('[data-agent-filter-reset]').forEach(function (button) {
    button.addEventListener('click', function () {
      if (companyFilter) {
        companyFilter.value = '';
      }
      if (contactFilter) {
        contactFilter.value = '';
      }
      if (statusFilter) {
        statusFilter.value = '';
      }
      applyFilters();
    });
  });
});
/******/ })()
;