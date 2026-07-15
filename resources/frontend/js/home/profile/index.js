document.addEventListener('DOMContentLoaded', () => {
    const profilePage = document.querySelector('[data-profile-page]');

    if (!profilePage) {
        return;
    }

    const platforms = JSON.parse(profilePage.dataset.contactPlatforms || '[]');
    const defaultPlaceholder = profilePage.dataset.contactDefaultPlaceholder || 'Example: profile link, username, or direct number';
    const platformMap = platforms.reduce((carry, platform) => {
        carry[platform.value] = platform;
        return carry;
    }, {});

    if (window.bootstrap && window.bootstrap.Modal) {
        const openModalId = profilePage.dataset.profileOpenModal;

        if (openModalId) {
            const modalElement = document.getElementById(openModalId);

            if (modalElement) {
                window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        }
    }

    const previewInput = document.querySelector('[data-profile-preview-input]');
    const previewImage = document.querySelector('[data-profile-preview-image]');

    if (previewInput && previewImage) {
        previewInput.addEventListener('change', (event) => {
            const [file] = event.target.files || [];

            if (!file) {
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            previewImage.src = objectUrl;
            previewImage.onload = () => URL.revokeObjectURL(objectUrl);
        });
    }

    const socialManager = document.querySelector('[data-social-manager]');

    if (socialManager) {
        const socialList = socialManager.querySelector('[data-social-list]');
        const socialEmpty = socialManager.querySelector('[data-social-empty]');
        const socialTemplate = socialManager.querySelector('[data-social-template]');
        const addSocialButton = socialManager.querySelector('[data-add-social-channel]');
        let nextSocialIndex = Array.from(socialList.querySelectorAll('[data-social-platform]')).reduce((maxIndex, element) => {
            const match = element.name.match(/contact_channels\[(\d+)\]/);

            if (!match) {
                return maxIndex;
            }

            return Math.max(maxIndex, Number.parseInt(match[1], 10) + 1);
        }, 0);

        const updateEmptyState = () => {
            if (!socialEmpty) {
                return;
            }

            socialEmpty.classList.toggle('is-hidden', socialList.querySelectorAll('[data-social-row]').length > 0);
        };

        const updateSocialRow = (row) => {
            const platformSelect = row.querySelector('[data-social-platform]');
            const valueInput = row.querySelector('[data-social-value]');
            const iconUse = row.querySelector('[data-social-icon] use');
            const selectedPlatform = platformMap[platformSelect.value];

            if (iconUse) {
                iconUse.setAttribute('href', `#profile-icon-${selectedPlatform ? selectedPlatform.icon : 'chat'}`);
            }

            if (valueInput) {
                valueInput.placeholder = selectedPlatform
                    ? selectedPlatform.placeholder
                    : defaultPlaceholder;
            }
        };

        const createSocialRow = () => {
            if (!socialTemplate) {
                return;
            }

            const nextIndex = nextSocialIndex;
            nextSocialIndex += 1;
            const templateMarkup = socialTemplate.innerHTML.replace(/__INDEX__/g, String(nextIndex));
            socialList.insertAdjacentHTML('beforeend', templateMarkup);
            updateEmptyState();
            const rows = socialList.querySelectorAll('[data-social-row]');
            const newRow = rows[rows.length - 1];

            if (newRow) {
                updateSocialRow(newRow);
            }
        };

        socialList.querySelectorAll('[data-social-row]').forEach((row) => updateSocialRow(row));
        updateEmptyState();

        if (addSocialButton) {
            addSocialButton.addEventListener('click', () => {
                createSocialRow();
            });
        }

        socialManager.addEventListener('change', (event) => {
            const row = event.target.closest('[data-social-row]');

            if (row && event.target.matches('[data-social-platform]')) {
                updateSocialRow(row);
            }
        });

        socialManager.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-social-channel]');

            if (!removeButton) {
                return;
            }

            const row = removeButton.closest('[data-social-row]');

            if (row) {
                row.remove();
                updateEmptyState();
            }
        });
    }

    document.querySelectorAll('[data-profile-submit-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('[data-submit-button]');

            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;
            submitButton.classList.add('is-loading');
            submitButton.textContent = submitButton.dataset.loadingLabel || submitButton.textContent;
        });
    });
});
