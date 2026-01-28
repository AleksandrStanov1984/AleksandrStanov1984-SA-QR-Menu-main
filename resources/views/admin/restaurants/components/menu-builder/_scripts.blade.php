<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
(function(){
  const openModal = (id) => { const m = document.getElementById(id); if(m) m.setAttribute('aria-hidden','false'); };
  const closeModal = (m) => { if(m) m.setAttribute('aria-hidden','true'); };

  document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-mb-open]');
    if(openBtn){
      const id = openBtn.getAttribute('data-mb-open');

      if(id === 'mbModalSubcategory'){
        document.getElementById('mbSubParentId').value = openBtn.getAttribute('data-parent-id') || '';
      }

      if(id === 'mbModalItem'){
        const sectionId = openBtn.getAttribute('data-section-id') || '';
        document.getElementById('mbItemSectionId').value = sectionId;

        const form = document.getElementById('mbItemForm');
        const base = "{{ url('/admin/restaurants/'.$restaurant->id.'/sections') }}";
        form.action = base + "/" + sectionId + "/items";
      }

      openModal(id);
      return;
    }

    if(e.target.matches('[data-mb-close]')){
      const m = e.target.closest('.modal');
      closeModal(m);
    }
  });

  document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape'){
      document.querySelectorAll('.modal[aria-hidden="false"]').forEach(m => closeModal(m));
    }
  });

  // Sortable (items only)
  document.querySelectorAll('[data-sortable-items]').forEach((el) => {
    const url = el.getAttribute('data-reorder-url');
    if(!url) return;

    new Sortable(el, {
      handle: '.mb-handle',
      animation: 150,
      onEnd: async () => {
        const ids = Array.from(el.querySelectorAll('[data-item-id]')).map(x => Number(x.getAttribute('data-item-id')));
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
          if(!resp.ok){ console.error('reorder failed', await resp.text()); }
        }catch(err){ console.error(err); }
      }
    });
  });

  // Word-like preview
  const applyStyle = (key) => {
    const font = document.querySelector(`[data-style-font="${key}"]`)?.value || '';
    const color = document.querySelector(`[data-style-color="${key}"]`)?.value || '';
    const size = document.querySelector(`[data-style-size="${key}"]`)?.value || '';

    document.querySelectorAll(`[data-text-field="${key}"]`).forEach(inp => {
      if(font) inp.style.fontFamily = font;
      if(color) inp.style.color = color;
      if(size) inp.style.fontSize = Number(size) + 'px';
    });
  };

  ['title','desc','details'].forEach(k => applyStyle(k));

  document.addEventListener('input', (e) => {
    const f = e.target.closest('[data-style-font],[data-style-color],[data-style-size]');
    if(!f) return;
    const key = f.getAttribute('data-style-font') || f.getAttribute('data-style-color') || f.getAttribute('data-style-size');
    if(key) applyStyle(key);
  });

})();
</script>
