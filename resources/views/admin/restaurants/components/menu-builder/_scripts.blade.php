<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
(function(){
  const openModal = (id) => {
    const m = document.getElementById(id);
    if(m) m.setAttribute('aria-hidden','false');
  };

  const closeModal = (m) => {
    if(m) m.setAttribute('aria-hidden','true');
  };

  // ----------------------------
  // Click handlers: open/close
  // ----------------------------
  document.addEventListener('click', (e) => {
    const target = (e.target && e.target.nodeType === 1) ? e.target : e.target?.parentElement;
    if(!target) return;

    const openBtn = target.closest('[data-mb-open]');
    if(openBtn){
      const id = (openBtn.getAttribute('data-mb-open') || '').trim();

      if(id === 'mbModalSubcategory'){
        const parentId = (openBtn.getAttribute('data-parent-id') || '').trim();
        const el = document.getElementById('mbSubParentId');
        if(el) el.value = parentId;
      }

      if(id === 'mbModalItem'){
        const sectionId = (openBtn.getAttribute('data-section-id') || '').trim();
        const secEl = document.getElementById('mbItemSectionId');
        if(secEl) secEl.value = sectionId;

        const form = document.getElementById('mbItemForm');
        if(form){
          const base = "{{ url('/admin/restaurants/'.$restaurant->id.'/sections') }}";
          form.action = base + "/" + sectionId + "/items";
        }
      }

      openModal(id);
      return;
    }

    if(target.matches('[data-mb-close]')){
      const m = target.closest('.modal');
      closeModal(m);
      return;
    }
  });

  // ----------------------------
  // ESC closes all open modals
  // ----------------------------
  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape'){
      document.querySelectorAll('.modal[aria-hidden="false"]').forEach(m => closeModal(m));
    }
  });

  // ----------------------------
  // Sortable (items only)
  // ----------------------------
  document.querySelectorAll('[data-sortable-items]').forEach((el) => {
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
// Word-like preview (NO caching modal)
// ----------------------------
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
  if(!itemModal) return;
  if(!itemModal.contains(target)) return;

  const f = target.closest('[data-style-font],[data-style-color],[data-style-size]');
  if(!f) return;

  const key =
    f.getAttribute('data-style-font') ||
    f.getAttribute('data-style-color') ||
    f.getAttribute('data-style-size');

  if(key) applyStyle(key);
};

// initial apply after page load (на случай если модалка уже в DOM)
['title','desc','details'].forEach(k => applyStyle(k));

document.addEventListener('input', onStyleChange);
document.addEventListener('change', onStyleChange);



  // ----------------------------
  // Edit item (modal) - if present
  // ----------------------------
  document.querySelectorAll('[data-edit-item]').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = JSON.parse(btn.dataset.item);

      const form = document.getElementById('item-edit-form');
      const base = "{{ url('/admin/restaurants/'.$restaurant->id.'/items') }}";

      if(!form) return;

      form.action = base + '/' + item.id;

      const idEl = document.getElementById('edit-item-id');
      if(idEl) idEl.value = item.id;

      // translations
      (item.translations || []).forEach(t => {
        const title = form.querySelector(`[data-edit-title="${t.locale}"]`);
        const desc  = form.querySelector(`[data-edit-description="${t.locale}"]`);
        const det   = form.querySelector(`[data-edit-details="${t.locale}"]`);

        if (title) title.value = t.title ?? '';
        if (desc)  desc.value  = t.description ?? '';
        if (det)   det.value   = t.details ?? '';
      });

      // flags
      const showImage = form.querySelector('[data-edit-show-image]');
      const isNew     = form.querySelector('[data-edit-is-new]');
      const dish      = form.querySelector('[data-edit-dish]');
      const spicy     = form.querySelector('[data-edit-spicy]');

      if(showImage) showImage.checked = !!item.show_image;
      if(isNew)     isNew.checked     = !!item.is_new;
      if(dish)      dish.checked      = !!item.dish_of_day;
      if(spicy)     spicy.value       = item.spicy ?? 0;

      // IMPORTANT: openModal expects id without '#'
      openModal('item-edit-modal');
    });
  });

})();
</script>
