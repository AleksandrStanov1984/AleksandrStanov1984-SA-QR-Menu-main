{{-- resources/views/admin/restaurants/components/menu-builder/_scripts.blade.php --}}

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
(function(){

    // ----------------------------
    // AJAX DELETE
    // ----------------------------
    document.addEventListener('submit', async function (e) {

        const form = e.target.closest('#mbConfirmDeleteForm');
        if (!form) return;

        e.preventDefault();

        if (window.showLoader)
            window.showLoader();

        try {
            const resp = await fetch(form.action, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });

            let data = {};

            try {
                data = await resp.json();
            } catch (e) {
                console.warn('Response not JSON');
            }

            if (!resp.ok) {
                throw new Error(data.message || 'Delete failed');
            }

            let row =
                document.querySelector(`[data-item-id="${data.deleted_id}"]`) ||
                document.querySelector(`[data-section-id="${data.deleted_id}"]`);

            if (row) {

                if (row.hasAttribute('data-item-id')) {
                    row.remove();
                }

                if (row.hasAttribute('data-section-id')) {

                    const wrapper = row.closest('[data-section-id]');

                    if (wrapper) {
                        wrapper.remove();
                    } else {
                        row.remove();
                    }
                }
            }

            const modal = document.getElementById('mbConfirmDelete');
            const formEl = document.getElementById('mbConfirmDeleteForm');

            if (formEl) {
                formEl.reset();
                formEl.action = '';

                const btn = formEl.querySelector('button[type="submit"]');
                if (btn) btn.disabled = false;
            }

            if (modal) modal.setAttribute('aria-hidden', 'true');

            if (data.flash) {
                showFlash(data.flash.message, data.flash.type);
            } else {
                showFlash(
                    data.message || window.UI_LANG.saved || 'Done',
                    'success'
                );
            }

        } catch (err) {
            console.error(err);

            if (data.flash) {
                showFlash(data.flash.message, data.flash.type);
            } else {
                showFlash(
                    window.UI_LANG.delete_error || 'Error',
                    'error'
                );
            }

        } finally {
            if (window.resetLoader) {
                window.resetLoader();
            }
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
  // Item modal reset for CREATE
  // ----------------------------
  const resetItemModalForCreate = () => {
    const modal = document.getElementById('mbModalItem');
    if(!modal) return;

    const form = modal.querySelector('#mbItemForm');
    if(!form) return;

    ensureMethod(form, null);

    const price = form.querySelector('[name="price"]');
    if(price) price.value = '';

    form.querySelectorAll('[name^="translations["]').forEach(i => { i.value = ''; });

    form.querySelectorAll('[data-style-font],[data-style-color]').forEach(i => i.value = '');

    const file = form.querySelector('input[type="file"][name="image"]');
    if(file) file.value = '';

    const submit = form.querySelector('button[type="submit"]');
    if(submit) submit.textContent = "{{ __('admin.actions.create') }}";
  };

    // ----------------------------
    // Confirm Delete modal
    // ----------------------------
    const openDeleteConfirm = (btn) => {
        if(isDisabledEl(btn)) return;

        const url  = (btn.getAttribute('data-delete-url')  || '').trim();
        const text = (btn.getAttribute('data-delete-text') || '').trim();
        const hint = (btn.getAttribute('data-delete-hint') || '').trim();

        const modal = document.getElementById('mbConfirmDelete');
        const form  = document.getElementById('mbConfirmDeleteForm');
        const txtEl = document.getElementById('mbConfirmDeleteText');
        const hEl   = document.getElementById('mbConfirmDeleteHint');

        if(!modal || !form) return;

        form.reset();

        form.method = 'POST';
        if(url) form.action = url;
        ensureMethod(form, 'DELETE');

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.removeAttribute('disabled');
        }

        if(txtEl) txtEl.textContent = text;

        if(hEl){
            hEl.textContent = hint || '';
            hEl.style.display = hint ? '' : 'none';
        }

        openModal('mbConfirmDelete');
    };

  const fontMap = {
    inter: 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
    poppins: 'Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif',
    roboto: 'Roboto, system-ui, -apple-system, Segoe UI, Arial, sans-serif',
    playfair: 'Playfair Display, Georgia, "Times New Roman", serif',
  };

  const getItemModal = () => document.getElementById('mbModalItem');

  const applyStyle = (key) => {
    const itemModal = getItemModal();
    if(!itemModal) return;

    const fontKey = itemModal.querySelector(`[data-style-font="${key}"]`)?.value || '';
    const color   = itemModal.querySelector(`[data-style-color="${key}"]`)?.value || '';
    const sizeVal = itemModal.querySelector(`[data-style-size="${key}"]`)?.value || '';

    const font = fontKey ? (fontMap[fontKey] || fontKey) : '';

    itemModal.querySelectorAll(`[data-text-field="${key}"]`).forEach(inp => {
      inp.style.fontFamily = font;
      inp.style.color = color;
      inp.style.fontSize = sizeVal ? (Number(sizeVal) + 'px') : '';
    });
  };

  const onStyleChange = (e) => {
    const target = (e.target && e.target.nodeType === 1) ? e.target : e.target?.parentElement;
    if(!target) return;

    const itemModal = getItemModal();
    if(!itemModal || !itemModal.contains(target)) return;

    const f = target.closest('[data-style-font],[data-style-color],[data-style-size]');
    if(!f) return;

    const key =
      f.getAttribute('data-style-font') ||
      f.getAttribute('data-style-color') ||
      f.getAttribute('data-style-size');

    if(key) applyStyle(key);
  };

  ['title','desc','details'].forEach(k => applyStyle(k));
  document.addEventListener('input', onStyleChange);
  document.addEventListener('change', onStyleChange);

  const getMetaBase = (el) => {
    const list = el.closest('[data-sortable-items]');
    const base = list?.getAttribute('data-item-meta-base');
    return (base && base.trim()) ? base.trim() : "{{ url('/admin/restaurants/'.$restaurant->id.'/items') }}";
  };

  const patchItemMeta = async (base, itemId, payload) => {
    const url = `${base}/${itemId}/meta`;

    const resp = await fetch(url, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    if(!resp.ok){
      const txt = await resp.text();
      throw new Error(txt || 'Meta update failed');
    }

    return await resp.json();
  };

  const setRowInactiveUI = (row, inactive) => {
    if(!row) return;
    row.classList.toggle('mb-inactive', !!inactive);
    row.setAttribute('data-item-active', inactive ? '0' : '1');

    row.querySelectorAll('[data-disable-when-inactive]').forEach(el => {
      if(el.hasAttribute('data-keep-enabled')) return;
      el.disabled = !!inactive;
      if(inactive) el.setAttribute('aria-disabled','true');
      else el.removeAttribute('aria-disabled');
    });
  };

  const setPill = (row, pillKey, show) => {
    const pill = row?.querySelector(`[data-pill="${pillKey}"]`);
    if(!pill) return;
    pill.style.display = show ? '' : 'none';
  };

  document.addEventListener('focusin', (e) => {
    const sel = e.target?.closest('select[data-item-meta]');
    if(!sel || isDisabledEl(sel)) return;
    sel.dataset.prev = sel.value;
  });

    document.addEventListener('change', async (e) => {

        let el = e.target?.closest('[data-item-meta]');

        if (!el) {
            const hidden = e.target.closest('.ui-select input[type="hidden"]');
            if (hidden) {
                el = hidden.closest('.ui-select');
            }
        }

        if(!el) return;
        if(isDisabledEl(el)) return;

        const itemId = el.getAttribute('data-item-id');
        const key = el.getAttribute('data-item-meta');
        if(!itemId || !key) return;

        const row = el.closest('[data-item-row]');
        const base = getMetaBase(el);

        const rowActive = row ? (row.getAttribute('data-item-active') !== '0') : true;

        if(!rowActive && key !== 'is_active'){
            return;
        }

        let payload = {};

        if (el.classList.contains('ui-select')) {

            const input = el.querySelector('input[type="hidden"]');
            payload[key] = Number(input.value);

        } else if (el.tagName === 'SELECT') {

            payload[key] = Number(el.value);

        } else {

            payload[key] = !!el.checked;
        }

        if(key === 'is_new') setPill(row, 'new', payload.is_new);
        if(key === 'dish_of_day') setPill(row, 'day', payload.dish_of_day);
        if(key === 'is_active') setRowInactiveUI(row, !payload.is_active);

        try{
            await patchItemMeta(base, itemId, payload);
        }catch(err){
            console.error(err);

            if (data.flash) {
                showFlash(data.flash.message, data.flash.type);
            } else {
                showFlash(
                    window.UI_LANG.delete_error || 'Error',
                    'error'
                );
            }
        }
    });

  document.querySelectorAll('[data-item-row]').forEach(row => {
    const active = row.getAttribute('data-item-active');
    if(active === '0') setRowInactiveUI(row, true);
  });

  // ----------------------------
  // Sortable
  // ----------------------------
  document.querySelectorAll('[data-sortable-items]').forEach((el) => {
    // если родитель выключен — не даём таскать
    const allowed = (el.getAttribute('data-sortable') ?? '1') !== '0';
    if(!allowed) return;

    const url = el.getAttribute('data-reorder-url');
    if(!url) return;

    new Sortable(el, {
      handle: '.mb-handle',
      animation: 150,
      onEnd: async () => {
        const ids = Array.from(el.querySelectorAll('[data-item-id]'))
          .map(x => Number(x.getAttribute('data-item-id')));

        try{
          const resp = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type':'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept':'application/json',
            },
            body: JSON.stringify({ item_ids: ids })
          });

          if(!resp.ok){
            console.error('reorder failed', await resp.text());
          }
        }catch(err){
          console.error(err);
        }
      }
    });
  });

  // ----------------------------
  // Click handlers: open/close + edit section + edit item + confirm delete
  // ----------------------------
  document.addEventListener('click', (e) => {
    const target = (e.target && e.target.nodeType === 1) ? e.target : e.target?.parentElement;
    if(!target) return;

    // confirm delete
    const delBtn = target.closest('[data-confirm-delete]');
    if(delBtn){
      if(isDisabledEl(delBtn)) return;
      openDeleteConfirm(delBtn);
      return;
    }

    // EDIT section
    const editBtn = target.closest('[data-mb-edit="section"]');
    if(editBtn){
      if(isDisabledEl(editBtn)) return;

      const modalId = (editBtn.getAttribute('data-mb-open') || '').trim();
      const modal = document.getElementById(modalId);
      if(!modal) return;

      const form = modal.querySelector('form');
      if(!form) return;

      const updateUrl = (editBtn.getAttribute('data-section-update-url') || '').trim();
      if(updateUrl) form.action = updateUrl;
      ensureMethod(form, 'PUT');

      let titles = {};
      try { titles = JSON.parse(editBtn.getAttribute('data-section-titles') || '{}'); } catch(_){ titles = {}; }

      Object.keys(titles || {}).forEach(loc => {
        const val = (titles[loc] ?? '').toString();
        const inp = modal.querySelector(`[name="title[${loc}]"]`);
        if(inp) inp.value = val;
      });

      if(modalId === 'mbModalSubcategory'){
        const parentId = (editBtn.getAttribute('data-parent-id') || '').trim();
        const el = document.getElementById('mbSubParentId');
        if(el && parentId) el.value = parentId;
      }

      openModal(modalId);
      return;
    }

    // EDIT item
    const editItemBtn = target.closest('[data-edit-item]');
    if(editItemBtn){
      if(isDisabledEl(editItemBtn)) return;

      let item;
      try { item = JSON.parse(editItemBtn.dataset.item || '{}'); } catch(_){ item = null; }
      if(!item || !item.id) return;

      const form  = document.getElementById('mbItemForm');
      if(!form) return;

      const base = "{{ url('/admin/restaurants/'.$restaurant->id.'/items') }}";
      form.action = base + '/' + item.id;
      ensureMethod(form, 'PUT');

      const sec = document.getElementById('mbItemSectionId');
      if(sec && item.section_id) sec.value = item.section_id;

      const price = form.querySelector('[name="price"]');
      if(price) price.value = (item.price ?? '').toString();

      (item.translations || []).forEach(t => {
        const loc = t.locale;
        if(!loc) return;

        const title = form.querySelector(`[name="translations[${loc}][title]"]`);
        const desc  = form.querySelector(`[name="translations[${loc}][description]"]`);
        const det   = form.querySelector(`[name="translations[${loc}][details]"]`);

        if(title) title.value = (t.title ?? '');
        if(desc)  desc.value  = (t.description ?? '');
        if(det)   det.value   = (t.details ?? '');
      });

      const style = item.style || null;
      if(style){
        ['title','desc','details'].forEach(k => {
          const s = style[k] || {};
          const fontEl  = form.querySelector(`[data-style-font="${k}"]`);
          const colorEl = form.querySelector(`[data-style-color="${k}"]`);
          const sizeEl  = form.querySelector(`[data-style-size="${k}"]`);
          if(fontEl && typeof s.font !== 'undefined') fontEl.value = s.font || '';
          if(colorEl && typeof s.color !== 'undefined') colorEl.value = s.color || '';
          if(sizeEl  && typeof s.size !== 'undefined') sizeEl.value  = s.size || sizeEl.value;
        });
        ['title','desc','details'].forEach(k => applyStyle(k));
      }

      const submit = form.querySelector('button[type="submit"]');
      if(submit) submit.textContent = "{{ __('admin.actions.save') ?? 'Save' }}";

      openModal('mbModalItem');
      return;
    }

    // OPEN create modals
    const openBtn = target.closest('[data-mb-open]');
    if(openBtn){
      if(isDisabledEl(openBtn)) return;

      const id = (openBtn.getAttribute('data-mb-open') || '').trim();
      const modal = document.getElementById(id);

      if(modal){
        const form = modal.querySelector('form');
        if(form) ensureMethod(form, null);
      }

      if(id === 'mbModalSubcategory'){
        const parentId = (openBtn.getAttribute('data-parent-id') || '').trim();
        const el = document.getElementById('mbSubParentId');
        if(el) el.value = parentId;
      }

      if(id === 'mbModalItem'){
        resetItemModalForCreate();

        const sectionId = (openBtn.getAttribute('data-section-id') || '').trim();
        const secEl = document.getElementById('mbItemSectionId');
        if(secEl) secEl.value = sectionId;

        const form = document.getElementById('mbItemForm');
        if(form){
          const base = "{{ url('/admin/restaurants/'.$restaurant->id.'/sections') }}";
          form.action = base + "/" + sectionId + "/items";
          ensureMethod(form, null);
        }
      }

      openModal(id);
      return;
    }

    // close
    if(target.matches('[data-mb-close]')){
      const m = target.closest('.modal');
      closeModal(m);
      return;
    }
  });

  // ESC closes all
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape'){
      document.querySelectorAll('.modal[aria-hidden="false"]').forEach(m => closeModal(m));
    }
  });

  // ----------------------------
  // SuperAdmin: Show/Hide deleted
  // ----------------------------
  const showDeletedToggle = document.getElementById('mbShowDeleted');
  if(showDeletedToggle){
    const key = 'mb_show_deleted';
    const saved = localStorage.getItem(key);
    if(saved !== null) showDeletedToggle.checked = (saved === '1');

    const apply = () => {
      const show = !!showDeletedToggle.checked;
      localStorage.setItem(key, show ? '1' : '0');
      document.querySelectorAll('[data-deleted="1"]').forEach(el => {
        el.style.display = show ? '' : 'none';
      });
    };

    showDeletedToggle.addEventListener('change', apply);
    apply();
  }

  // Image preview in item modal
  const imgInp = document.getElementById('mbItemImageInput');
  const imgPrev = document.getElementById('mbItemImagePreview');
  if(imgInp && imgPrev){
    imgInp.addEventListener('change', () => {
      const f = imgInp.files && imgInp.files[0];
      if(!f) return;
      const url = URL.createObjectURL(f);
      imgPrev.src = url;
    });
  }


  // OPEN text modal (Description/Details)
  document.addEventListener('click', (e) => {
    const target = e.target;
    const textBtn = target.closest('[data-open-text-modal]');
    if (!textBtn) return;

    const title = (textBtn.getAttribute('data-text-title') || 'Text').trim();

    let body = '';
    try {
      body = JSON.parse(textBtn.getAttribute('data-text-body') || '""');
    } catch (err) {
      body = (textBtn.getAttribute('data-text-body') || '');
    }

    const tEl = document.getElementById('mbTextModalTitle');
    const bEl = document.getElementById('mbTextModalBody');

    if (tEl) tEl.textContent = title;
    if (bEl) bEl.textContent = body || '';

    openModal('mbTextModal');
  });

    // PREVIEW
    document.addEventListener('change', function (e) {
        const input = e.target;

        if (input.id !== 'mbItemImageInput') return;

        const file = input.files[0];
        if (!file) return;

        const preview = document.getElementById('mbItemImagePreview');
        if (preview) {
            preview.src = URL.createObjectURL(file);
        }

        const del = document.getElementById('mbItemImageDelete');
        if (del) del.style.display = 'inline-block';
    });


