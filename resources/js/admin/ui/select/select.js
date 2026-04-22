// resources/js/admin/ui/select/select.js

document.addEventListener('click', function (e) {

    const select = e.target.closest('.ui-select');

    document.querySelectorAll('.ui-select').forEach(s => {
        if (s !== select) s.classList.remove('open');
    });

    if (e.target.closest('.ui-select-btn')) {
        if (select) {
            select.classList.toggle('open');
        }
        return;
    }

    const option = e.target.closest('.ui-select-option');
    if (option) {

        const root = option.closest('.ui-select');
        const value = option.dataset.value;
        const label = option.innerText;

        const btn = root.querySelector('.ui-select-btn');
        const input = root.querySelector('input[type="hidden"]');

        btn.innerText = label;
        input.value = value;

        root.querySelectorAll('.ui-select-option')
            .forEach(o => o.classList.remove('active'));

        option.classList.add('active');

        root.classList.remove('open');

        input.dispatchEvent(new Event('change', { bubbles: true }));

        if (root.closest('#topbarLocaleForm')) {
            setTimeout(() => {
                root.closest('form').submit();
            }, 50);
        }
    }

});
