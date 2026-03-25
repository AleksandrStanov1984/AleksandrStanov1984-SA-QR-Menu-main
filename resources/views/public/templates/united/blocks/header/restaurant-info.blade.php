<div class="restaurant-header">

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
        {{ $today['label'] }} {{ $today['open'] }} – {{ $today['close'] }}

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
