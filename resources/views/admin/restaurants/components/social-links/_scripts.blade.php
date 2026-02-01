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

  // ----- URLs (blade-safe) -----
  const ROUTE_STORE = @json(route('admin.restaurants.social_links.store', $restaurant));
  const URL_UPDATE_BASE = @json(url('/admin/restaurants/'.$restaurant->id.'/social-links'));
  const CSRF_TOKEN = @json(csrf_token());

  // ----- open/close -----
  function showModal() {
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function hideModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    // важно: при закрытии чистим превью/флаг
    resetIconPreview();
    if (inputTitle) inputTitle.value = '';
    if (inputUrl) inputUrl.value = '';
    if (methodEl) methodEl.value = 'POST';
    if (form) form.action = ROUTE_STORE;
  }

  // ----- icon helpers -----
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

  // ----- open create/edit -----
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

  // ----- open buttons -----
  openBtns.forEach(btn => btn.addEventListener('click', openCreate));

  // ----- CLOSE HANDLING (новый стандарт) -----
  // 1) Закрытие по любому элементу внутри модалки с data-modal-close
  modal.addEventListener('click', (e) => {
    if (e.target.closest('[data-modal-close]')) {
      e.preventDefault();
      hideModal();
    }
  });

  // 2) На всякий случай: ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.style.display !== 'none') {
      hideModal();
    }
  });

  // ----- edit/delete handling -----
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
      const text = delBtn.getAttribute('data-delete-text') || 'Delete?';
      const hint = delBtn.getAttribute('data-delete-hint') || '';
      if (!url) return;

      if (confirm(text + (hint ? ("\n\n" + hint) : ""))) {
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
      }
      return;
    }
  });

  // ----- remove icon -----
  if (removeIconBtn) {
    removeIconBtn.addEventListener('click', () => {
      if (removeIconHidden) removeIconHidden.value = '1';
      setIconPreview('');
    });
  }

  // ----- file preview -----
  if (iconFile) {
    iconFile.addEventListener('change', () => {
      const file = iconFile.files && iconFile.files[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      setIconPreview(url);

      // если выбрали новый — remove_icon снимаем
      if (removeIconHidden) removeIconHidden.value = '0';
    });
  }

  // ----- master accordion -----
  const master = document.querySelector('[data-sl-master]');
  if (master) {
    master.addEventListener('toggle', () => {
      const open = master.open;
      document.querySelectorAll('[data-sl-item]').forEach(d => { d.open = open; });
    });
  }
})();
</script>
