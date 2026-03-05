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

    <button
        type="button"
        class="restaurant-status status-{{ $vm->status }}"
        data-open-modal="hours"
    >

        @if($vm->status === 'open')
            <i class="ri-checkbox-circle-line"></i>
        @elseif($vm->status === 'closing_soon')
            <i class="ri-alarm-warning-line"></i>
        @else
            <i class="ri-close-circle-line"></i>
        @endif

        <span id="statusLabel">

            @if($vm->status === 'open')

                {{ __('public.open') }}

            @elseif($vm->status === 'closing_soon')

                {{ __('public.closing_soon') }}

            @else

                {{ __('public.closed') }}

            @endif

        </span>

    </button>

</div>
