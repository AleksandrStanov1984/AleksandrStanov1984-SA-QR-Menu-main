{{-- resources/views/admin/restaurants/components/_scripts-logo.blade.php --}}
<script>

    document.addEventListener('click', async (e) => {

        const btn = e.target.closest('[data-logo-delete]');
        if (!btn) return;

        if (btn.disabled) return;

        const url = btn.getAttribute('data-logo-url');
        if (!url) return;

        try {

            window.showLoader?.();

            const resp = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!resp.ok) {
                throw new Error(await resp.text());
            }

            const img = document.querySelector('[data-preview="logo"]');

            if (img) {
                img.src = img.dataset.fallback;
            }

            const sidebarLogo = document.querySelector('[data-sidebar-logo]');
            if (sidebarLogo) {
                sidebarLogo.src = img?.dataset.fallback || '';
            }

            if (btn) btn.remove();

            showFlash(window.UI_LANG.saved || 'Saved', 'success');

        } catch (err) {
            console.error(err);
            showFlash(window.UI_LANG.delete_error || 'Error', 'error');
        } finally {
            window.hideLoader?.();
        }

    });

        // =========================
        // SAVE BUTTON STATE (LOGO)
        // =========================
        document.addEventListener('DOMContentLoaded', () => {

        const form = document.querySelector('.branding-logo-form');
        if (!form) return;

        const input = form.querySelector('[data-input="logo"]');
        const btn   = form.querySelector('.branding-logo-save');

        const updateState = () => {
        if (!btn || !input) return;

        const hasFile = input.files && input.files.length > 0;
        btn.disabled = !hasFile;
    };

        updateState();

        input.addEventListener('change', updateState);
    });


    // =========================
    // PREVIEW (UPLOAD)
    // =========================
    document.addEventListener('change', function (e) {

        const input = e.target.closest('[data-input="logo"]');
        if (!input) return;

        const file = input.files?.[0];
        if (!file) return;

        const img = document.querySelector('[data-preview="logo"]');
        if (!img) return;

        img.src = URL.createObjectURL(file);
    });

</script>
