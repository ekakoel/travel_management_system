document.addEventListener('DOMContentLoaded', () => {

    const page = document.querySelector(
        '.agents-admin-page'
    );

    if (!page) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Agent Index Filters
    |--------------------------------------------------------------------------
    */

    const companyFilter = page.querySelector(
        '[data-agent-filter="company"]'
    );

    const contactFilter = page.querySelector(
        '[data-agent-filter="contact"]'
    );

    const statusFilter = page.querySelector(
        '[data-agent-filter="status"]'
    );

    const rows = Array.from(
        page.querySelectorAll(
            '[data-agent-row]'
        )
    );


    const applyFilters = () => {

        const company = (
            companyFilter?.value || ''
        ).trim().toLowerCase();

        const contact = (
            contactFilter?.value || ''
        ).trim().toLowerCase();

        const status = (
            statusFilter?.value || ''
        ).trim().toLowerCase();


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
                !company ||
                rowCompany.includes(company);

            const matchContact =
                !contact ||
                rowContact.includes(contact);

            const matchStatus =
                !status ||
                rowStatus === status;


            row.style.display =
                matchCompany &&
                matchContact &&
                matchStatus
                    ? ''
                    : 'none';

        });

    };


    companyFilter?.addEventListener(
        'input',
        applyFilters
    );

    contactFilter?.addEventListener(
        'input',
        applyFilters
    );

    statusFilter?.addEventListener(
        'change',
        applyFilters
    );


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    page.querySelectorAll(
        '[data-agent-filter-reset]'
    ).forEach((button) => {

        button.addEventListener(
            'click',
            () => {

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

            }
        );

    });

});