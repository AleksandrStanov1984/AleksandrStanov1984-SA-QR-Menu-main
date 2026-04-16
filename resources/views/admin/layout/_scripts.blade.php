{{-- resources/views/admin/layout/_scripts.blade.php --}}
{{-- admin/layout/_scripts --}}
<script>
    document.querySelectorAll('[data-select]').forEach(select => {
        const trigger = select.querySelector('.ui-select-trigger');
        const dropdown = select.querySelector('.ui-select-dropdown');
        const options = select.querySelectorAll('.ui-select-option');
        const valueEl = select.querySelector('.ui-select-value');
        const form = select.closest('form');
        const input = form.querySelector('input[name="locale"]');

        trigger.addEventListener('click', () => {
            select.classList.toggle('open');
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;

                input.value = value;
                valueEl.textContent = value.toUpperCase();

                form.submit();

                select.classList.remove('open');
            });
        });

        document.addEventListener('click', (e) => {
            if (!select.contains(e.target)) {
                select.classList.remove('open');
            }
        });
    });
</script>
