// resources/js/admin/restaurants/restaurants.js

document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('restaurantSearch');

    const statusFilter = document.getElementById('restaurantStatusFilter');
    const billingWarningFilter = document.getElementById('billingWarningFilter');
    const billingTypeFilter = document.getElementById('billingTypeFilter');
    const planFilter = document.getElementById('planFilter');

    // =========================
    // TABLE FILTERS
    // =========================
    const templateFilter = document.getElementById('templateFilter');

    const rows = document.querySelectorAll('#restaurantsTable tbody tr');

    if (!searchInput || !rows.length) {
        return;
    }

    function applyFilters() {

        const search = (searchInput.value || '')
            .toLowerCase()
            .trim();

        const statusValue = statusFilter?.value || '';
        const warningValue = billingWarningFilter?.value || '';
        const billingValue = billingTypeFilter?.value || '';
        const planValue = planFilter?.value || '';

        // =========================
        // TABLE FILTERS
        // =========================
        const templateValue = templateFilter?.value || '';

        rows.forEach((row) => {

            const name = row.dataset.name || '';
            const slug = row.dataset.slug || '';
            const id = row.dataset.id || '';

            const active = row.dataset.active || '';
            const plan = row.dataset.plan || '';

            const billingWarning = row.dataset.billingWarning || '';
            const billingStatus = row.dataset.billingStatus || '';

            // =========================
            // TABLE DATASETS
            // =========================
            const template = row.dataset.template || '';

            // =========================
            // SEARCH
            // =========================
            const matchesSearch =
                !search ||
                name.includes(search) ||
                slug.includes(search) ||
                id.includes(search);

            // =========================
            // STATUS FILTER
            // =========================
            const matchesStatus =
                !statusValue ||
                active === statusValue;

            // =========================
            // BILLING WARNING FILTER
            // =========================
            const matchesWarning =
                !warningValue ||
                billingWarning === warningValue;

            // =========================
            // BILLING TYPE FILTER
            // =========================
            const matchesBilling =
                !billingValue ||
                billingStatus.includes(billingValue);

            // =========================
            // PLAN FILTER
            // =========================
            const matchesPlan =
                !planValue ||
                plan === planValue;

            // =========================
            // TEMPLATE FILTER
            // =========================
            const matchesTemplate =
                !templateValue ||
                template === templateValue;

            // =========================
            // FINAL
            // =========================
            const visible =
                matchesSearch &&
                matchesStatus &&
                matchesWarning &&
                matchesBilling &&
                matchesPlan &&
                matchesTemplate;

            row.style.display = visible
                ? ''
                : 'none';
        });
    }

    // =========================
    // EVENTS
    // =========================
    searchInput.addEventListener('input', applyFilters);

    statusFilter?.addEventListener('change', applyFilters);

    billingWarningFilter?.addEventListener('change', applyFilters);

    billingTypeFilter?.addEventListener('change', applyFilters);

    planFilter?.addEventListener('change', applyFilters);

    templateFilter?.addEventListener('change', applyFilters);

    // =========================
    // DELETE CONFIRM
    // =========================
    document.querySelectorAll('.js-restaurant-delete')
        .forEach((btn) => {

            btn.addEventListener('click', () => {

                const formId = btn.dataset.formId;
                const message = btn.dataset.confirm;

                showConfirm(message, () => {

                    const form = document.getElementById(formId);

                    if (form) {
                        form.submit();
                    }

                });

            });

        });

    // =========================
    // INITIAL APPLY
    // =========================
    applyFilters();

});
