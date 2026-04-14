<div class="restaurant-header">

    @if(!empty($vm->branding['logo']))
        <div class="menu-logo">
            <img src="{{ $vm->branding['logo'] }}" alt="logo">
        </div>
    @endif

    <h1 class="restaurant-title">
        {{ $vm->merchant->name }}
    </h1>

    @php
        $mapUrl = $vm->merchant->map_url ?? null;
    @endphp

    @if($mapUrl)
        <a
            href="{{ $mapUrl }}"
            target="_blank"
            class="restaurant-address"
        >
            <i class="ri-map-pin-line"></i>

            <span>
                {{ $vm->merchant->address }}
            </span>
        </a>
    @else
        <div class="restaurant-address">
            <i class="ri-map-pin-line"></i>

            <span>
                {{ $vm->merchant->address }}
            </span>
        </div>
    @endif

    @php
        $today = $vm->todayHours ?? ($vm->hours[0] ?? null);
    @endphp

    @if($today)
        <button
            type="button"
            class="restaurant-status {{ $vm->showStatus ? 'status-' . $vm->status : '' }}"
            @if($vm->showHoursModal)
                data-open-modal="hours"
            @endif
        >
            @if($vm->showStatus)
                @if($vm->status === 'open')
                    <i class="ri-checkbox-circle-line"></i>
                @elseif($vm->status === 'closing_soon')
                    <i class="ri-alarm-warning-line"></i>
                @else
                    <i class="ri-close-circle-line"></i>
                @endif
            @endif

            <span id="statusLabel">
                @if(!empty($today['closed']))
                    {{ $today['label'] }}
                @else
                    {{ $today['label'] }} {{ $today['open'] }} – {{ $today['close'] }}
                @endif

                @if($vm->showStatus)
                    •
                    @if($vm->status === 'open')
                        {{ __('public.open') }}
                    @elseif($vm->status === 'closing_soon')
                        {{ __('public.closing_soon') }}
                    @else
                        {{ __('public.closed') }}
                    @endif
                @endif
            </span>
        </button>
    @endif

</div>

@php
    $marketingBanners = collect($vm->promoBanners ?? []);
    $marketingItems = collect($vm->carouselItems ?? []);
@endphp

@if($marketingBanners->isNotEmpty() || $marketingItems->isNotEmpty())
    @include('public.templates.united.blocks.header.courusel-header', [
        'items' => $marketingItems,
    ])
@endif
