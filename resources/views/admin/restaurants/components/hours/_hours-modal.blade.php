<div class="modal" id="mbModalHours" aria-hidden="true" style="display:none;">

    <div class="modal__backdrop" data-mb-close></div>

    <div class="modal__panel wh-modal__panel">

        <div class="wh-modal__header">
            <strong>{{ __('hours.title') }}</strong>

            <button class="btn small" type="button" data-mb-close aria-label="{{ __('admin.actions.close') }}">
                ✕
            </button>
        </div>

        <form method="POST"
              action="{{ route('admin.restaurants.hours.update', $restaurant) }}"
              id="mbHoursForm">

            @csrf

            <div class="wh-modal__list">
                @foreach($days as $day => $key)
                    @php
                        $row = $hours[$day] ?? null;
                        $isClosed = !$row || $row->is_closed;
                    @endphp

                    <div class="wh-modal__row mb-row">
                        <div class="wh-modal__day">
                            {{ __('hours.days.' . $key) }}
                        </div>

                        <div class="wh-modal__toggle">
                            <label class="wh-switcher">
                                <input type="checkbox"
                                       name="hours[{{ $day }}][is_closed]"
                                       data-hours-closed
                                    {{ $isClosed ? 'checked' : '' }}>
                                <span class="wh-switcher__ui"></span>
                            </label>
                        </div>

                        <div class="wh-modal__time">
                            <input type="time"
                                   step="1800"
                                   name="hours[{{ $day }}][open_time]"
                                   value="{{ $row?->open_time?->format('H:i') }}"
                                   data-hours-open
                                {{ $isClosed ? 'disabled' : '' }}>

                            <span class="wh-modal__dash">—</span>

                            <input type="time"
                                   step="1800"
                                   name="hours[{{ $day }}][close_time]"
                                   value="{{ $row?->close_time?->format('H:i') }}"
                                   data-hours-close
                                {{ $isClosed ? 'disabled' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="wh-modal__actions">
                <button class="btn" type="button" data-mb-close>
                    {{ __('hours.cancel') }}
                </button>

                <button class="btn ok" type="submit">
                    {{ __('hours.save') }}
                </button>
            </div>

        </form>

    </div>
</div>
