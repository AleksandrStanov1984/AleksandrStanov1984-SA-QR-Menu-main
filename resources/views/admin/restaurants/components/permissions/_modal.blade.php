<div id="permModal" class="modal" aria-hidden="true">

    <div class="modal__backdrop" data-close-perm-modal></div>

    <div class="modal__panel"
         role="dialog"
         aria-modal="true"
         aria-labelledby="permModalTitle">

        <div class="modal__head">

            <div id="permModalTitle"></div>

            <button type="button"
                    class="btn small secondary"
                    data-close-perm-modal>
                ✕
            </button>
        </div>

        <div class="modal__body">
            <div id="permModalBody"></div>
        </div>

        <div class="modal__foot">

            <button type="button"
                    class="btn secondary"
                    data-close-perm-modal>
                {{ __('admin.common.cancel') ?? 'Отмена' }}
            </button>

            @if(($mode ?? 'view') === 'edit')
                <button class="btn ok"
                        type="submit"
                        form="permForm">
                    {{ __('admin.permissions.save') }}
                </button>
            @endif

        </div>
    </div>
</div>
