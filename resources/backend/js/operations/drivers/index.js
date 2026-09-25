document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.agents-admin-page');

    if (!page) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const companyFilter = page.querySelector('[data-agent-filter="company"]');
    const contactFilter = page.querySelector('[data-agent-filter="contact"]');
    const statusFilter = page.querySelector('[data-agent-filter="status"]');

    const rows = Array.from(
        page.querySelectorAll('[data-agent-row]')
    );

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    const applyFilters = () => {
        const company = (companyFilter?.value || '').trim().toLowerCase();
        const contact = (contactFilter?.value || '').trim().toLowerCase();
        const status = (statusFilter?.value || '').trim().toLowerCase();

        rows.forEach((row) => {
            const rowCompany = (
                row.dataset.company || ''
            ).toLowerCase();

            const rowContact = (
                row.dataset.contact || ''
            ).toLowerCase();

            const rowStatus = (
                row.dataset.status || ''
            ).toLowerCase();

            const matchCompany =
                !company || rowCompany.includes(company);

            const matchContact =
                !contact || rowContact.includes(contact);

            const matchStatus =
                !status || rowStatus === status;

            row.classList.toggle(
                'is-filtered-out',
                !(matchCompany && matchContact && matchStatus)
            );
        });
    };

    companyFilter?.addEventListener('input', applyFilters);
    contactFilter?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);

    /*
    |--------------------------------------------------------------------------
    | Modal Helper
    |--------------------------------------------------------------------------
    */

    const openModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    };

    const closeModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        if (!page.querySelector('.backend-modal.is-open')) {
            document.body.classList.remove('modal-open');
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Close Buttons
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.dataset.modalClose;
            const modal = document.getElementById(modalId);

            closeModal(modal);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Click Outside Modal
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll('.backend-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        page.querySelectorAll('.backend-modal.is-open').forEach((modal) => {
            closeModal(modal);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Detail Modal
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll('[data-agent-detail]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.dataset.agentDetail;
            const modal = document.getElementById(modalId);

            openModal(modal);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Verify Modal
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll('[data-agent-verify]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.dataset.agentVerify;
            const modal = document.getElementById(modalId);

            openModal(modal);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Reject Modal
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll('[data-agent-reject]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.dataset.agentReject;
            const modal = document.getElementById(modalId);

            openModal(modal);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll('[data-agent-filter-reset]').forEach((button) => {
        button.addEventListener('click', () => {
            if (companyFilter) {
                companyFilter.value = '';
            }

            if (contactFilter) {
                contactFilter.value = '';
            }

            if (statusFilter) {
                statusFilter.value = '';
            }

            applyFilters();
        });
    });
});