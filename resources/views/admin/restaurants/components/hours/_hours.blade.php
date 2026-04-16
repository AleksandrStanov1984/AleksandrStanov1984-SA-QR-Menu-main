{{-- resources/views/admin/restaurants/components/hours/_hours.blade.php --}}
{{-- admin/restaurants/components/hours/_hours --}}
@php
    $days = [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        0 => 'sunday',
    ];

    $hours = $restaurant->hours->keyBy('day_of_week');
    $today = now()->dayOfWeek;
@endphp

@include('admin.restaurants.components.hours._styles')

<div class="card wh-card">

    <div class="wh-card__header">
        <strong>{{ __('hours.title') }}</strong>

        <button class="btn small" type="button" data-mb-open-hours>
            {{ __('hours.edit') }}
        </button>
    </div>

    <div class="wh-card__list">
        @foreach($days as $day => $key)
            @php $row = $hours[$day] ?? null; @endphp

            <div class="wh-card__row {{ $today === $day ? 'is-today' : '' }}">
                <div class="wh-card__day">
                    {{ __('hours.days.' . $key) }}
                </div>

                <div class="wh-card__value">
                    @if(!$row || $row->is_closed)
                        <span class="wh-card__closed">{{ __('hours.closed') }}</span>
                    @else
                        <span class="wh-card__time">
                            <span>{{ $row->open_time?->format('H:i') ?? '--:--' }}</span>
                            <span class="wh-card__dash">—</span>
                            <span>{{ $row->close_time?->format('H:i') ?? '--:--' }}</span>
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>

@include('admin.restaurants.components.hours._hours-modal')
@include('admin.restaurants.components.hours._scripts')
