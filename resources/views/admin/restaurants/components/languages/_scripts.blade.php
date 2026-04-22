{{-- resources/views/admin/restaurants/components/languages/_scripts.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const form = document.querySelector('.lang-card form');
        if (!form) return;

        const url   = form.action;
        const token = form.querySelector('input[name="_token"]').value;

        const limit = {{ $limit ?? 'null' }};
        let defaultLocale = "{{ $restaurant->default_locale }}";

        let isSaving = false;

        function save(enabled) {
            if (isSaving) return;
            isSaving = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    enabled_locales: enabled,
                    default_locale: defaultLocale
                })
            })
                .then(res => res.json())
                .then(data => {
                    defaultLocale = data.default_locale;

                    document.querySelectorAll('.lang-switch input').forEach(i => {
                        i.checked = data.enabled_locales.includes(i.value);
                    });

                    updateDefaultLocaleSelect(data.enabled_locales, data.default_locale);
                    updateTopbarLocales(data.enabled_locales, data.default_locale);

                    showFlash(window.UI_LANG.saved, 'success');
                })
                .catch(() => {
                    showFlash('Error', 'error');
                })
                .finally(() => {
                    isSaving = false;
                });
        }

        document.querySelectorAll('.lang-switch input').forEach(el => {
            el.addEventListener('change', () => {
                let enabled = [];

                const checked = Array.from(document.querySelectorAll('.lang-switch input:checked'))
                    .map(i => i.value);

                if (limit !== null && limit === 2) {
                    if (checked.length > 2) {
                        const last = el.value;

                        enabled = [defaultLocale];

                        if (last !== defaultLocale) {
                            enabled.push(last);
                        }
                    } else {
                        enabled = checked;
                    }
                } else {
                    enabled = checked;
                }

                save(enabled);
            });
        });

        function updateDefaultLocaleSelect(locales, currentDefaultLocale) {

            const select = form.querySelector('[data-name="default_locale"]');
            if (!select) return;

            const btn   = select.querySelector('.ui-select-btn');
            const menu  = select.querySelector('.ui-select-menu');
            const input = select.querySelector('input');

            const currentValue = locales.includes(currentDefaultLocale)
                ? currentDefaultLocale
                : (locales[0] || 'de');

            input.value = currentValue;
            btn.innerText = currentValue.toUpperCase();

            menu.innerHTML = '';

            locales.forEach(locale => {
                const div = document.createElement('div');

                div.className = 'ui-select-option';
                if (locale === currentValue) div.classList.add('active');

                div.dataset.value = locale;
                div.textContent = locale.toUpperCase();

                menu.appendChild(div);
            });
        }

        function updateTopbarLocales(locales, currentDefaultLocale) {

            const select = document.querySelector('#topbarLocaleForm .ui-select');
            if (!select) return;

            const btn   = select.querySelector('.ui-select-btn');
            const menu  = select.querySelector('.ui-select-menu');
            const input = select.querySelector('input');

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

        const defaultSelect = form.querySelector('select[name="default_locale"]');
        if (defaultSelect) {
            defaultSelect.addEventListener('change', () => {
                defaultLocale = defaultSelect.value;

                const enabled = Array.from(document.querySelectorAll('.lang-switch input:checked'))
                    .map(i => i.value);

                save(enabled);
            });
        }

    });
</script>
