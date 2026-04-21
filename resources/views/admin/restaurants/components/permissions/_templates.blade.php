{{-- resources/views/admin/restaurants/components/permissions/_templates.blade.php --}}

@php
    $mode = $mode ?? 'view';
@endphp

@foreach($grouped as $g => $items)
    <template id="tplPermGroup_{{ $g }}">

        <div class="perm-grid perm-grid--modal" data-perm-group="{{ $g }}">

            @foreach($items as $permKey => $label)

                @if($mode === 'edit')

                    <div class="perm-item">

                        <span class="perm-label">{{ $label }}</span>

                        <label class="mb-switch">
                            <input type="checkbox"
                                   data-perm-key="{{ $permKey }}"
                                   value="1"
                                @checked(!empty($p[$permKey]))>

                            <span class="mb-switch__ui"></span>
                        </label>

                    </div>

                @else

                    @if(!empty($p[$permKey]))
                        <div class="perm-item">
                            <span class="perm-label">{{ $label }}</span>

                            <span class="status">
        <span class="status-dot on"></span>
    </span>
                        </div>
                    @endif

                @endif

            @endforeach

        </div>

    </template>
@endforeach
