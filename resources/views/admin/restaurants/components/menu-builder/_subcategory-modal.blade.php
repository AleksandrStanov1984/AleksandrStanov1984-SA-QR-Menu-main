{{-- resources/views/admin/restaurants/components/menu-builder/_subcategory-modal.blade.php --}}

<div class="modal" id="mbModalSubcategory" aria-hidden="true">
    <div class="modal__backdrop" data-mb-close></div>

    <div class="modal__panel">

        <div class="mb-row">
            <strong>{{ __('admin.menu_builder.add_subcategory') }}</strong>
            <button class="btn small" type="button" data-mb-close>✕</button>
        </div>

        <form method="POST" action="{{ route('admin.restaurants.subcategories.store', $restaurant) }}">
            @csrf

            <input type="hidden" name="parent_id" id="mbSubParentId" value="">

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

                    <div class="mb-muted" style="margin-top:6px;">
                        {{ __('admin.menu_builder.auto_translate_hint') }}
                    </div>
                </div>

                {{-- POSITION --}}
                <div class="col12" id="mbSubPositionWrap" style="display:none; margin-top:12px;">
                    <label>{{ __('admin.position.label') }}</label>

                    <div class="ui-select ui-select--button"
                         data-name="position_mode"
                         id="mbSubPosition">

                        <button type="button" class="ui-select-btn">
                            —
                        </button>

                        <div class="ui-select-menu" id="mbSubPositionMenu">

                            <div class="ui-select-option active" data-value="">
                                —
                            </div>

                            <div class="ui-select-option" data-value="end">
                                {{ __('admin.position.end') }}
                            </div>

                            <div class="ui-select-option" data-value="start">
                                {{ __('admin.position.start') }}
                            </div>

                            {{-- before / after добавляются через JS --}}
                        </div>

                        <input type="hidden" name="position_mode" value="">
                    </div>
                </div>

                {{-- TARGET --}}
                <div class="col12" id="mbSubTargetWrap" style="display:none;">
                    <label>{{ __('admin.position.target_subcategory') }}</label>

                    <div class="ui-select ui-select--button"
                         id="mbSubTargetSelect"
                         data-name="target_id">

                        <button type="button" class="ui-select-btn">
                            —
                        </button>

                        <div class="ui-select-menu">
                            {{-- наполняется через JS --}}
                        </div>

                        <input type="hidden" name="target_id" value="">
                    </div>

                    <div class="mb-muted mb-position-error" style="display:none; margin-top:6px;">
                        {{ __('admin.position.target_required') }}
                    </div>
                </div>

            </div>

            <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
                <button class="btn ok" type="submit">
                    {{ __('admin.actions.create') }}
                </button>
            </div>

        </form>
    </div>
</div>

<style>
    /* =========================
       FIX: dropdown inside modal
    ========================= */

    #mbModalSubcategory .modal__panel {
        overflow: visible;
    }

    #mbModalSubcategory .ui-select {
        position: relative;
    }

    #mbModalSubcategory .ui-select-menu {
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
