/******/ (() => { // webpackBootstrap
/*!*********************************************************************!*\
  !*** ./resources/frontend/js/landing-page/accommodations/detail.js ***!
  \*********************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var roomModal = document.getElementById('roomModal');
  if (!roomModal) {
    return;
  }
  var roomModalImage = document.getElementById('roomModalImage');
  var roomModalTitle = document.getElementById('roomModalTitle');
  var roomModalPromos = document.getElementById('roomModalPromos');
  var roomModalPackages = document.getElementById('roomModalPackages');
  var bookingPeriodLabel = roomModal.getAttribute('data-label-booking-period') || 'Booking Period';
  var stayPeriodLabel = roomModal.getAttribute('data-label-stay-period') || 'Stay Period';
  var durationLabel = roomModal.getAttribute('data-label-duration') || 'Duration';
  function safeJsonParse(value) {
    if (!value) {
      return [];
    }
    try {
      return JSON.parse(value);
    } catch (error) {
      return [];
    }
  }
  function renderPromoList(items) {
    if (!roomModalPromos) {
      return;
    }
    var list = roomModalPromos.querySelector('.room-modal-section__list');
    if (!list || !items.length) {
      roomModalPromos.classList.add('d-none');
      if (list) {
        list.innerHTML = '';
      }
      return;
    }
    list.innerHTML = items.map(function (item) {
      return '<div class="room-modal-offer room-modal-offer--promo">' + '<div class="room-modal-offer__title">' + item.name + '</div>' + '<div class="room-modal-offer__meta"><span>' + bookingPeriodLabel + '</span><strong>' + item.booking_period + '</strong></div>' + '<div class="room-modal-offer__meta"><span>' + stayPeriodLabel + '</span><strong>' + item.stay_period + '</strong></div>' + '</div>';
    }).join('');
    roomModalPromos.classList.remove('d-none');
  }
  function renderPackageList(items) {
    if (!roomModalPackages) {
      return;
    }
    var list = roomModalPackages.querySelector('.room-modal-section__list');
    if (!list || !items.length) {
      roomModalPackages.classList.add('d-none');
      if (list) {
        list.innerHTML = '';
      }
      return;
    }
    list.innerHTML = items.map(function (item) {
      return '<div class="room-modal-offer room-modal-offer--package">' + '<div class="room-modal-offer__title">' + item.name + '</div>' + '<div class="room-modal-offer__meta"><span>' + stayPeriodLabel + '</span><strong>' + item.stay_period + '</strong></div>' + '<div class="room-modal-offer__meta"><span>' + durationLabel + '</span><strong>' + item.duration + '</strong></div>' + '</div>';
    }).join('');
    roomModalPackages.classList.remove('d-none');
  }
  roomModal.addEventListener('show.bs.modal', function (event) {
    var trigger = event.relatedTarget;
    var card = trigger && typeof trigger.closest === 'function' ? trigger.closest('.accommodation-room-card') || trigger : trigger;
    if (!card) {
      return;
    }
    var imageSrc = card.getAttribute('data-image');
    var roomName = card.getAttribute('data-room-name');
    var promos = safeJsonParse(card.getAttribute('data-room-promos'));
    var packages = safeJsonParse(card.getAttribute('data-room-packages'));
    roomModalImage.src = imageSrc;
    roomModalTitle.textContent = roomName;
    renderPromoList(promos);
    renderPackageList(packages);
  });
});
/******/ })()
;