// resources/js/admin/restaurants/restaurants.js

document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('restaurantSearch');
    const statusFilter = document.getElementById('restaurantStatusFilter');

    const rows = document.querySelectorAll('#restaurantsTable tbody tr');

    if (!searchInput || !statusFilter || !rows.length) {
        return;
    }

    function applyFilters() {

        const search = (searchInput.value || '')
            .toLowerCase()
            .trim();

        const status = statusFilter.value;

        rows.forEach((row) => {

            const name = row.dataset.name || '';
            const slug = row.dataset.slug || '';
            const id = row.dataset.id || '';
            const active = row.dataset.active || '';

            const matchesSearch =
                !search ||
                name.includes(search) ||
                slug.includes(search) ||
                id.includes(search);

            const matchesStatus =
                !status ||
                active === status;

            row.style.display =
                matchesSearch && matchesStatus
                    ? ''
                    : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);

    statusFilter.addEventListener('change', applyFilters);

});
