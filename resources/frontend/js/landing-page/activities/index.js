document.addEventListener('DOMContentLoaded', function () {
    var page = document.querySelector('[data-activities-page]');

    if (!page || !window.fetch || !window.DOMParser || !window.history) {
        return;
    }

    var parser = new DOMParser();
    var activeRequest = null;
    var searchTimer = null;

    function getForm() {
        return page.querySelector('[data-activities-filter-form]');
    }

    function getResults() {
        return page.querySelector('[data-activities-results]');
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
        var nextSummary = doc.querySelector('[data-activities-summary]');
        var currentSummary = page.querySelector('[data-activities-summary]');
        var nextForm = doc.querySelector('[data-activities-filter-form]');
        var currentForm = getForm();
        var nextResults = doc.querySelector('[data-activities-results]');
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
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }

                return response.text();
            })
            .then(function (html) {
                var doc = parser.parseFromString(html, 'text/html');

                replaceFromDocument(doc);

                if (shouldPush) {
                    window.history.pushState({}, '', url.toString());
                }
            })
            .catch(function (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                window.location.href = url.toString();
            })
            .finally(function () {
                if (activeRequest === request) {
                    setLoading(false);
                    activeRequest = null;
                }
            });
    }

    page.addEventListener('submit', function (event) {
        var form = event.target.closest('[data-activities-filter-form]');

        if (!form) {
            return;
        }

        event.preventDefault();
        loadUrl(buildUrl(form));
    });

    page.addEventListener('input', function (event) {
        if (!event.target.matches('[data-activities-search]')) {
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
        if (!event.target.matches('[data-activities-location], [data-activities-type]')) {
            return;
        }

        var form = getForm();

        if (form) {
            loadUrl(buildUrl(form));
        }
    });

    page.addEventListener('click', function (event) {
        var resetButton = event.target.closest('[data-activities-reset]');
        var paginationLink = event.target.closest('[data-activities-results] .pagination a');

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
        loadUrl(new URL(window.location.href), { push: false });
    });
});
