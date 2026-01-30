<div class="modal" id="mbTextModal" aria-hidden="true">
  <div class="modal__backdrop" data-mb-close></div>

  <div class="modal__panel" style="max-width:720px;">
    <div class="mb-row">
      <strong id="mbTextModalTitle">{{ __('admin.menu_builder.text') ?? 'Текст' }}</strong>
      <button class="btn small" type="button" data-mb-close aria-label="{{ __('admin.actions.close') ?? 'Close' }}">✕</button>
    </div>

    <div id="mbTextModalBody"
         style="margin-top:12px; white-space:pre-wrap; color:var(--text); line-height:1.5;">
    </div>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
      <button type="button" class="btn secondary" data-mb-close>
        {{ __('admin.actions.close') }}
      </button>
    </div>
  </div>
</div>
