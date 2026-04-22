{{-- resources/views/admin/layout/_scripts.blade.php --}}

<script>

    // ===============================
    // SYNC ALL LOCALE SELECTS
    // ===============================
    function syncLocaleSelects(locale) {

        document.querySelectorAll('.ui-select[data-name="locale"]').forEach(select => {

            const btn   = select.querySelector('.ui-select-btn');
            const input = select.querySelector('input');

            if (btn)   btn.innerText = locale.toUpperCase();
            if (input) input.value   = locale;

            select.querySelectorAll('.ui-select-option').forEach(opt => {
                opt.classList.toggle('active', opt.dataset.value === locale);
            });
        });

    }

    // ===============================
    // UPDATE TOPBAR LOCALES (FULL REBUILD)
    // ===============================
    function updateTopbarLocales(locales, currentDefaultLocale) {

        const select = document.querySelector('#topbarLocaleForm .ui-select');
        if (!select) return;

        const btn   = select.querySelector('.ui-select-btn');
        const input = select.querySelector('input');
        const menu  = select.querySelector('.ui-select-menu');

        btn.innerText = currentDefaultLocale.toUpperCase();
        input.value   = currentDefaultLocale;

        locales = [...locales].sort((a, b) => {
            if (a === currentDefaultLocale) return -1;
            if (b === currentDefaultLocale) return 1;
            return a.localeCompare(b);
        });

        menu.innerHTML = '';

        locales.forEach(locale => {
            const div = document.createElement('div');

            div.className = 'ui-select-option';
            if (locale === currentDefaultLocale) {
                div.classList.add('active');
            }

            div.dataset.value = locale;
            div.textContent = locale.toUpperCase();

            menu.appendChild(div);
        });
    }

    // ===============================
    // SELECT HANDLER
    // ===============================
    document.addEventListener('click', function (e) {

        const select = e.target.closest('.ui-select');

        document.querySelectorAll('.ui-select').forEach(s => {
            if (s !== select) s.classList.remove('open');
        });

        if (e.target.closest('.ui-select-btn')) {
            if (select) select.classList.toggle('open');
            return;
        }

        const option = e.target.closest('.ui-select-option');
        if (option) {

            const root  = option.closest('.ui-select');
            const value = option.dataset.value;
            const label = option.innerText;

            const btn   = root.querySelector('.ui-select-btn');
            const input = root.querySelector('input');

            btn.innerText = label;
            input.value   = value;

            root.querySelectorAll('.ui-select-option')
                .forEach(o => o.classList.remove('active'));

            option.classList.add('active');

            root.classList.remove('open');

            if (root.dataset.name === 'locale') {

                syncLocaleSelects(value);

                setTimeout(() => {
                    root.closest('form')?.submit();
                }, 50);
            }
        }

    });

</script>
