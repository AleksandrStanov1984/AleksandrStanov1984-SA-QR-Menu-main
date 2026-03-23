<div class="modal" id="mbModalQrUpload" aria-hidden="true">

    <div class="modal__backdrop" data-mb-close></div>

    <div class="modal__panel" style="max-width:500px;">

        <div class="mb-row">
            <strong>{{ __('qr.modal.title') }}</strong>

            <button class="btn small"
                    type="button"
                    data-mb-close
                    aria-label="Close">
                ✕
            </button>
        </div>

        <div class="mb-row" style="margin-top:12px;">
            <label>{{ __('qr.modal.logo') }}</label>
            <input type="file" name="logo" id="qrLogoInput">
        </div>

        <div class="mb-row">
            <label>{{ __('qr.modal.background') }}</label>
            <input type="file" name="background" id="qrBgInput">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">

            <button
                class="btn"
                type="button"
                data-generate-qr
                data-restaurant-id="{{ $restaurant->id }}"
                id="qrGenerateBtn"
            >
                {{ __('qr.modal.generate') }}
            </button>

            <button class="btn secondary"
                    type="button"
                    data-mb-close>
                {{ __('qr.buttons.cancel') }}
            </button>

        </div>

    </div>

</div>