// DELETE IMAGE
    document.addEventListener('change', function (e) {
        if (e.target.id !== 'mbItemImageInput') return;

        const file = e.target.files[0];
        if (!file) return;

        const preview = document.getElementById('mbItemImagePreview');
        const deleteBtn = document.getElementById('mbItemImageDelete');
        const removeInput = document.getElementById('mbRemoveImage');

        if (preview) {
            preview.src = URL.createObjectURL(file);
        }

        if (deleteBtn) {
            deleteBtn.style.display = 'block';
        }

        if (removeInput) {
            removeInput.value = '0';
        }
    });

    document.getElementById('mbItemImageDelete')?.addEventListener('click', function () {
        const preview = document.getElementById('mbItemImagePreview');
        const input = document.getElementById('mbItemImageInput');
        const removeInput = document.getElementById('mbRemoveImage');

        if (preview) {
            preview.src = preview.dataset.fallbackSrc || '';
        }

        if (input) {
            input.value = '';
        }

        if (removeInput) {
            removeInput.value = '1';
        }

        this.style.display = 'none';
    });

    document.addEventListener('click', async (e) => {

        const btn = e.target.closest('[data-item-remove-image]');
        if (!btn) return;

        if (btn.disabled) return;

        const itemId = btn.getAttribute('data-item-remove-image');
        if (!itemId) return;

        const base = btn.closest('[data-sortable-items]')
            ?.getAttribute('data-item-meta-base');

        if (!base) return;

        try {

            window.showLoader?.();

            const resp = await fetch(`${base}/${itemId}/image`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (!resp.ok) {
                throw new Error(await resp.text());
            }

            const img = document.querySelector(`[data-item-image="${itemId}"]`);

            if (img) {
                img.src = "{{ app(\App\Services\ImageService::class)->food(null) }}";
            }
            if (data.flash) {
                showFlash(data.flash.message, data.flash.type);
            } else {
                showFlash(
                    window.UI_LANG.saved || 'Saved',
                    'success'
                );
            }

        } catch (err) {
            console.error(err);
            if (data.flash) {
                showFlash(data.flash.message, data.flash.type);
            } else {
                showFlash(
                    window.UI_LANG.delete_error || 'Error',
                    'error'
                );
            }
        } finally {
            window.hideLoader?.();
        }

    });

    const mbInput = document.getElementById('mbSearchInput');
    const mbResults = document.getElementById('mbSearchResults');

    if (mbInput) {

        mbInput.addEventListener('input', () => {

            const val = mbInput.value.toLowerCase().trim();
            mbResults.innerHTML = '';

            if (!val) {
                mbResults.style.display = 'none';
                return;
            }

            const all = document.querySelectorAll('[data-search]');
            let found = [];

            all.forEach(el => {
                const searchText = (el.dataset.search || '').toLowerCase();

                if (searchText.includes(val)) {
                    found.push(el);
                }
            });

            if (!found.length) {
                mbResults.style.display = 'none';
                return;
            }

            found.slice(0, 10).forEach(el => {

                const div = document.createElement('div');
                div.className = 'mb-search-item';

                const type = el.dataset.type;
                const text = el.dataset.search;

                div.innerHTML = `
                <div class="mb-search-line ${type}">
                    ${text}
                </div>
            `;

                div.onclick = () => {

                    el.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    el.classList.add('mb-highlight');

                    setTimeout(() => {
                        el.classList.remove('mb-highlight');
                    }, 1500);

                    mbResults.style.display = 'none';
                };

                mbResults.appendChild(div);
            });

            mbResults.style.display = 'block';
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.mb-search')) {
                mbResults.style.display = 'none';
            }
        });
    }

    window.addEventListener('load', () => {

        let sectionId = window.__SCROLL_TO_SECTION__;
        let itemId = window.__SCROLL_TO_ITEM__;

        let attempts = 0;

        const interval = setInterval(() => {

            if (sectionId) {
                const el = document.querySelector(`[data-section-id="${sectionId}"]`);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('mb-highlight');

                    setTimeout(() => el.classList.remove('mb-highlight'), 2000);
                    clearInterval(interval);
                    return;
                }
            }

            if (itemId) {
                const el = document.querySelector(`[data-item-id="${itemId}"]`);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('mb-highlight');

                    setTimeout(() => el.classList.remove('mb-highlight'), 2000);
                    clearInterval(interval);
                    return;
                }
            }

            if (attempts > 20) {
                console.warn('Scroll target not found');
                clearInterval(interval);
            }

            attempts++;

        }, 100);

    });


})();
</script>
