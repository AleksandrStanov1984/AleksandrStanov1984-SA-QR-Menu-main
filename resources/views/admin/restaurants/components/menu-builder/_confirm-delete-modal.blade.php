<div class="modal" id="mbConfirmDelete" aria-hidden="true">
  <div class="modal__backdrop" data-mb-close></div>

  <div class="modal__panel" style="max-width:520px;">
    <div class="mb-row">
      <strong>{{ __('admin.confirm.title') ?? 'Подтверждение' }}</strong>
      <button class="btn small" type="button" data-mb-close aria-label="{{ __('admin.actions.close') ?? 'Close' }}">✕</button>
    </div>

    <div class="mb-muted" style="margin-top:10px; line-height:1.5;">
      <div id="mbConfirmDeleteText">
        {{ __('admin.confirm.delete_generic') ?? 'Вы уверены, что хотите удалить?' }}
      </div>
      <div class="mb-mini" id="mbConfirmDeleteHint" style="margin-top:6px; opacity:.9;"></div>
    </div>

    <form id="mbConfirmDeleteForm" method="POST" action="" style="margin-top:14px;">
      @csrf
      @method('DELETE')

      <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
        <button class="btn secondary" type="button" data-mb-close>
          {{ __('admin.actions.cancel') ?? 'Отмена' }}
        </button>

        <button class="btn danger" type="submit">
          {{ __('admin.actions.delete') ?? 'Удалить' }}
        </button>
      </div>
    </form>
  </div>
</div>
