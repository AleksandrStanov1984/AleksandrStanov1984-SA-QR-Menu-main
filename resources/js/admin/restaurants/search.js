document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('restaurantSearch');
    const table = document.getElementById('restaurantsTable');

    if (!input || !table) return;

    const rows = table.querySelectorAll('tbody tr');

    let debounceTimer = null;

    // =========================
    // SEARCH
    // =========================
    input.addEventListener('input', () => {

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {

            const query = input.value.toLowerCase().trim();

            rows.forEach(row => {

                const id   = row.dataset.id || '';
                const name = row.dataset.name || '';
                const slug = row.dataset.slug || '';

                const match =
                    id.includes(query) ||
                    name.includes(query) ||
                    slug.includes(query);

                row.style.display = match ? '' : 'none';

            });

            // =========================
            // EMPTY STATE (optional)
            // =========================
            handleEmptyState();

        }, 150);

    });

    // =========================
    // EMPTY STATE
    // =========================
    function handleEmptyState() {

        const visible = Array.from(rows).some(row => row.style.display !== 'none');

        let empty = table.querySelector('.search-empty');

        if (!visible) {

            if (!empty) {
                empty = document.createElement('div');
                empty.className = 'search-empty';
                empty.innerText = 'No results found';
                empty.style.padding = '16px';
                empty.style.opacity = '0.6';

                table.appendChild(empty);
            }

        } else {
            if (empty) empty.remove();
        }
    }

});
