{{-- resources/views/admin/restaurants/components/qr/_qr.blade.php --}}

@php
    $qrPath = optional($restaurant->qr)->qr_path;
@endphp

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

        {{-- MENU URL --}}
        @if(!empty($qrPath) && !empty($menuUrl))
            <div style="margin-top:14px; display:flex; justify-content:center;">

                <div style="
            display:flex;
            align-items:center;
            gap:6px;
            background:rgba(255,255,255,0.06);
            border:1px solid rgba(255,255,255,0.12);
            border-radius:12px;
            padding:6px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        ">

                    <input
                        type="text"
                        value="{{ $menuUrl }}"
                        readonly
                        onclick="window.open(this.value, '_blank')"
                        style="
                    border:none;
                    background:transparent;
                    color:#d1e3ff;
                    font-size:12px;
                    padding:6px 8px;
                    outline:none;
                    min-width:280px;
                    cursor:pointer;
                "
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'"
                    >

                    <button
                        type="button"
                        data-copy-url="{{ $menuUrl }}"
                        style="
                    border:none;
                    background:rgba(255,255,255,0.08);
                    border-radius:8px;
                    padding:6px 8px;
                    cursor:pointer;
                    transition:0.2s;
                "
                        onmouseover="this.style.background='rgba(255,255,255,0.18)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.08)'"
                        title="Copy"
                    >
                        📋
                    </button>

                </div>

            </div>
        @endif

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
                            {{ __('qr.buttons.download') }}
                        </button>

                        <div id="qrDropdown" class="qr-dropdown">

                            <a
                                href="{{ route('admin.restaurants.qr.download', [$restaurant, 'svg']) }}"
                                data-qr-download="svg"
                            >
                                SVG
                            </a>

                            <a
                                href="{{ route('admin.restaurants.qr.download', [$restaurant, 'pdf']) }}"
                                data-qr-download="pdf"
                            >
                                PDF
                            </a>

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

@include('admin.restaurants.components.qr._modal-qr')
@include('admin.restaurants.components.qr._scripts')
