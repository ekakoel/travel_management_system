document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-backend-status-toggle]').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const form = toggle.closest('form');

            if (!form) {
                return;
            }

            const input = form.querySelector('input[name="is_active"]');
            const label = toggle.querySelector('[data-backend-status-toggle-label]');

            if (!input) {
                return;
            }

            const isActive = input.value === '1';
            const newValue = !isActive;

            input.value = newValue ? '1' : '0';

            toggle.classList.toggle('is-active', newValue);

            if (label) {
                label.textContent = newValue ? 'Active' : 'Draft';
            }

            toggle.title = newValue ? 'Active' : 'Draft';
        });

        toggle.addEventListener('click', async () => {
            const url = toggle.dataset.statusUrl;
            const currentValue = toggle.dataset.statusValue === '1';
            const newValue = !currentValue;

            toggle.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                    },
                    body: JSON.stringify({
                        is_active: newValue,
                    }),
                });

                if (!response.ok) {
                    throw new Error('Failed to update status.');
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to update status.');
                }

                // Update state
                toggle.dataset.statusValue = data.is_active ? '1' : '0';

                // Update visual toggle
                toggle.classList.toggle('is-active', data.is_active);

                // Update label
                const label = toggle.querySelector(
                    '[data-backend-status-toggle-label]'
                );

                if (label) {
                    label.textContent = data.is_active
                        ? 'Active'
                        : 'Draft';
                }

                // Update tooltip
                toggle.title = data.is_active
                    ? 'Active'
                    : 'Draft';

            } catch (error) {
                console.error(error);
            } finally {
                toggle.disabled = false;
            }
        });
    });
});