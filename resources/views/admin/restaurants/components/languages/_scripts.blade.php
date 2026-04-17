<script>
    document.addEventListener('DOMContentLoaded', () => {

        const form = document.querySelector('.lang-card form');
        if (!form) return;

        const url   = form.action;
        const token = form.querySelector('input[name="_token"]').value;

        const limit = {{ $limit ?? 'null' }};
        const defaultLocale = "{{ $restaurant->default_locale }}";

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
                    document.querySelectorAll('.lang-switch input').forEach(i => {
                        i.checked = data.enabled_locales.includes(i.value);
                    });

                    showFlash(window.UI_LANG.saved, 'success');
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

                }
                else {
                    enabled = checked;
                }

                save(enabled);
            });

        });

    });
</script>
