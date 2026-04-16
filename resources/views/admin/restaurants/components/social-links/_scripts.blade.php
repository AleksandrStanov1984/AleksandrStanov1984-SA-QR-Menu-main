{{-- resources/views/admin/restaurants/components/social-links/_scripts.blade.php --}}

<script>
    (function () {
        const modal = document.getElementById('slModal');
        if (!modal) return;

        const openBtns = document.querySelectorAll('[data-sl-add]');

        const form = document.getElementById('slForm');
        const methodEl = document.getElementById('slMethod');
        const titleEl = document.getElementById('slModalTitle');

        const inputTitle = document.getElementById('slTitle');
        const inputUrl   = document.getElementById('slUrl');

        const iconPreview = document.getElementById('slIconPreview');
        const iconEmpty   = document.getElementById('slIconEmpty');
        const iconFile    = document.getElementById('slIconFile');

        const removeIconBtn    = document.getElementById('slRemoveIconBtn');
        const removeIconHidden = document.getElementById('slRemoveIcon');

        const ROUTE_STORE = @json(route('admin.restaurants.social_links.store', $restaurant));
        const URL_UPDATE_BASE = @json(url('/admin/restaurants/'.$restaurant->id.'/social-links'));
        const CSRF_TOKEN = @json(csrf_token());

        // =========================
        // LOADER
        // =========================
        function setLoading(state) {
            document.body.classList.toggle('is-loading', state);
            if (state) window.showLoader?.();
            else window.hideLoader?.();
        }

        // =========================
        // MODAL
        // =========================
        function showModal() {
            modal.style.display = 'block';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function hideModal() {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';

            resetIconPreview();
            if (inputTitle) inputTitle.value = '';
            if (inputUrl) inputUrl.value = '';
            if (methodEl) methodEl.value = 'POST';
            if (form) form.action = ROUTE_STORE;
        }

        // =========================
        // ICON
        // =========================
        function resetIconPreview() {
            if (iconPreview) {
                iconPreview.src = '';
                iconPreview.style.display = 'none';
            }
            if (iconEmpty) iconEmpty.style.display = 'block';
            if (removeIconBtn) removeIconBtn.style.display = 'none';
            if (removeIconHidden) removeIconHidden.value = '0';
            if (iconFile) iconFile.value = '';
        }

        function setIconPreview(url) {
            if (!iconPreview || !iconEmpty) return;

            if (url) {
                iconPreview.src = url;
                iconPreview.style.display = 'block';
                iconEmpty.style.display = 'none';
                if (removeIconBtn) removeIconBtn.style.display = 'inline-flex';
            } else {
                resetIconPreview();
            }
        }

        // =========================
        // CREATE / EDIT
        // =========================
        function openCreate() {
            if (titleEl) titleEl.textContent = @json(__('admin.socials.add'));
            if (form) form.action = ROUTE_STORE;
            if (methodEl) methodEl.value = 'POST';

            if (inputTitle) inputTitle.value = '';
            if (inputUrl) inputUrl.value = '';
            resetIconPreview();

            showModal();
        }

        function openEdit(payload) {
            if (!payload || !payload.id) return;

            if (titleEl) titleEl.textContent = @json(__('admin.socials.edit'));
            if (form) form.action = URL_UPDATE_BASE + '/' + payload.id;
            if (methodEl) methodEl.value = 'PUT';

            if (inputTitle) inputTitle.value = payload.title || '';
            if (inputUrl) inputUrl.value = payload.url || '';

            resetIconPreview();
            if (payload.icon_url) setIconPreview(payload.icon_url);

            showModal();
        }

        openBtns.forEach(btn => btn.addEventListener('click', openCreate));

        // =========================
        // CLOSE
        // =========================
        modal.addEventListener('click', (e) => {
            if (e.target.closest('[data-modal-close]')) {
                e.preventDefault();
                hideModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.style.display !== 'none') {
                hideModal();
            }
        });

        // =========================
        // SUBMIT LOADER
        // =========================
        if (form) {
            form.addEventListener('submit', () => {
                setLoading(true);
            });
        }

        // =========================
        // EDIT / DELETE
        // =========================
        document.addEventListener('click', (e) => {

            const editBtn = e.target.closest('[data-sl-edit]');
            if (editBtn) {
                try {
                    const payload = JSON.parse(editBtn.getAttribute('data-sl') || '{}');
                    openEdit(payload);
                } catch (_) {}
                return;
            }

            const delBtn = e.target.closest('[data-sl-delete]');
            if (delBtn) {
                const url  = delBtn.getAttribute('data-delete-url');
                const text = delBtn.getAttribute('data-delete-text') || '';
                const hint = delBtn.getAttribute('data-delete-hint') || '';

                if (!url) return;

                const runDelete = () => {
                    setLoading(true);

                    const f = document.createElement('form');
                    f.method = 'POST';
                    f.action = url;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = CSRF_TOKEN;
                    f.appendChild(csrf);

                    const m = document.createElement('input');
                    m.type = 'hidden';
                    m.name = '_method';
                    m.value = 'DELETE';
                    f.appendChild(m);

                    document.body.appendChild(f);
                    f.submit();
                };

                if (typeof showConfirm === 'function') {
                    showConfirm(text + (hint ? ("\n\n" + hint) : ""), runDelete);
                } else {
                    if (confirm(text + (hint ? ("\n\n" + hint) : ""))) {
                        runDelete();
                    }
                }

                return;
            }
        });

        // =========================
        // ICON REMOVE (modal)
        // =========================
        if (removeIconBtn) {
            removeIconBtn.addEventListener('click', () => {
                if (removeIconHidden) removeIconHidden.value = '1';
                setIconPreview('');
            });
        }

        // =========================
        // FILE PREVIEW
        // =========================
        if (iconFile) {
            iconFile.addEventListener('change', () => {
                const file = iconFile.files && iconFile.files[0];
                if (!file) return;

                const url = URL.createObjectURL(file);
                setIconPreview(url);

                if (removeIconHidden) removeIconHidden.value = '0';
            });
        }

        // =========================
        // MASTER ACCORDION
        // =========================
        const master = document.querySelector('[data-sl-master]');
        if (master) {
            master.addEventListener('toggle', () => {
                const open = master.open;
                document.querySelectorAll('[data-sl-item]').forEach(d => { d.open = open; });
            });
        }

        // =========================
        // DRAG & DROP
        // =========================
        const list = document.querySelector('[data-sl-list]');
        const REORDER_URL = @json(route('admin.restaurants.social_links.reorder', $restaurant));

        if (list && window.Sortable) {
            new Sortable(list, {
                animation: 150,
                ghostClass: 'drag-ghost',
                handle: '.mb-handle',

                onEnd: function () {
                    const items = [...list.querySelectorAll('[data-sl-item]')];

                    const order = items.map((el, index) => ({
                        id: el.dataset.id,
                        sort_order: index + 1
                    }));

                    fetch(REORDER_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({ items: order })
                    })
                        .then(res => res.json())
                        .then(() => location.reload());
                }
            });
        }

        // =========================
        // ICON DELETE
        // =========================

        document.addEventListener('click', (e) => {

            const btn = e.target.closest('[data-sl-icon-delete]');
            if (!btn) return;

            const url  = btn.dataset.url;
            const text = btn.dataset.deleteText || '';

            if (!url) return;

            const run = async () => {
                setLoading(true);

                try {
                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    if (!res.ok) {
                        throw new Error('Request failed');
                    }

                    const box = btn.closest('.sl-icon-box');
                    const img = box?.querySelector('[data-sl-icon]');

                    if (img) {
                        img.src = img.dataset.fallback || img.src;
                    }

                    btn.remove();

                    if (typeof showFlash === 'function') {
                        showFlash(window.UI_LANG.saved || 'Saved', 'success');
                    }

                } catch (err) {
                    console.error(err);

                    if (typeof showFlash === 'function') {
                        showFlash(window.UI_LANG.delete_error || 'Error', 'error');
                    }
                } finally {
                    setLoading(false);
                }
            };

            if (typeof showConfirm === 'function') {
                showConfirm(text, run);
            } else {
                if (confirm(text)) {
                    run();
                }
            }
        });

    })();
</script>
