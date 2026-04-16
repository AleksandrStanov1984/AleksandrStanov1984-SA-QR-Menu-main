{{-- resources/views/admin/restaurants/components/hours/_scripts.blade.php --}}
{{-- admin/restaurants/components/hours/_scripts --}}
<script>
    (function(){

        if (window.MB_HOURS_INIT) return;
        window.MB_HOURS_INIT = true;

        // ----------------------------
        // OPEN MODAL
        // ----------------------------
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

            const inputs = row.querySelectorAll('[data-hours-open],[data-hours-close]');

            inputs.forEach(i => i.disabled = e.target.checked);
        });

    })();

</script>
