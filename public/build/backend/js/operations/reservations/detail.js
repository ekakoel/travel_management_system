/******/ (() => { // webpackBootstrap
/*!****************************************************************!*\
  !*** ./resources/backend/js/operations/reservations/detail.js ***!
  \****************************************************************/
(function () {
  'use strict';

  var page = document.querySelector('[data-reservation-detail]');
  if (!page) {
    return;
  }
  function setPrintState(active) {
    page.classList.toggle('is-printing', active);
    document.body.classList.toggle('is-reservation-printing', active);
  }
  window.addEventListener('beforeprint', function () {
    setPrintState(true);
  });
  window.addEventListener('afterprint', function () {
    setPrintState(false);
  });
  page.addEventListener('click', function (event) {
    var confirmTrigger = event.target.closest('[data-confirm-delete]');
    if (confirmTrigger) {
      var message = confirmTrigger.getAttribute('data-confirm-delete') || 'Are you sure?';
      if (!window.confirm(message)) {
        event.preventDefault();
        event.stopPropagation();
        return;
      }
    }
    var printTrigger = event.target.closest('[data-reservation-print]');
    if (printTrigger) {
      event.preventDefault();
      setPrintState(true);
      window.print();
    }
  });
  var navLinks = Array.prototype.slice.call(page.querySelectorAll('.reservation-detail-section-nav a'));
  var sections = Array.prototype.slice.call(page.querySelectorAll('[data-reservation-section]'));
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