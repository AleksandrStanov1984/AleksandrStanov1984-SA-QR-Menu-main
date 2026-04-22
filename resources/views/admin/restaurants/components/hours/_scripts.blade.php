{{-- resources/views/admin/restaurants/components/hours/_scripts.blade.php --}}

<script>
    (function(){

        if (window.MB_HOURS_INIT) return;
        window.MB_HOURS_INIT = true;

        document.addEventListener('click', function(e){

            if (e.target.closest('[data-mb-open-hours]')) {
                document.getElementById('mbModalHours').style.display = 'block';
            }

            if (e.target.closest('[data-mb-close]')) {
                const modal = document.getElementById('mbModalHours');
                if (modal) modal.style.display = 'none';
            }
        });

        // ----------------------------
        // DISABLE TIME IF CLOSED
        // ----------------------------
        document.addEventListener('change', function(e){

            if (!e.target.matches('[data-hours-closed]')) return;

            const row = e.target.closest('.mb-row');
            if (!row) return;

            const isClosed = e.target.checked;

            row.querySelectorAll('[data-hours-open],[data-hours-close]')
                .forEach(i => i.disabled = isClosed);

            row.querySelectorAll('.ui-select').forEach(select => {

                const btn = select.querySelector('.ui-select-btn');

                if (isClosed) {
                    select.classList.add('is-disabled');
                    btn.setAttribute('disabled', 'disabled');
                } else {
                    select.classList.remove('is-disabled');
                    btn.removeAttribute('disabled');
                }

            });
        });

    })();

</script>
