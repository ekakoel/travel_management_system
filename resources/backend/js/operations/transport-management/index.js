document.addEventListener('DOMContentLoaded', () => {
    const archiveTableSelector = '#spkArchived';
    const filterInput = document.getElementById('filter_order_no');

    const debounce = (callback, delay = 300) => {
        let timer = null;

        return (...args) => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback(...args), delay);
        };
    };

    const initArchiveTable = () => {
        if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) {
            return null;
        }

        const $ = window.jQuery;

        if (!document.querySelector(archiveTableSelector)) {
            return null;
        }

        if ($.fn.dataTable.isDataTable(archiveTableSelector)) {
            return $(archiveTableSelector).DataTable();
        }

        const table = $(archiveTableSelector).DataTable({
            responsive: true,
            order: [[1, 'desc']],
            pageLength: 10,
            autoWidth: false,
        });

        if (filterInput) {
            const handleFilter = debounce(() => {
                table.column(2).search(filterInput.value).draw();
            });

            filterInput.addEventListener('input', handleFilter);
        }

        return table;
    };

    window.initTransportManagementArchiveTable = initArchiveTable;
    initArchiveTable();

    document.querySelectorAll('[data-transport-management-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');

            if (!submitButton) {
                return;
            }

            const label = submitButton.querySelector('span');
            const processingLabel = submitButton.dataset.processingLabel;

            submitButton.disabled = true;
            submitButton.setAttribute('aria-disabled', 'true');

            if (label && processingLabel) {
                label.textContent = processingLabel;
            }

            const icon = submitButton.querySelector('i');

            if (icon) {
                icon.className = 'icon-copy fa fa-spinner';
                icon.setAttribute('aria-hidden', 'true');
            }
        });
    });
});
