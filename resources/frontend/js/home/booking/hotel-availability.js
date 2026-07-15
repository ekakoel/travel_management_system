document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('hotelRateDetailModal');
    const loadedImageSources = new Set();

    function getImageSource(image) {
        return image.currentSrc || image.getAttribute('src') || image.src || '';
    }

    function bindProgressiveImages(scope) {
        scope.querySelectorAll('.availability-progressive-image').forEach(function (image) {
            const markLoaded = function () {
                const imageSource = getImageSource(image);

                if (imageSource) {
                    loadedImageSources.add(imageSource);
                }

                image.classList.add('is-loaded');

                if (image.parentElement) {
                    image.parentElement.classList.add('is-image-loaded');
                }
            };

            const imageSource = getImageSource(image);

            if (imageSource && loadedImageSources.has(imageSource)) {
                markLoaded();
                return;
            }

            if (image.complete && image.naturalWidth > 0) {
                markLoaded();
                return;
            }

            image.addEventListener('load', markLoaded, { once: true });
            image.addEventListener('error', markLoaded, { once: true });
        });
    }

    bindProgressiveImages(document);

    if (!detailModal) {
        return;
    }

    const eyebrowElement = detailModal.querySelector('[data-detail-modal-eyebrow]');
    const titleElement = detailModal.querySelector('[data-detail-modal-title]');
    const iconElement = detailModal.querySelector('[data-detail-modal-icon]');
    const bodyElement = detailModal.querySelector('[data-detail-modal-body]');

    document.querySelectorAll('[data-detail-trigger="hotel-rate-detail"]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const sourceSelector = trigger.getAttribute('data-detail-source');
            const sourceTemplate = sourceSelector ? document.querySelector(sourceSelector) : null;

            if (!sourceTemplate) {
                bodyElement.innerHTML = '';
                bodyElement.removeAttribute('data-current-detail-source');
                return;
            }

            const sourceContent = sourceTemplate.content ? sourceTemplate.content.firstElementChild : null;

            if (!sourceContent) {
                bodyElement.innerHTML = '';
                bodyElement.removeAttribute('data-current-detail-source');
                return;
            }

            const eyebrow = sourceContent.getAttribute('data-detail-eyebrow') || '';
            const title = sourceContent.getAttribute('data-detail-title') || '';
            const icon = sourceContent.getAttribute('data-detail-icon') || 'fa-check-circle-o';

            titleElement.textContent = title;
            iconElement.className = 'fa ' + icon;

            if (bodyElement.getAttribute('data-current-detail-source') !== sourceSelector) {
                bodyElement.innerHTML = sourceContent.innerHTML;
                bodyElement.setAttribute('data-current-detail-source', sourceSelector);
                bindProgressiveImages(bodyElement);
            }

            if (eyebrow) {
                eyebrowElement.textContent = eyebrow;
                eyebrowElement.classList.remove('d-none');
            } else {
                eyebrowElement.textContent = '';
                eyebrowElement.classList.add('d-none');
            }
        });
    });
});
