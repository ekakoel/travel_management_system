/******/ (() => { // webpackBootstrap
/*!*****************************************************!*\
  !*** ./resources/frontend/js/home/profile/index.js ***!
  \*****************************************************/
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
document.addEventListener('DOMContentLoaded', function () {
  var profilePage = document.querySelector('[data-profile-page]');
  if (!profilePage) {
    return;
  }
  var platforms = JSON.parse(profilePage.dataset.contactPlatforms || '[]');
  var defaultPlaceholder = profilePage.dataset.contactDefaultPlaceholder || 'Example: profile link, username, or direct number';
  var platformMap = platforms.reduce(function (carry, platform) {
    carry[platform.value] = platform;
    return carry;
  }, {});
  if (window.bootstrap && window.bootstrap.Modal) {
    var openModalId = profilePage.dataset.profileOpenModal;
    if (openModalId) {
      var modalElement = document.getElementById(openModalId);
      if (modalElement) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
      }
    }
  }
  var previewInput = document.querySelector('[data-profile-preview-input]');
  var previewImage = document.querySelector('[data-profile-preview-image]');
  if (previewInput && previewImage) {
    previewInput.addEventListener('change', function (event) {
      var _ref = event.target.files || [],
        _ref2 = _slicedToArray(_ref, 1),
        file = _ref2[0];
      if (!file) {
        return;
      }
      var objectUrl = URL.createObjectURL(file);
      previewImage.src = objectUrl;
      previewImage.onload = function () {
        return URL.revokeObjectURL(objectUrl);
      };
    });
  }
  var socialManager = document.querySelector('[data-social-manager]');
  if (socialManager) {
    var socialList = socialManager.querySelector('[data-social-list]');
    var socialEmpty = socialManager.querySelector('[data-social-empty]');
    var socialTemplate = socialManager.querySelector('[data-social-template]');
    var addSocialButton = socialManager.querySelector('[data-add-social-channel]');
    var nextSocialIndex = Array.from(socialList.querySelectorAll('[data-social-platform]')).reduce(function (maxIndex, element) {
      var match = element.name.match(/contact_channels\[(\d+)\]/);
      if (!match) {
        return maxIndex;
      }
      return Math.max(maxIndex, Number.parseInt(match[1], 10) + 1);
    }, 0);
    var updateEmptyState = function updateEmptyState() {
      if (!socialEmpty) {
        return;
      }
      socialEmpty.classList.toggle('is-hidden', socialList.querySelectorAll('[data-social-row]').length > 0);
    };
    var updateSocialRow = function updateSocialRow(row) {
      var platformSelect = row.querySelector('[data-social-platform]');
      var valueInput = row.querySelector('[data-social-value]');
      var iconUse = row.querySelector('[data-social-icon] use');
      var selectedPlatform = platformMap[platformSelect.value];
      if (iconUse) {
        iconUse.setAttribute('href', "#profile-icon-".concat(selectedPlatform ? selectedPlatform.icon : 'chat'));
      }
      if (valueInput) {
        valueInput.placeholder = selectedPlatform ? selectedPlatform.placeholder : defaultPlaceholder;
      }
    };
    var createSocialRow = function createSocialRow() {
      if (!socialTemplate) {
        return;
      }
      var nextIndex = nextSocialIndex;
      nextSocialIndex += 1;
      var templateMarkup = socialTemplate.innerHTML.replace(/__INDEX__/g, String(nextIndex));
      socialList.insertAdjacentHTML('beforeend', templateMarkup);
      updateEmptyState();
      var rows = socialList.querySelectorAll('[data-social-row]');
      var newRow = rows[rows.length - 1];
      if (newRow) {
        updateSocialRow(newRow);
      }
    };
    socialList.querySelectorAll('[data-social-row]').forEach(function (row) {
      return updateSocialRow(row);
    });
    updateEmptyState();
    if (addSocialButton) {
      addSocialButton.addEventListener('click', function () {
        createSocialRow();
      });
    }
    socialManager.addEventListener('change', function (event) {
      var row = event.target.closest('[data-social-row]');
      if (row && event.target.matches('[data-social-platform]')) {
        updateSocialRow(row);
      }
    });
    socialManager.addEventListener('click', function (event) {
      var removeButton = event.target.closest('[data-remove-social-channel]');
      if (!removeButton) {
        return;
      }
      var row = removeButton.closest('[data-social-row]');
      if (row) {
        row.remove();
        updateEmptyState();
      }
    });
  }
  document.querySelectorAll('[data-profile-submit-form]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var submitButton = form.querySelector('[data-submit-button]');
      if (!submitButton) {
        return;
      }
      submitButton.disabled = true;
      submitButton.classList.add('is-loading');
      submitButton.textContent = submitButton.dataset.loadingLabel || submitButton.textContent;
    });
  });
});
/******/ })()
;