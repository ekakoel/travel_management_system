/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/backend/js/finance/invoices/detail.js ***!
  \*********************************************************/
(function () {
  'use strict';

  var page = document.querySelector('[data-invoice-detail]');
  if (!page) {
    return;
  }
  page.addEventListener('click', function (event) {
    var modalTrigger = event.target.closest('[data-invoice-modal-open]');
    if (modalTrigger) {
      event.preventDefault();
      var modalId = modalTrigger.getAttribute('data-invoice-modal-open');
      var modal = modalId ? document.getElementById(modalId.replace(/^#/, '')) : null;
      if (modal && window.showBackendModal) {
        window.showBackendModal(modal);
      }
    }
    var deleteTrigger = event.target.closest('[data-invoice-delete-confirm]');
    if (deleteTrigger && !window.confirm(deleteTrigger.getAttribute('data-invoice-delete-confirm'))) {
      event.preventDefault();
      event.stopPropagation();
    }
  });
  var navLinks = Array.prototype.slice.call(page.querySelectorAll('.invoice-detail-section-nav a'));
  var sections = Array.prototype.slice.call(page.querySelectorAll('[data-invoice-section]'));
  function setActiveSection(id) {
    navLinks.forEach(function (link) {
      link.classList.toggle('is-active', link.getAttribute('href') === '#' + id);
    });
  }
  navLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      setActiveSection(link.getAttribute('href').slice(1));
    });
  });
  if ('IntersectionObserver' in window && sections.length) {
    var observer = new IntersectionObserver(function (entries) {
      var visible = entries.filter(function (entry) {
        return entry.isIntersecting;
      }).sort(function (left, right) {
        return right.intersectionRatio - left.intersectionRatio;
      })[0];
      if (visible) {
        setActiveSection(visible.target.id);
      }
    }, {
      rootMargin: '-20% 0px -65% 0px',
      threshold: [0.05, 0.25, 0.5]
    });
    sections.forEach(function (section) {
      observer.observe(section);
    });
  }
  if (sections.length) {
    setActiveSection(sections[0].id);
  }
})();
/******/ })()
;