/******/ (() => { // webpackBootstrap
/*!**********************************************************!*\
  !*** ./resources/backend/js/operations/drivers/index.js ***!
  \**********************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var page = document.querySelector('.agents-admin-page');
  if (!page) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Elements
  |--------------------------------------------------------------------------
  */

  var companyFilter = page.querySelector('[data-agent-filter="company"]');
  var contactFilter = page.querySelector('[data-agent-filter="contact"]');
  var statusFilter = page.querySelector('[data-agent-filter="status"]');
  var rows = Array.from(page.querySelectorAll('[data-agent-row]'));

  /*
  |--------------------------------------------------------------------------
  | Filter
  |--------------------------------------------------------------------------
  */

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
      row.classList.toggle('is-filtered-out', !(matchCompany && matchContact && matchStatus));
    });
  };
  companyFilter === null || companyFilter === void 0 ? void 0 : companyFilter.addEventListener('input', applyFilters);
  contactFilter === null || contactFilter === void 0 ? void 0 : contactFilter.addEventListener('input', applyFilters);
  statusFilter === null || statusFilter === void 0 ? void 0 : statusFilter.addEventListener('change', applyFilters);

  /*
  |--------------------------------------------------------------------------
  | Modal Helper
  |--------------------------------------------------------------------------
  */

  var openModal = function openModal(modal) {
    if (!modal) {
      return;
    }
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
  };
  var closeModal = function closeModal(modal) {
    if (!modal) {
      return;
    }
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    if (!page.querySelector('.backend-modal.is-open')) {
      document.body.classList.remove('modal-open');
    }
  };

  /*
  |--------------------------------------------------------------------------
  | Close Buttons
  |--------------------------------------------------------------------------
  */

  page.querySelectorAll('[data-modal-close]').forEach(function (button) {
    button.addEventListener('click', function () {
      var modalId = button.dataset.modalClose;
      var modal = document.getElementById(modalId);
      closeModal(modal);
    });
  });

  /*
  |--------------------------------------------------------------------------
  | Click Outside Modal
  |--------------------------------------------------------------------------
  */

  page.querySelectorAll('.backend-modal').forEach(function (modal) {
    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        closeModal(modal);
      }
    });
  });

  /*
  |--------------------------------------------------------------------------
  | ESC
  |--------------------------------------------------------------------------
  */

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') {
      return;
    }
    page.querySelectorAll('.backend-modal.is-open').forEach(function (modal) {
      closeModal(modal);
    });
  });

  /*
  |--------------------------------------------------------------------------
  | Detail Modal
  |--------------------------------------------------------------------------
  */

  page.querySelectorAll('[data-agent-detail]').forEach(function (button) {
    button.addEventListener('click', function () {
      var modalId = button.dataset.agentDetail;
      var modal = document.getElementById(modalId);
      openModal(modal);
    });
  });

  /*
  |--------------------------------------------------------------------------
  | Verify Modal
  |--------------------------------------------------------------------------
  */

  page.querySelectorAll('[data-agent-verify]').forEach(function (button) {
    button.addEventListener('click', function () {
      var modalId = button.dataset.agentVerify;
      var modal = document.getElementById(modalId);
      openModal(modal);
    });
  });

  /*
  |--------------------------------------------------------------------------
  | Reject Modal
  |--------------------------------------------------------------------------
  */

  page.querySelectorAll('[data-agent-reject]').forEach(function (button) {
    button.addEventListener('click', function () {
      var modalId = button.dataset.agentReject;
      var modal = document.getElementById(modalId);
      openModal(modal);
    });
  });

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