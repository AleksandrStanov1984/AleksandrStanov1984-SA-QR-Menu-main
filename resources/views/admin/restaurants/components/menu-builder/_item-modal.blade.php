@php
  use App\Support\Permissions;
@endphp

<div class="modal" id="mbModalItem" aria-hidden="true">
  <div class="modal__backdrop" data-mb-close></div>
  <div class="modal__panel">
    <div class="mb-row">
      <strong>{{ __('admin.menu_builder.add_item') }}</strong>
      <button class="btn small" type="button" data-mb-close aria-label="{{ __('admin.actions.close') }}">✕</button>
    </div>

    @php
      $user = auth()->user();
      $canUploadImage = Permissions::can($user, 'items.image.upload');
      $canDetails = $canUploadImage; // правило: image upload => details
    @endphp

    <form method="POST" enctype="multipart/form-data"
          action="#"
          id="mbItemForm">
      @csrf

      <input type="hidden" id="mbItemSectionId" name="_section_id" value="">

      <div class="grid" style="margin-top:10px;">
        <div class="col6">
          <label>{{ __('admin.menu_builder.price') ?? 'Price' }}</label>
          <input
            name="price"
            maxlength="20"
            inputmode="decimal"
            pattern="^\d+(?:[.,]\d{1,2})?$"
            placeholder="0.00"
            aria-label="{{ __('admin.menu_builder.price') ?? 'Price' }}"
            required
          >
          <div class="mb-muted" style="margin-top:6px;">
            {{ __('admin.menu_builder.price_hint') ?? 'Only numbers and one dot/comma.' }}
          </div>
        </div>

        <div class="col6"></div>
      </div>

      <hr style="border:0;border-top:1px solid var(--line); margin:12px 0;">

      <div class="grid">
        <div class="col12">
          <div class="mb-muted">{{ __('admin.menu_builder.styles_hint') }}</div>
        </div>

        @foreach(['title','desc','details'] as $k)
          <div class="col4">
            <label>{{ __('admin.menu_builder.style_font', ['field' => __('admin.menu_builder.field_'.$k)]) }}</label>
            <select name="style[{{ $k }}][font]" data-style-font="{{ $k }}">
              <option value="">{{ __('admin.common.dash') }}</option>
              <option value="inter">Inter</option>
              <option value="poppins">Poppins</option>
              <option value="roboto">Roboto</option>
              <option value="playfair">Playfair</option>
            </select>
          </div>

          <div class="col4">
            <label>{{ __('admin.menu_builder.style_color', ['field' => __('admin.menu_builder.field_'.$k)]) }}</label>
            <input name="style[{{ $k }}][color]" placeholder="#FFFFFF" data-style-color="{{ $k }}">
          </div>

          <div class="col4">
            <label>{{ __('admin.menu_builder.style_size', ['field' => __('admin.menu_builder.field_'.$k)]) }}</label>
            <input type="number" name="style[{{ $k }}][size]" min="8" max="72" step="1" value="14" data-style-size="{{ $k }}">
          </div>
        @endforeach
      </div>

      <div class="grid" style="margin-top:8px;">
        @foreach($locales as $loc)
          <div class="col12">
            <label>{{ __('admin.menu_builder.title_locale', ['locale' => strtoupper($loc)]) }}</label>
            <input
              name="translations[{{ $loc }}][title]"
              maxlength="50"
              required
              data-text-field="title"
              data-text-locale="{{ $loc }}"
              autocomplete="off"
            >

            <label>{{ __('admin.menu_builder.description_locale', ['locale' => strtoupper($loc)]) }}</label>
            <input
              name="translations[{{ $loc }}][description]"
              maxlength="100"
              data-text-field="desc"
              data-text-locale="{{ $loc }}"
              autocomplete="off"
            >

            @if($canDetails)
              <label>{{ __('admin.menu_builder.details_locale', ['locale' => strtoupper($loc)]) }}</label>
              <textarea
                name="translations[{{ $loc }}][details]"
                maxlength="255"
                style="width:100%; min-height:90px; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text);"
                data-text-field="details"
                data-text-locale="{{ $loc }}"
              ></textarea>
            @endif
          </div>
        @endforeach
      </div>

      @if($canUploadImage)
        <hr style="border:0;border-top:1px solid var(--line); margin:12px 0;">

        <label style="margin-top:10px;">{{ __('admin.menu_builder.image') }}</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
        <div class="mb-muted" style="margin-top:6px;">
          {{ __('admin.menu_builder.image_hint') }}
        </div>
      @endif

      <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn ok" type="submit">{{ __('admin.actions.create') }}</button>
      </div>
    </form>
  </div>
</div>
