
@php
    $categoriesCount = collect($menuTree ?? [])->count();
@endphp

<div class="modal" id="mbModalCategory" aria-hidden="true">
    <div class="modal__backdrop" data-mb-close></div>

    <div class="modal__panel">

        <div class="mb-row">
            <strong>{{ __('admin.menu_builder.add_category') }}</strong>
            <button class="btn small" type="button" data-mb-close>✕</button>
        </div>

        <form method="POST" action="{{ route('admin.restaurants.categories.store', $restaurant) }}">
            @csrf

            @php
                $locale = $restaurant->default_locale ?? 'de';
            @endphp

            <div class="grid" style="margin-top:12px;">

                {{-- TITLE --}}
                <div class="col12">
                    <label>
                        {{ __('admin.sections.categories.title') }}
                        ({{ strtoupper($locale) }})
                    </label>

                    <input
                        name="title[{{ $locale }}]"
                        maxlength="50"
                        required
                    >
                </div>

                @if($categoriesCount > 0)

                    {{-- POSITION --}}
                    <div class="col12" style="margin-top:12px;">
                        <label>{{ __('admin.position.label') }}</label>

                        <div class="ui-select ui-select--button" data-name="position_mode" id="mbCategoryPosition">

                            <button type="button" class="ui-select-btn">
                                {{ __('admin.position.end') }}
                            </button>

                            <div class="ui-select-menu">
                                <div class="ui-select-option active" data-value="end">
                                    {{ __('admin.position.end') }}
                                </div>

                                <div class="ui-select-option" data-value="start">
                                    {{ __('admin.position.start') }}
                                </div>

                                @if($categoriesCount > 1)
                                    <div class="ui-select-option" data-value="before">
                                        {{ __('admin.position.before') }}
                                    </div>

                                    <div class="ui-select-option" data-value="after">
                                        {{ __('admin.position.after') }}
                                    </div>
                                @endif
                            </div>

                            <input type="hidden" name="position_mode" value="end">
                        </div>
                    </div>

                    @if($categoriesCount > 1)
                        {{-- TARGET --}}
                        <div class="col12" id="mbCategoryTargetWrap" style="display:none;">
                            <label>{{ __('admin.position.target_category') }}</label>

                            <div class="ui-select ui-select--button" data-name="target_id" id="mbCategoryTargetSelect">

                                <button type="button" class="ui-select-btn">
                                    —
                                </button>

                                <div class="ui-select-menu">
                                    @foreach($menuTree as $c)
                                        <div class="ui-select-option" data-value="{{ $c->id }}">
                                            {{ $tTitle($c, $defaultLocale) }}
                                        </div>
                                    @endforeach
                                </div>

                                <input type="hidden" name="target_id">
                            </div>

                            <div class="mb-muted mb-position-error" style="display:none; margin-top:6px;">
                                {{ __('admin.position.target_required') }}
                            </div>
                        </div>
                    @endif

                @endif

            </div>

            <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
                <button class="btn ok" type="submit">
                    {{ __('admin.sections.categories.change') }}
                </button>
            </div>

        </form>
    </div>
</div>

<style>
    /* =========================
       FIX: dropdown inside modal
       ONLY for this modal
    ========================= */

    #mbModalCategory .modal__panel {
        overflow: visible;
    }

    #mbModalCategory .ui-select {
        position: relative;
    }

    #mbModalCategory .ui-select-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 10000;
        max-height: 240px;
        overflow-y: auto;
        background: #0f1a2b;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
</style>
