{{-- resources/views/admin/restaurants/components/menu-builder/_category-modal.blade.php --}}

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

            <div class="col12">
                <label>
                    {{ __('admin.sections.categories.title') }}
                    ({{ strtoupper($locale) }})
                </label>

                <input name="title[{{ $locale }}]"
                       maxlength="50"
                       required>
            </div>

        </div>

      <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn ok" type="submit">{{ __('admin.sections.categories.change') }}</button>
      </div>
    </form>
  </div>
</div>
