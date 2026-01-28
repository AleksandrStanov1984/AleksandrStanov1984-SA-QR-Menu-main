<div style="margin-top:14px; display:flex; flex-direction:column; gap:10px;">
  @forelse($menuTree as $cat)
    @php($catInactive = !$cat->is_active)
    <div class="card {{ $catInactive ? 'mb-inactive' : '' }}" style="padding:12px;">
      <div class="mb-row">
        <div class="mb-left">
          <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $cat]) }}">
            @csrf
            <label style="margin:0; display:flex; align-items:center; gap:8px;">
              <input type="checkbox" @checked($cat->is_active) onchange="this.form.submit()">
              <span class="mb-item-title">
                {{ $tTitle($cat, $defaultLocale) ?: ('Category #'.$cat->id) }}
              </span>
            </label>
          </form>

          <span class="mb-mini">ID: {{ $cat->id }}</span>
          @if($catInactive)
            <span class="pill red">{{ __('admin.common.disabled') ?? 'disabled' }}</span>
          @endif
        </div>

        <div class="mb-right mb-actions">
          <button class="btn small secondary" type="button"
                  data-mb-open="mbModalSubcategory"
                  data-parent-id="{{ $cat->id }}">+ {{ __('admin.menu_builder.add_subcategory') }}</button>

          <button class="btn small" type="button"
                  data-mb-open="mbModalItem"
                  data-section-id="{{ $cat->id }}">+ {{ __('admin.menu_builder.add_item') }}</button>

          <form method="POST" action="{{ route('admin.restaurants.sections.destroy', [$restaurant, $cat]) }}"
                onsubmit="return confirm('Delete category and all nested data?')">
            @csrf @method('DELETE')
            <button class="btn small danger" type="submit">{{ __('admin.actions.delete') ?? 'Delete' }}</button>
          </form>
        </div>
      </div>

      {{-- Items under category --}}
      @include('admin.restaurants.components.menu-builder._items-list', [
        'restaurant' => $restaurant,
        'section' => $cat,
        'defaultLocale' => $defaultLocale
      ])

      {{-- Subcategories --}}
      <div class="mb-sub" style="margin-top:12px;">
        @foreach($cat->children as $sub)
          @php($subInactive = !$sub->is_active)
          <div class="card {{ $subInactive ? 'mb-inactive' : '' }}" style="padding:12px; margin-top:10px;">
            <div class="mb-row">
              <div class="mb-left">
                <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $sub]) }}">
                  @csrf
                  <label style="margin:0; display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" @checked($sub->is_active) onchange="this.form.submit()">
                    <span class="mb-item-title">
                      {{ $tTitle($sub, $defaultLocale) ?: ('Subcategory #'.$sub->id) }}
                    </span>
                  </label>
                </form>
                <span class="mb-mini">ID: {{ $sub->id }}</span>
                @if($subInactive)
                  <span class="pill red">{{ __('admin.common.disabled') ?? 'disabled' }}</span>
                @endif
              </div>

              <div class="mb-right mb-actions">
                <button class="btn small" type="button"
                        data-mb-open="mbModalItem"
                        data-section-id="{{ $sub->id }}">+ {{ __('admin.menu_builder.add_item') }}</button>

                <form method="POST" action="{{ route('admin.restaurants.sections.destroy', [$restaurant, $sub]) }}"
                      onsubmit="return confirm('Delete subcategory and items?')">
                  @csrf @method('DELETE')
                  <button class="btn small danger" type="submit">{{ __('admin.actions.delete') ?? 'Delete' }}</button>
                </form>
              </div>
            </div>

            @include('admin.restaurants.components.menu-builder._items-list', [
              'restaurant' => $restaurant,
              'section' => $sub,
              'defaultLocale' => $defaultLocale
            ])
          </div>
        @endforeach
      </div>
    </div>
  @empty
    <div class="mb-muted" style="margin-top:10px;">
      {{ __('admin.menu_builder.empty') }}
    </div>
  @endforelse
</div>
