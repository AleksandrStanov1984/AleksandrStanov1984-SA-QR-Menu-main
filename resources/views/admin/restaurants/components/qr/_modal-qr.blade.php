{{-- resources/views/admin/restaurants/components/qr/_modal-qr.blade.php --}}

@php
    $qr = $restaurant->qr;
    $image = app(\App\Services\ImageService::class);

    $logoUrl = $qr?->logo_path ? $image->qr($qr->logo_path) : null;
    $bgUrl   = $qr?->background_path ? $image->qr($qr->background_path) : null;
@endphp

<div class="modal" id="mbModalQrUpload" data-restaurant-id="{{ $restaurant->id }}" aria-hidden="true">

    <div class="modal__backdrop" data-mb-close></div>

    <div class="modal__panel" style="max-width:500px;">

        {{-- HEADER --}}
        <div class="mb-row qr-head">
            <strong>{{ __('qr.modal.title') }}</strong>

            <button class="btn small qr-close"
                    type="button"
                    data-mb-close>
                ✕
            </button>
        </div>

        @php
            $qr = $restaurant->qr;
            $image = app(\App\Services\ImageService::class);

            $logoUrl = $qr?->logo_path ? $image->qr($qr->logo_path) : null;
            $bgUrl   = $qr?->background_path ? $image->qr($qr->background_path) : null;
        @endphp

        <div data-restaurant-id="{{ $restaurant->id }}"></div>

        {{-- ================= LOGO ================= --}}
        <div class="mb-row" style="margin-top:12px;">
            <label>{{ __('qr.modal.logo') }}</label>

            <div class="qr-upload">

                <div class="qr-preview">
                    <img
                        id="qrLogoPreview"
                        src="{{ $logoUrl ?? '' }}"
                        data-existing="{{ $logoUrl ? '1' : '0' }}"
                        alt=""
                        @if(!$logoUrl) hidden @endif
                    >

                    <span class="qr-placeholder" @if($logoUrl) hidden @endif>
                Logo
            </span>

                    <button
                        class="qr-remove"
                        type="button"
                        id="qrLogoRemove"
                        @if(!$logoUrl) hidden @endif
                    >
                        ✕
                    </button>
                </div>

                <button
                    class="btn small qr-upload-btn"
                    type="button"
                    id="qrLogoPick"
                >
                    {{ $logoUrl ? 'Change' : 'Upload' }}
                </button>

                <input
                    type="file"
                    name="logo"
                    id="qrLogoInput"
                    hidden
                >
            </div>
        </div>

        {{-- BACKGROUND --}}
        <div class="mb-row">
            <label>{{ __('qr.modal.background') }}</label>

            <div class="qr-upload">

                <div class="qr-preview">
                    <img
                        id="qrBgPreview"
                        src="{{ $bgUrl ?? '' }}"
                        data-existing="{{ $bgUrl ? '1' : '0' }}"
                        alt=""
                        @if(!$bgUrl) hidden @endif
                    >

                    <span class="qr-placeholder" @if($bgUrl) hidden @endif>
                Background
            </span>

                    <button
                        class="qr-remove"
                        type="button"
                        id="qrBgRemove"
                        @if(!$bgUrl) hidden @endif
                    >
                        ✕
                    </button>
                </div>

                <button
                    class="btn small qr-upload-btn"
                    type="button"
                    id="qrBgPick"
                >
                    {{ $bgUrl ? 'Change' : 'Upload' }}
                </button>

                <input
                    type="file"
                    name="background"
                    id="qrBgInput"
                    hidden
                >
            </div>
        </div>

        {{-- ACTIONS --}}
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">

            <button class="btn"
                    type="button"
                    data-generate-qr
                    data-restaurant-id="{{ $restaurant->id }}"
                    id="qrGenerateBtn">
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
