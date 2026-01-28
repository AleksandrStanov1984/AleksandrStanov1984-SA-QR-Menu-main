@php
  $items = $section->items ?? collect();

  $canItems = auth()->user()->can('items_manage') || (auth()->user()->is_super_admin ?? false);

  $t = function (string $key, string $fallback) {
    return trans()->has($key) ? __($key) : $fallback;
  };

  $title = function($it) use ($defaultLocale) {
    $trs = $it->translations ?? collect();

    $tr = $trs->firstWhere('locale', $defaultLocale)
        ?? $trs->first(); // fallback: любой доступный перевод

    $val = $tr?->title ?? null;
    $val = is_string($val) ? trim($val) : '';

    return $val !== '' ? $val : ('Item #'.$it->id);
  };
@endphp

<div class="mb-items"
     data-sortable-items
     data-reorder-url="{{ route('admin.restaurants.items.reorder', [$restaurant, $section]) }}">
  @foreach($items as $it)
    @php
      $m = is_array($it->meta ?? null) ? $it->meta : (json_decode($it->meta ?? '[]', true) ?: []);
      $inactive = !$it->is_active;
      $isNew = !empty($m['is_new']);
      $isDay = !empty($m['dish_of_day']);
      $spicy = (int)($m['spicy'] ?? 0);
    @endphp

    <div class="mb-item {{ $inactive ? 'mb-inactive' : '' }}" data-item-id="{{ $it->id }}">
      <div class="mb-item-head">
        <div class="mb-left">
          <span class="mb-handle" title="Drag">≡</span>

          {{-- Toggle active --}}
          <form method="POST" action="{{ route('admin.restaurants.items.toggle', [$restaurant, $it]) }}">
            @csrf
            <label style="margin:0; display:flex; align-items:center; gap:8px;">
              <input
                type="checkbox"
                @checked($it->is_active)
                @disabled(!$canItems)
                aria-label="Toggle item active"
                onchange="if(this.disabled) return; this.disabled=true; this.form.submit();"
              >
              <span class="mb-item-title">{{ $title($it) }}</span>
            </label>
          </form>

          @if($isNew)<span class="pill green">NEW</span>@endif
          @if($isDay)<span class="pill">★ Day</span>@endif
          @if($inactive)<span class="pill red">{{ $t('admin.common.disabled', 'disabled') }}</span>@endif
        </div>

        <div class="mb-right">
          <div class="mb-mini">spicy: {{ $spicy }}</div>

          {{-- Delete --}}
          @if($canItems)
            <form method="POST"
                  action="{{ route('admin.restaurants.items.destroy', [$restaurant, $it]) }}"
                  onsubmit="return confirm('{{ $t('admin.confirm.delete_item', 'Delete item?') }}')">
              @csrf
              @method('DELETE')
              <button class="btn small danger" type="submit">
                {{ $t('admin.actions.delete', 'Delete') }}
              </button>
            </form>
          @endif
        </div>
      </div>
    </div>
  @endforeach
</div>
