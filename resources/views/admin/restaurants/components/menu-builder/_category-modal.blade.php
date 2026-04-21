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

      <div class="grid" style="margin-top:12px;">
        @foreach($locales as $loc)
          <div class="col6">
            <label>{{ __('admin.sections.categories.title') }} ({{ strtoupper($loc) }})</label>
            <input name="title[{{ $loc }}]" maxlength="50" required>
          </div>
        @endforeach
      </div>

      <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn ok" type="submit">{{ __('admin.sections.categories.change') }}</button>
      </div>
    </form>
  </div>
</div>
