function getSafeSessionStorage() {
    try {
        return window.sessionStorage;
    } catch (error) {
        return null;
    }
}

function isHistoryRestore(event) {
    var navigation = window.performance && window.performance.getEntriesByType
        ? window.performance.getEntriesByType('navigation')[0]
        : null;

    return !!(event && event.persisted) || !!(navigation && navigation.type === 'back_forward');
}

function createFormSubmissionGuard(form, options) {
    var settings = options || {};
    var storage = getSafeSessionStorage();
    var storageKey = settings.storageKey || form.dataset.submissionKey || ('form-submit:' + window.location.pathname + ':' + (form.getAttribute('action') || ''));

    function markSubmitted() {
        if (!storage) {
            return;
        }

        storage.setItem(storageKey, String(Date.now()));
    }

    function clearSubmitted() {
        if (!storage) {
            return;
        }

        storage.removeItem(storageKey);
    }

    function wasSubmitted() {
        return !!(storage && storage.getItem(storageKey));
    }

    function bindHistoryRestore(handler) {
        window.addEventListener('pageshow', function (event) {
            if (typeof settings.onPageShow === 'function') {
                settings.onPageShow(event);
            }

            if (!isHistoryRestore(event) || !wasSubmitted()) {
                return;
            }

            clearSubmitted();

            if (typeof handler === 'function') {
                handler(event);
                return;
            }

            if (settings.reloadOnHistoryRestore === false) {
                return;
            }

            window.location.reload();
        });
    }

    return {
        bindHistoryRestore: bindHistoryRestore,
        clearSubmitted: clearSubmitted,
        markSubmitted: markSubmitted,
        storageKey: storageKey,
        wasSubmitted: wasSubmitted,
    };
}

module.exports = {
    createFormSubmissionGuard: createFormSubmissionGuard,
    getSafeSessionStorage: getSafeSessionStorage,
    isHistoryRestore: isHistoryRestore,
};
