document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('hotelRateDetailModal');

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
                return;
            }

            const sourceContent = sourceTemplate.content ? sourceTemplate.content.firstElementChild : null;

            if (!sourceContent) {
                bodyElement.innerHTML = '';
                return;
            }

            const eyebrow = sourceContent.getAttribute('data-detail-eyebrow') || '';
            const title = sourceContent.getAttribute('data-detail-title') || '';
            const icon = sourceContent.getAttribute('data-detail-icon') || 'fa-check-circle-o';

            titleElement.textContent = title;
            iconElement.className = 'fa ' + icon;
            bodyElement.innerHTML = sourceContent.innerHTML;

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
