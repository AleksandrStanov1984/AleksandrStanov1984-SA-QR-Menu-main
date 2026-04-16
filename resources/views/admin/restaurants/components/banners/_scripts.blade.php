{{-- resources/views/admin/restaurants/components/banners/_scripts.blade.php --}}
{{-- admin/restaurants/components/banners/_scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const container = document.querySelector('[data-banners]');
        if (!container) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const restaurantId = container.dataset.restaurantId;
        const saveUrl = container.dataset.saveUrl;
        const deleteAllUrl = container.dataset.deleteAllUrl;

        // =========================
        // HELPERS
        // =========================
        async function request(url, options = {}) {
            const res = await fetch(url, {
                ...options,
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    ...(options.headers || {})
                }
            });

            if (!res.ok) {
                let msg = 'Request failed';
                try {
                    const data = await res.json();
                    msg = data.message || msg;
                } catch (_) {}
                throw new Error(msg);
            }

            return res.json().catch(() => ({}));
        }

        function confirmAction(text, cb) {
            if (typeof showConfirm === 'function') {
                showConfirm(text, cb);
            } else {
                if (confirm(text)) cb();
            }
        }

        function setLoading(state) {
            document.body.classList.toggle('is-loading', state);

            if (state) {
                window.showLoader?.();
            } else {
                window.hideLoader?.();
            }
        }

        const reloadPage = () => {
            setTimeout(() => window.location.reload(), 300);
        };

        // =========================
        // PREVIEW
        // =========================
        document.querySelectorAll('.banner-input').forEach(input => {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                const img = this.closest('.banner-card')?.querySelector('img');
                if (!img) return;

                const reader = new FileReader();

                reader.onload = e => {
                    img.src = e.target.result;
                    updateButtonsState();
                };

                reader.readAsDataURL(file);
            });
        });

        // =========================
        // SAVE ONE
        // =========================
        document.querySelectorAll('.btn-save-one').forEach(btn => {
            btn.addEventListener('click', async function () {

                const card = this.closest('.banner-card');
                const input = card.querySelector('.banner-input');
                const slot = card.dataset.slot;

                if (!input.files.length) {
                    showFlash(window.UI_LANG.select_file, 'error');
                    return;
                }

                const fd = new FormData();
                fd.append(`banners[${slot}]`, input.files[0]);

                setLoading(true);

                try {
                    await request(saveUrl, { method: 'POST', body: fd });

                    showFlash(window.UI_LANG.saved, 'success');

                    console.log('BEFORE RELOAD');
                    reloadPage();
                    console.log('AFTER RELOAD');

                } catch (e) {
                    showFlash(window.UI_LANG.save_error, 'error');
                } finally {
                    setLoading(false);
                }
            });
        });

        // =========================
        // SAVE ALL
        // =========================
        document.getElementById('saveAll')?.addEventListener('click', async () => {

            const fd = new FormData();
            let has = false;

            document.querySelectorAll('.banner-card').forEach(card => {
                const input = card.querySelector('.banner-input');
                const slot = card.dataset.slot;

                if (input.files.length) {
                    has = true;
                    fd.append(`banners[${slot}]`, input.files[0]);
                }
            });

            if (!has) {
                showFlash(window.UI_LANG.select_file, 'error');
                return;
            }

            setLoading(true);

            try {
                await request(saveUrl, { method: 'POST', body: fd });

                showFlash(window.UI_LANG.saved, 'success');

                reloadPage();

            } catch (e) {
                console.error(e);
                showFlash(window.UI_LANG.save_error, 'error');
            } finally {
                setLoading(false);
            }
        });

        // =========================
        // DELETE
        // =========================
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {

                const id = btn.dataset.id;
                if (!id) return;

                confirmAction(window.UI_LANG.delete_banner, async () => {

                    setLoading(true);

                    try {
                        await request(`/admin/restaurants/${restaurantId}/banners/${id}`, {
                            method: 'DELETE'
                        });

                        showFlash(window.UI_LANG.saved, 'success');

                        console.log('BEFORE RELOAD');
                        reloadPage();
                        console.log('AFTER RELOAD');

                    } catch (e) {
                        console.error(e);
                        showFlash(window.UI_LANG.delete_error, 'error');
                    } finally {
                        setLoading(false);
                    }

                });

            });
        });

        // =========================
        // DELETE ALL
        // =========================
        document.getElementById('deleteAll')?.addEventListener('click', () => {

            confirmAction(window.UI_LANG.delete_all, async () => {

                setLoading(true);

                try {
                    await request(deleteAllUrl, { method: 'DELETE' });

                    showFlash(window.UI_LANG.saved, 'success');

                    reloadPage();

                } catch (e) {
                    console.error(e);
                    showFlash(window.UI_LANG.delete_error, 'error');
                } finally {
                    setLoading(false);
                }

            });

        });

        // =========================
        // DRAG
        // =========================
        let dragged = null;
        let canDrag = false;

        container.querySelectorAll('.banner-drag').forEach(handle => {
            handle.addEventListener('mousedown', () => {
                canDrag = true;
            });
        });

        document.addEventListener('mouseup', () => {
            canDrag = false;
        });

        container.querySelectorAll('.banner-card').forEach(card => {

            if (!card.dataset.id) return;

            card.setAttribute('draggable', true);

            card.addEventListener('dragstart', (e) => {
                if (!canDrag) {
                    e.preventDefault();
                    return;
                }

                dragged = card;
                card.classList.add('dragging');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('dragging');
                saveOrder();
            });

            card.addEventListener('dragover', (e) => {
                e.preventDefault();

                const after = getAfter(container, e.clientY);

                if (!after) {
                    container.appendChild(dragged);
                } else {
                    container.insertBefore(dragged, after);
                }
            });
        });

        // =========================
        // TOUCH DRAG (MOBILE)
        // =========================
        let touchItem = null;

        container.querySelectorAll('.banner-card').forEach(card => {

            if (!card.dataset.id) return;

            card.addEventListener('touchstart', (e) => {
                if (!e.target.closest('.banner-drag')) return;

                touchItem = card;
                card.classList.add('dragging');
            }, { passive: false });

            card.addEventListener('touchmove', (e) => {
                if (!touchItem) return;

                e.preventDefault();

                const touch = e.touches[0];
                const after = getAfter(container, touch.clientY);

                if (!after) {
                    container.appendChild(touchItem);
                } else {
                    container.insertBefore(touchItem, after);
                }
            }, { passive: false });

            card.addEventListener('touchend', () => {
                if (!touchItem) return;

                touchItem.classList.remove('dragging');
                touchItem = null;

                saveOrder();
            });
        });

        function getAfter(container, y) {
            const els = [...container.querySelectorAll('.banner-card:not(.dragging)')];

            return els.reduce((closest, el) => {
                const box = el.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset, element: el };
                }

                return closest;
            }, { offset: -Infinity }).element;
        }

        function saveOrder() {
            const ids = [...container.querySelectorAll('.banner-card')]
                .map(el => el.dataset.id)
                .filter(Boolean);

            if (!ids.length) return;

            request(container.dataset.reorderUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids })
            });
        }

        // =========================
        // BUTTON STATE
        // =========================
        function updateButtonsState() {

            let hasFiles = false;
            let hasBanners = false;

            document.querySelectorAll('.banner-card').forEach(card => {

                const input = card.querySelector('.banner-input');
                const saveBtn = card.querySelector('.btn-save-one');
                const deleteBtn = card.querySelector('.btn-delete');

                if (input.files.length) {
                    hasFiles = true;
                    saveBtn.disabled = false;
                } else {
                    saveBtn.disabled = true;
                }

                if (card.dataset.id) {
                    hasBanners = true;
                    if (deleteBtn) deleteBtn.disabled = false;
                } else {
                    if (deleteBtn) deleteBtn.disabled = true;
                }
            });

            document.getElementById('saveAll')?.toggleAttribute('disabled', !hasFiles);
            document.getElementById('deleteAll')?.toggleAttribute('disabled', !hasBanners);
        }

        updateButtonsState();

    });
</script>
