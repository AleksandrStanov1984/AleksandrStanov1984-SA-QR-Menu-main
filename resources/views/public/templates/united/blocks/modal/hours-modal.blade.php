<div id="hoursModal" class="modal">

    <div class="modal-box">

        <div class="hours-header">

            <h3>
                {{ __('menu.opening_hours') }}
            </h3>
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
