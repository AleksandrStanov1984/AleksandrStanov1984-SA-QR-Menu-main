<script>
    document.addEventListener('DOMContentLoaded', function () {

        // =========================
        // OPEN MODAL
        // =========================
        document.querySelectorAll('[data-mb-open]').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-mb-open');
                const modal = document.getElementById(id);
                if (modal) {
                    modal.setAttribute('aria-hidden', 'false');
                }
            });
        });

        // =========================
        // CLOSE MODAL
        // =========================
        document.querySelectorAll('[data-mb-close]').forEach(el => {
            el.addEventListener('click', function () {
                const modal = this.closest('.modal');
                if (modal) {
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        });

        // =========================
        // ESC CLOSE
        // =========================
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
                logPanel.toggleAttribute('hidden');
            });
        }

// =========================
// IMPORT STATUS POLLING
// =========================
        const block = document.getElementById('import-status-block');
        const textEl = document.getElementById('import-status-text');

        let interval = null;

        if (block && textEl) {

            const url = "{{ route('admin.restaurants.menu.import_status', $restaurant) }}";

            let lastStatus = null;

            function render(data) {
                if (!data) return;

                if (!data.status) {
                    clearInterval(interval);
                    block.classList.add('hidden');
                    return;
                }

                if (data.status === lastStatus) return;

                lastStatus = data.status;

                block.classList.remove('hidden');

                if (data.status === 'processing') {
                    textEl.textContent = "{{ __('admin.import.status.processing') }}";
                }

                if (data.status === 'done') {

                    const result = data.result || {};

                    block.innerHTML = `
                <div class="mb-import-result">

                    <button type="button" class="mb-import-close" data-import-status-close>×</button>

                    <div class="mb-import-result-title">
                        {{ __('admin.import.status.done') }}
                    </div>

                    <div class="mb-import-result-stats">
                        <div class="mb-import-ok">
                            ✔ {{ __('admin.import.processed') }}: <strong>${result.processed || 0}</strong>
                        </div>

                        <div class="mb-import-fail">
                            ❌ {{ __('admin.import.unmatched') }}: <strong>${result.unmatched || 0}</strong>
                        </div>
                    </div>

                    ${result.unmatched > 0 ? `
                        <div style="margin-top:10px;">
                            <a href="{{ route('admin.restaurants.menu.import_unmatched', $restaurant) }}"
                               class="btn btn-secondary btn-sm">
                                {{ __('admin.import.download_unmatched') }}
                    </a>
                </div>
` : ''}

                </div>
            `;

                    clearInterval(interval);
                }

                if (data.status === 'error') {
                    textEl.textContent = "{{ __('admin.import.status.error') }}";
                    clearInterval(interval);
                }
            }

            function poll() {
                fetch(url, { cache: 'no-store' })
                    .then(r => r.json())
                    .then(render)
                    .catch(() => {});
            }

            interval = setInterval(poll, 2000);
            poll();
        }

// =========================
// CLOSE BUTTON
// =========================
        document.addEventListener('click', function (e) {
            if (e.target.matches('[data-import-status-close]')) {
                if (block) block.classList.add('hidden');
                if (interval) clearInterval(interval);
            }
        });

// =========================
// FILE NAME PREVIEW
// =========================
        const zipInput = document.querySelector('[data-import-input="zip"]');
        const fileNameEl = document.getElementById('zip-file-name');

        if (zipInput && fileNameEl) {
            zipInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    fileNameEl.textContent = this.files[0].name;
                    fileNameEl.classList.remove('hidden');
                } else {
                    fileNameEl.classList.add('hidden');
                }
            });
        }

    });
</script>
