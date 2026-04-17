{{-- resources/views/public/templates/united/blocks/modal/hours-modal.blade.php --}}

<div id="hoursModal" class="modal">

    <div class="modal-box">

        <div class="hours-header">

            <div style="width:36px;"></div>

            <h3>
                {{ __('menu.opening_hours') }}
            </h3>

            <button type="button"
                    class="hours-close"
                    data-close-modal="hoursModal">
                ✕
            </button>

        </div>

        <div class="hours-list">

            @foreach($vm->hours as $day)

                <div class="hours-row @if($day['today']) hours-today @endif">

                    <div class="hours-day">

                        {{ $day['label'] }}

                        @if($day['today'])
                            <span class="hours-today-label">
                                ({{ __('menu.today') }})
                            </span>
                        @endif

                    </div>

                    <div class="hours-time">

                        @if($day['closed'])

                            {{ __('menu.closed') }}

                        @else

                            {{ $day['open'] }} – {{ $day['close'] }}

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>
