@php
    $qrPath = optional($restaurant->qr)->qr_path;
@endphp

@include('admin.restaurants.components.qr._styles')

<div class="card" style="margin-top:16px;">

    <div class="mb-row">
        <h2>{{ __('qr.title') }}</h2>
    </div>

    <div class="qr-block">

        {{-- LEFT / QR --}}
        <div class="qr-left">
            <div class="qr-preview">
                <img
                    src="{{ app(\App\Services\ImageService::class)->qr($qrPath) }}"
                    alt="QR"
                >
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="qr-right">

            <div class="qr-actions">

                {{-- GENERATE --}}
                <button
                    type="button"
                    class="btn btn-success"
                    data-generate-qr
                    data-restaurant-id="{{ $restaurant->id }}"
                >
                    {{ empty($qrPath)
                        ? __('qr.buttons.create')
                        : __('qr.buttons.regenerate') }}
                </button>

                {{-- UPLOAD --}}
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-mb-open="mbModalQrUpload"
                >
                    {{ __('qr.buttons.upload') }}
                </button>

                {{-- DOWNLOAD DROPDOWN --}}
                @if(!empty($qrPath))
                    <div class="qr-download">

                        <button type="button" class="btn btn-success" id="qrDownloadBtn">
                            Скачать
                        </button>

                        <div id="qrDropdown" class="qr-dropdown">

                            <a href="{{ route('admin.restaurants.qr.download', [$restaurant, 'svg']) }}">
                                SVG
                            </a>

                            <a href="{{ route('admin.restaurants.qr.download', [$restaurant, 'pdf']) }}">
                                PDF
                            </a>

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

<div id="qrLoader" class="qr-loader" style="display:none;">
    <div class="qr-loader__backdrop"></div>

    <div class="qr-loader__content">
        <div class="qr-spinner"></div>
        <div class="qr-loader__text">
            {{ __('qr.loading.title') }}
        </div>
    </div>
</div>

@include('admin.restaurants.components.qr._modal-qr')
@include('admin.restaurants.components.qr._scripts')
