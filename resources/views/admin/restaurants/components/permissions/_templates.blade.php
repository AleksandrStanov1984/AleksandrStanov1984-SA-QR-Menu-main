@php
    $mode = $mode ?? 'view';
@endphp

@foreach($grouped as $g => $items)
    <template id="tplPermGroup_{{ $g }}">

        <div class="perm-grid perm-grid--modal">

            @foreach($items as $permKey => $label)

                @if($mode === 'edit')

                    <label class="perm-item">
                        <span class="perm-label">{{ $label }}</span>

                        <input type="checkbox"
                               name="perm[{{ $permKey }}]"
                               value="1"
                            @checked(!empty($p[$permKey]))>
                    </label>

                @else

                    @if(!empty($p[$permKey]))
                        <div class="perm-item">
                            <span class="perm-label">{{ $label }}</span>

                            <span class="pill green">
                                {{ __('admin.permissions.enabled') ?? 'Включено' }}
                            </span>
                        </div>
                    @endif

                @endif

            @endforeach

        </div>

    </template>
@endforeach
