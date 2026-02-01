<div id="permModal" class="modal" aria-hidden="true" style="display:none;">
  <div class="modal__backdrop" data-close-perm-modal></div>

  <div class="modal__panel" role="dialog" aria-modal="true" style="max-width:860px; width:calc(100% - 24px);">
    <div class="modal__head" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
      <div style="font-weight:800; font-size:18px;" id="permModalTitle"></div>
      <button type="button" class="btn small secondary" data-close-perm-modal>✕</button>
    </div>

    <div class="modal__body" style="margin-top:12px;">
      <div id="permModalBody"
      style="max-height:60vh;
          overflow:auto;
          padding-right:6px;
      "></div>
    </div>


    <div class="modal__foot" style="margin-top:14px; display:flex; justify-content:flex-end; gap:10px;">
      <button type="button" class="btn secondary" data-close-perm-modal>
        {{ __('admin.common.cancel') ?? 'Отмена' }}
      </button>

      @if(($mode ?? 'view') === 'edit')
        <button class="btn ok" type="submit">
          {{ __('admin.permissions.save') }}
        </button>
      @endif
    </div>
  </div>
</div>
