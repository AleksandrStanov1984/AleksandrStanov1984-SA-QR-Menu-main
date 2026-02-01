<script>
(function () {
  const modal = document.getElementById('permModal');
  const titleEl = document.getElementById('permModalTitle');
  const bodyEl  = document.getElementById('permModalBody');

  const isEdit = @json(($mode ?? 'view') === 'edit');

  function openPermModal(groupKey, groupTitle) {
    const tpl = document.getElementById('tplPermGroup_' + groupKey);
    if (!tpl) return;

    titleEl.textContent = groupTitle || groupKey;
    bodyEl.innerHTML = '';
    bodyEl.appendChild(tpl.content.cloneNode(true));

    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closePermModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    bodyEl.innerHTML = '';
    document.body.style.overflow = '';
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-open-perm-group]');
    if (btn) {
      openPermModal(btn.getAttribute('data-group-key'), btn.getAttribute('data-group-title'));
      return;
    }

    if (e.target.closest('[data-close-perm-modal]')) {
      closePermModal();
      return;
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.style.display === 'block') closePermModal();
  });

  if (!isEdit) return;

  // Нормализация: снятые чекбоксы должны сохранить 0
  const form = document.querySelector('form[data-perm-form="1"]');
  if (!form) return;

  // список всех perm keys берём из templates (они всегда в DOM)
  const allKeys = Array.from(document.querySelectorAll('template[id^="tplPermGroup_"]'))
    .flatMap(tpl => Array.from(tpl.content.querySelectorAll('input[type="checkbox"][name^="perm["]')))
    .map(i => i.name);

  form.addEventListener('submit', () => {
    // убираем старые hidden-заглушки
    form.querySelectorAll('input[data-perm-zero="1"]').forEach(x => x.remove());

    // отмеченные чекбоксы (в текущей модалке)
    const checked = new Set(
      Array.from(form.querySelectorAll('#permModalBody input[type="checkbox"]:checked'))
        .map(i => i.name)
    );

    // на каждый ключ, который НЕ отмечен, добавим hidden = 0
    allKeys.forEach(name => {
      if (!checked.has(name)) {
        const h = document.createElement('input');
        h.type = 'hidden';
        h.name = name;
        h.value = '0';
        h.setAttribute('data-perm-zero', '1');
        form.appendChild(h);
      }
    });
  });
})();
</script>
