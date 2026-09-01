/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./resources/frontend/js/home/manual-book/index.js ***!
  \*********************************************************/
(function () {
  var page = document.querySelector('[data-manual-book-page]');
  if (!page) {
    return;
  }
  var searchInput = page.querySelector('[data-manual-search]');
  var languageSelect = page.querySelector('[data-manual-language]');
  var items = Array.from(page.querySelectorAll('[data-manual-item]'));
  var emptyState = page.querySelector('[data-manual-empty]');
  var normalize = function normalize(value) {
    return (value || '').toString().trim().toLowerCase();
  };
  var filterManuals = function filterManuals() {
    var query = normalize(searchInput === null || searchInput === void 0 ? void 0 : searchInput.value);
    var language = (languageSelect === null || languageSelect === void 0 ? void 0 : languageSelect.value) || 'all';
    var visibleCount = 0;
    items.forEach(function (item) {
      var searchValue = item.dataset.manualSearchValue || '';
      var languageValue = item.dataset.manualLanguageValue || '';
      var matchesSearch = query === '' || searchValue.includes(query);
      var matchesLanguage = language === 'all' || languageValue === language;
      var isVisible = matchesSearch && matchesLanguage;
      item.classList.toggle('is-hidden', !isVisible);
      if (isVisible) {
        visibleCount += 1;
      }
    });
    if (emptyState) {
      emptyState.classList.toggle('d-none', visibleCount > 0);
    }
  };
  searchInput === null || searchInput === void 0 ? void 0 : searchInput.addEventListener('input', filterManuals);
  languageSelect === null || languageSelect === void 0 ? void 0 : languageSelect.addEventListener('change', filterManuals);
})();
/******/ })()
;