// resources/js/public/templates/united/menu/search.js

(function initPublicSearch() {

    const input = document.getElementById('publicSearchInput');
    const results = document.getElementById('publicSearchResults');

    if (!input || !results) {
        return;
    }

    const normalize = (value) => String(value || '').toLowerCase().trim();

    let debounceTimer = null;

    const getSearchNodes = () => {
        return Array.from(document.querySelectorAll('[data-search]')).filter(el => {
            return normalize(el.dataset.search).length > 0;
        });
    };

    input.addEventListener('input', () => {

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {

            const val = normalize(input.value);

            if (!val) {
                results.innerHTML = '';
                results.style.display = 'none';
                return;
            }

            const all = getSearchNodes();

            const found = all.filter(el => {
                return normalize(el.dataset.search).includes(val);
            });

            results.innerHTML = '';

            found.slice(0, 10).forEach(el => {

                const div = document.createElement('div');
                div.className = 'menu-search-item';

                const label = el.dataset.label || el.dataset.search;
                const type = el.dataset.type;

                const typeIcon = {
                    category: `
                        <svg viewBox="0 0 24 24" class="menu-search-svg">
                            <path d="M3 7h5l2 2h11v8a2 2 0 0 1-2 2H3z" fill="currentColor"/>
                        </svg>
                    `,
                    subcategory: `
                        <svg viewBox="0 0 24 24" class="menu-search-svg">
                            <circle cx="6" cy="12" r="2" fill="currentColor"/>
                            <circle cx="12" cy="12" r="2" fill="currentColor"/>
                            <circle cx="18" cy="12" r="2" fill="currentColor"/>
                        </svg>
                    `,
                    item: `
    <svg viewBox="0 0 24 24" class="menu-search-svg">
        <path d="M3 7l9-4 9 4-9 4-9-4z" fill="currentColor"/>
        <path d="M3 7v10l9 4 9-4V7" fill="none" stroke="currentColor" stroke-width="1.5"/>
    </svg>
`
                }[type] || '';

                div.innerHTML = `
                    <div class="menu-search-line ${type}">
                        <span class="menu-search-icon">${typeIcon}</span>
                        <span class="menu-search-text">${label}</span>
                    </div>
                `;

                div.onclick = () => {

                    el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    el.classList.add('menu-highlight');

                    setTimeout(() => {
                        el.classList.remove('menu-highlight');
                    }, 1200);

                    results.style.display = 'none';
                    input.value = '';
                };

                results.appendChild(div);
            });

            results.style.display = found.length ? 'block' : 'none';

        }, 120);

    });

    document.addEventListener('click', (e) => {

        const isInside = e.target.closest('.menu-search');

        if (!isInside) {
            results.style.display = 'none';
            input.value = '';
        }

    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            results.style.display = 'none';
            input.value = '';
            input.blur();
        }
    });

    input.addEventListener('focus', () => {
        input.classList.add('is-active');
    });

    input.addEventListener('blur', () => {
        setTimeout(() => {
            input.classList.remove('is-active');
        }, 100);
    });

})();
