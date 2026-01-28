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

      <div class="grid" style="margin-top:12px;">
        @foreach($locales as $loc)
          <div class="col6">
            <label>{{ __('admin.sections.categories.title') }} ({{ strtoupper($loc) }})</label>
            <input name="title[{{ $loc }}]" maxlength="50" required>
          </div>
        @endforeach
      </div>

      <div class="grid" style="margin-top:12px;">
        <div class="col6">
          <label>{{ __('admin.sections.categories.font') }}</label>
          <select name="title_font">
            <option value="">—</option>
            <option value="inter">Inter</option>
            <option value="poppins">Poppins</option>
            <option value="roboto">Roboto</option>
            <option value="playfair">Playfair</option>
          </select>
        </div>
        <div class="col6">
          <label>{{ __('admin.sections.categories.color') }}</label>
          <input name="title_color" placeholder="#FFFFFF">
        </div>
      </div>

      <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn ok" type="submit">{{ __('admin.actions.create') ?? 'Create' }}</button>
      </div>
    </form>
  </div>
</div>
