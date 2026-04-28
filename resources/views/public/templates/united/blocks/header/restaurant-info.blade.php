{{-- resources/views/public/templates/united/blocks/header/restaurant-info.blade.php --}}

<div class="restaurant-header">

    @if(!empty($vm->branding['logo']))
        <div class="menu-logo">
            <img
                src="{{ $vm->branding['logo'] }}"
                alt="logo"
                loading="eager"
                fetchpriority="high"
                width="120"
                height="40"
            >
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


                <span id="statusLabel" class="status-label">

    <span class="status-day">
        {{ $today['label'] }}
    </span>

    @if(empty($today['closed']))
                        <span class="status-time">
            {{ $today['open'] }} – {{ $today['close'] }}
        </span>
                    @endif

                    @if($vm->showStatus)
                        <span class="status-state">
            @if($vm->status === 'open')
                                {{ __('public.open') }}
                            @elseif($vm->status === 'closing_soon')
                                {{ __('public.closing_soon') }}
                            @else
                                {{ __('public.closed') }}
                            @endif
        </span>
                    @endif

</span>
        </button>
    @endif

</div>
