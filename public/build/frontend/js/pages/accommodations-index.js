/******/ (() => { // webpackBootstrap
/*!********************************************************************!*\
  !*** ./resources/frontend/js/landing-page/accommodations/index.js ***!
  \********************************************************************/
document.addEventListener('DOMContentLoaded', function () {
  var page = document.querySelector('[data-accommodations-page]');
  if (!page || !window.fetch || !window.DOMParser || !window.history) {
    return;
  }
  var parser = new DOMParser();
  var activeRequest = null;
  var searchTimer = null;
  function getForm() {
    return page.querySelector('[data-accommodations-filter-form]');
  }
  function getResults() {
    return page.querySelector('[data-accommodations-results]');
  }
  function buildUrl(form) {
    var url = new URL(form.getAttribute('action'), window.location.origin);
    var formData = new FormData(form);
    formData.forEach(function (value, key) {
      if (value !== '') {
        url.searchParams.set(key, value);
      }
    });
    return url;
  }
  function setLoading(isLoading) {
    var results = getResults();
    var form = getForm();
    if (results) {
      results.classList.toggle('is-loading', isLoading);
      results.setAttribute('aria-busy', isLoading ? 'true' : 'false');
    }
    if (form) {
      Array.from(form.elements).forEach(function (field) {
        field.disabled = isLoading;
      });
    }
  }
  function replaceFromDocument(doc) {
    var nextSummary = doc.querySelector('[data-accommodations-summary]');
    var currentSummary = page.querySelector('[data-accommodations-summary]');
    var nextForm = doc.querySelector('[data-accommodations-filter-form]');
    var currentForm = getForm();
    var nextResults = doc.querySelector('[data-accommodations-results]');
    var currentResults = getResults();
    if (nextSummary && currentSummary) {
      currentSummary.replaceWith(nextSummary);
    }
    if (nextForm && currentForm) {
      currentForm.replaceWith(nextForm);
    }
    if (nextResults && currentResults) {
      currentResults.replaceWith(nextResults);
    }
  }
  function loadUrl(url, options) {
    var shouldPush = !options || options.push !== false;
    if (activeRequest) {
      activeRequest.abort();
    }
    var request = new AbortController();
    activeRequest = request;
    setLoading(true);
    return fetch(url.toString(), {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      signal: request.signal
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Request failed');
      }
      return response.text();
    }).then(function (html) {
      var doc = parser.parseFromString(html, 'text/html');
      replaceFromDocument(doc);
      if (shouldPush) {
        window.history.pushState({}, '', url.toString());
      }
    })["catch"](function (error) {
      if (error.name === 'AbortError') {
        return;
      }
      window.location.href = url.toString();
    })["finally"](function () {
      if (activeRequest === request) {
        setLoading(false);
        activeRequest = null;
      }
    });
  }
  page.addEventListener('submit', function (event) {
    var form = event.target.closest('[data-accommodations-filter-form]');
    if (!form) {
      return;
    }
    event.preventDefault();
    loadUrl(buildUrl(form));
  });
  page.addEventListener('input', function (event) {
    if (!event.target.matches('[data-accommodations-search]')) {
      return;
    }
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(function () {
      var form = getForm();
      if (form) {
        loadUrl(buildUrl(form));
      }
    }, 450);
  });
  page.addEventListener('change', function (event) {
    if (!event.target.matches('[data-accommodations-region], [name="promo_available"]')) {
      return;
    }
    var form = getForm();
    if (form) {
      loadUrl(buildUrl(form));
    }
  });
  page.addEventListener('click', function (event) {
    var resetButton = event.target.closest('[data-accommodations-reset]');
    var paginationLink = event.target.closest('[data-accommodations-results] .pagination a');
    if (resetButton) {
      event.preventDefault();
      loadUrl(new URL(resetButton.getAttribute('href'), window.location.origin));
      return;
    }
    if (paginationLink) {
      event.preventDefault();
      loadUrl(new URL(paginationLink.getAttribute('href'), window.location.origin));
    }
  });
  window.addEventListener('popstate', function () {
    loadUrl(new URL(window.location.href), {
      push: false
    });
  });
});
/******/ })()
;