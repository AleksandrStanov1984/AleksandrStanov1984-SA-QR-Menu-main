{{-- resources/views/admin/restaurants/components/import/_scripts.blade.php --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // OPEN MODAL
        document.querySelectorAll('[data-mb-open]').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-mb-open');
                const modal = document.getElementById(id);
                if (modal) {
                    modal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        // CLOSE MODAL
        document.querySelectorAll('[data-mb-close]').forEach(el => {
            el.addEventListener('click', function () {
                const modal = this.closest('.modal');
                if (modal) {
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        });

        // ESC CLOSE
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.setAttribute('aria-hidden', 'true');
                });
            }
        });

        // =========================
        // IMPORT LOG TOGGLE
        // =========================
        const logBtn = document.querySelector('.mb-import-log button');
        const logPanel = document.querySelector('.mb-log-panel');

        if (logBtn && logPanel) {
            logBtn.addEventListener('click', function () {
                const isHidden = logPanel.hasAttribute('hidden');

                if (isHidden) {
                    logPanel.removeAttribute('hidden');
                } else {
                    logPanel.setAttribute('hidden', '');
                }
            });
        }

    });
</script>
