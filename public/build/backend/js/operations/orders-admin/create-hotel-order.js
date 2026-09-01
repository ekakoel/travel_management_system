/******/ (() => { // webpackBootstrap
/*!****************************************************************************!*\
  !*** ./resources/backend/js/operations/orders-admin/create-hotel-order.js ***!
  \****************************************************************************/
(function () {
  'use strict';

  var page = document.querySelector('[data-create-hotel-order-page]');
  if (!page) {
    return;
  }
  var roomList = page.querySelector('#dynamic_field');
  var roomTemplate = page.querySelector('#hotelOrderRoomTemplate');
  var addButton = page.querySelector('#add');
  var maxRooms = parseInt(page.dataset.maxRooms || '8', 10);
  var roomCount = roomList ? roomList.querySelectorAll('li').length : 0;
  var roomIndex = roomCount;
  var normalPrice = readNumber('#var_normal_price');
  var kickBack = readNumber('#var_kick_back');
  function readNumber(selector) {
    var element = page.querySelector(selector);
    var value = element ? parseInt(element.value || element.textContent || '0', 10) : 0;
    return Number.isFinite(value) ? value : 0;
  }
  function setValue(selector, value) {
    var element = page.querySelector(selector);
    if (!element) {
      return;
    }
    if ('value' in element) {
      element.value = value;
    }
    element.textContent = value;
  }
  function syncTotals() {
    var totalNormalPrice = normalPrice * roomCount;
    setValue('#normal_price', totalNormalPrice);
    setValue('#tda', totalNormalPrice);
    if (kickBack <= 0) {
      return;
    }
    var totalKickBack = kickBack * roomCount;
    var netPrice = totalNormalPrice - totalKickBack;
    setValue('#kick_back', totalKickBack);
    setValue('#total_kick_back', totalKickBack);
    setValue('#normalprice_kickback', netPrice);
    setValue('#npkb', netPrice);
  }
  function addRoom() {
    if (!roomList || !roomTemplate || roomCount >= maxRooms) {
      return;
    }
    roomCount += 1;
    roomIndex += 1;
    var html = roomTemplate.innerHTML.replace(/__ROOM_ID__/g, roomIndex).replace(/__ROOM_NUMBER__/g, roomCount);
    roomList.insertAdjacentHTML('beforeend', html);
    syncTotals();
  }
  function removeRoom(button) {
    var room = button.closest('li');
    if (!room || roomCount <= 1) {
      return;
    }
    room.remove();
    roomCount -= 1;
    syncTotals();
  }
  if (addButton) {
    addButton.addEventListener('click', addRoom);
  }
  page.addEventListener('click', function (event) {
    var removeButton = event.target.closest('[data-hotel-order-remove]');
    if (removeButton) {
      removeRoom(removeButton);
    }
  });
  page.addEventListener('change', function (event) {
    if (!event.target.matches('[data-hotel-order-guest-count]')) {
      return;
    }
    if (typeof window.fRequest === 'function') {
      window.fRequest();
    }
  });
})();
/******/ })()
;