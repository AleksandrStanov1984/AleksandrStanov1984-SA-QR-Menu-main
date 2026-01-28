<div class="modal" id="mbModalItem" aria-hidden="true">
  <div class="modal__backdrop" data-mb-close></div>
  <div class="modal__panel">
    <div class="mb-row">
      <strong>{{ __('admin.menu_builder.add_item') }}</strong>
      <button class="btn small" type="button" data-mb-close>✕</button>
    </div>

    <form method="POST" enctype="multipart/form-data"
          action="{{ route('admin.restaurants.items.store', [$restaurant, 0, 0]) }}"
          id="mbItemForm">
      @csrf

      <input type="hidden" id="mbItemSectionId" name="_section_id" value="">

      <div class="grid" style="margin-top:10px;">
        <div class="col6">
          <label>{{ __('admin.menu_builder.flags') }}</label>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <label class="perm-item"><input type="checkbox" name="is_active" value="1" checked> active</label>
            <label class="perm-item"><input type="checkbox" name="show_image" value="1" checked> show image + modal</label>
            <label class="perm-item"><input type="checkbox" name="is_new" value="1"> NEW</label>
            <label class="perm-item"><input type="checkbox" name="dish_of_day" value="1"> Dish of day</label>
          </div>
        </div>

        <div class="col6">
          <label>{{ __('admin.menu_builder.spicy') }}</label>
          <select name="spicy">
            @for($i=0;$i<=5;$i++)
              <option value="{{ $i }}">{{ $i }}</option>
            @endfor
          </select>

          <label style="margin-top:10px;">{{ __('admin.menu_builder.image') }}</label>
          <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
        </div>
      </div>

      <hr style="border:0;border-top:1px solid var(--line); margin:12px 0;">

      <div class="grid">
        <div class="col12">
          <div class="mb-muted">{{ __('admin.menu_builder.styles_hint') }}</div>
        </div>

        @foreach(['title'=>'Title','desc'=>'Description','details'=>'Details'] as $k => $lbl)
          <div class="col4">
            <label>{{ $lbl }} font</label>
            <select name="style[{{ $k }}][font]" data-style-font="{{ $k }}">
              <option value="">—</option>
              <option value="inter">Inter</option>
              <option value="poppins">Poppins</option>
              <option value="roboto">Roboto</option>
              <option value="playfair">Playfair</option>
            </select>
          </div>
          <div class="col4">
            <label>{{ $lbl }} color</label>
            <input name="style[{{ $k }}][color]" placeholder="#FFFFFF" data-style-color="{{ $k }}">
          </div>
          <div class="col4">
            <label>{{ $lbl }} size</label>
            <input type="number" name="style[{{ $k }}][size]" min="8" max="72" step="1" value="14" data-style-size="{{ $k }}">
          </div>
        @endforeach
      </div>

      <div class="grid" style="margin-top:8px;">
        @foreach($locales as $loc)
          <div class="col12">
            <label>Title ({{ strtoupper($loc) }})</label>
            <input name="translations[{{ $loc }}][title]" maxlength="50" required data-text-field="title">

            <label>Description ({{ strtoupper($loc) }})</label>
            <input name="translations[{{ $loc }}][description]" maxlength="250" data-text-field="desc">

            <label>Details ({{ strtoupper($loc) }})</label>
            <textarea name="translations[{{ $loc }}][details]" maxlength="500"
                      style="width:100%; min-height:90px; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text);"
                      data-text-field="details"></textarea>
          </div>
        @endforeach
      </div>

      <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn ok" type="submit">{{ __('admin.actions.create') ?? 'Create' }}</button>
      </div>
    </form>
  </div>
</div>
