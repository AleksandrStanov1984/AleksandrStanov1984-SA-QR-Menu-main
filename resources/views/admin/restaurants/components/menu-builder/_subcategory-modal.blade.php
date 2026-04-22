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
                    {{ __('admin.menu_builder.auto_translate_hint') ?? 'Will be translated automatically later.' }}
                </div>

            </div>
        </div>

      <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn ok" type="submit">{{ __('admin.actions.create') ?? 'Create' }}</button>
      </div>
    </form>
  </div>
</div>
