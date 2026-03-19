<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
    (function(){

        // ----------------------------
        // 🔥 AJAX DELETE
        // ----------------------------
        document.addEventListener('submit', async function (e) {

            const form = e.target.closest('#mbConfirmDeleteForm');
            if (!form) return;

            e.preventDefault();

            try {
                const resp = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form)
                });

                if (!resp.ok) throw new Error('Delete failed');

                const data = await resp.json();

                const row = document.querySelector(`[data-item-id="${data.deleted_id}"]`);
                if (row) {
                    const wrap = row.closest('[data-item-row]');
                    if (wrap) wrap.remove();
                }

                const modal = document.getElementById('mbConfirmDelete');
                if (modal) modal.setAttribute('aria-hidden','true');

            } catch (err) {
                console.error(err);
                alert('Ошибка удаления');
            }

        });

        // ----------------------------
        // utils
        // ----------------------------
        const openModal = (id) => {
            const m = document.getElementById(id);
            if(m) m.setAttribute('aria-hidden','false');
        };

        const closeModal = (mOrId) => {
            const m = (typeof mOrId === 'string') ? document.getElementById(mOrId) : mOrId;
            if(m) m.setAttribute('aria-hidden','true');
        };

        const ensureMethod = (form, method) => {
            let el = form.querySelector('input[name="_method"]');
            if(!method){
                if(el) el.remove();
                return;
            }
            if(!el){
                el = document.createElement('input');
                el.type = 'hidden';
                el.name = '_method';
                form.appendChild(el);
            }
            el.value = method;
        };

        const isDisabledEl = (el) => {
            if(!el) return true;
            return !!(el.disabled || el.getAttribute('disabled') !== null || el.getAttribute('aria-disabled') === 'true');
        };

        // ----------------------------
        // 🔥 FIX: ACTIVE ENDPOINT
        // ----------------------------
        const patchItemActive = async (base, itemId, value) => {
            const url = `${base}/${itemId}/active`;

            const resp = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ is_active: value }),
            });

            if(!resp.ok){
                const txt = await resp.text();
                throw new Error(txt || 'Active update failed');
            }

            return await resp.json();
        };

        // ----------------------------
        // 🔥 META BASE
        // ----------------------------
        const getMetaBase = (el) => {
            const list = el.closest('[data-sortable-items]');
            const base = list?.getAttribute('data-item-meta-base');
            return (base && base.trim())
                ? base.trim()
                : "{{ url('/admin/restaurants/'.$restaurant->id.'/items') }}";
        };

        // ----------------------------
        // 🔥 CHANGE HANDLER (ВОТ ГЛАВНОЕ)
        // ----------------------------
        document.addEventListener('change', async (e) => {

            const el = e.target?.closest('[data-item-meta]');
            if(!el) return;
            if(isDisabledEl(el)) return;

            const itemId = el.getAttribute('data-item-id');
            const key = el.getAttribute('data-item-meta');
            if(!itemId || !key) return;

            const base = getMetaBase(el);

            let value;
            if(el.tagName === 'SELECT'){
                value = Number(el.value);
            } else {
                value = !!el.checked;
            }

            try{

                // 🔥 ВАЖНО: РАЗДЕЛЕНИЕ
                if(key === 'is_active'){
                    await patchItemActive(base, itemId, value);
                    return;
                }

                // fallback (если вдруг meta используешь)
                const resp = await fetch(`${base}/${itemId}/meta`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ [key]: value }),
                });

                if(!resp.ok){
                    throw new Error('Meta update failed');
                }

            } catch(err){
                console.error(err);
                alert('Ошибка сохранения');

                // rollback checkbox
                if(el.type === 'checkbox'){
                    el.checked = !el.checked;
                }
            }

        });

        // ----------------------------
        // DELETE CONFIRM BUTTON
        // ----------------------------
        document.addEventListener('click', (e) => {
            const target = e.target;

            const delBtn = target.closest('[data-confirm-delete]');
            if(delBtn){
                if(isDisabledEl(delBtn)) return;

                const url  = (delBtn.getAttribute('data-delete-url')  || '').trim();
                const text = (delBtn.getAttribute('data-delete-text') || '').trim();
                const hint = (delBtn.getAttribute('data-delete-hint') || '').trim();

                const modal = document.getElementById('mbConfirmDelete');
                const form  = document.getElementById('mbConfirmDeleteForm');
                const txtEl = document.getElementById('mbConfirmDeleteText');
                const hEl   = document.getElementById('mbConfirmDeleteHint');

                if(!modal || !form) return;

                form.method = 'POST';
                if(url) form.action = url;
                ensureMethod(form, 'DELETE');

                if(txtEl) txtEl.textContent = text || 'Вы уверены, что хотите удалить?';

                if(hEl){
                    hEl.textContent = hint || '';
                    hEl.style.display = hint ? '' : 'none';
                }

                openModal('mbConfirmDelete');
            }
        });

    })();
</script>
