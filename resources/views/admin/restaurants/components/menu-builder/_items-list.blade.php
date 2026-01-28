@php
  $items = $section->items ?? collect();
  $title = function($it) use ($defaultLocale){
    $tr = $it->translations->firstWhere('locale', $defaultLocale);
    return $tr->title ?? ('Item #'.$it->id);
  };
@endphp

<div class="mb-items"
     data-sortable-items
     data-reorder-url="{{ route('admin.restaurants.items.reorder', [$restaurant, $section]) }}">
  @foreach($items as $it)
    @php
      $m = $it->meta ?? [];
      $inactive = !$it->is_active;
      $isNew = !empty($m['is_new']);
      $isDay = !empty($m['dish_of_day']);
    @endphp

    <div class="mb-item {{ $inactive ? 'mb-inactive' : '' }}" data-item-id="{{ $it->id }}">
      <div class="mb-item-head">
        <div class="mb-left">
          <span class="mb-handle">≡</span>

          <form method="POST" action="{{ route('admin.restaurants.items.toggle', [$restaurant, $it]) }}">
            @csrf
            <label style="margin:0; display:flex; align-items:center; gap:8px;">
              <input type="checkbox" @checked($it->is_active) onchange="this.form.submit()">
              <span class="mb-item-title">{{ $title($it) }}</span>
            </label>
          </form>

          @if($isNew)<span class="pill green">NEW</span>@endif
          @if($isDay)<span class="pill">★ Day</span>@endif
          @if($inactive)<span class="pill red">{{ __('admin.common.disabled') ?? 'disabled' }}</span>@endif
        </div>

        <div class="mb-right">
          <div class="mb-mini">spicy: {{ (int)($m['spicy'] ?? 0) }}</div>

          <form method="POST" action="{{ route('admin.restaurants.items.destroy', [$restaurant, $it]) }}"
                onsubmit="return confirm('Delete item?')">
            @csrf @method('DELETE')
            <button class="btn small danger" type="submit">{{ __('admin.actions.delete') ?? 'Delete' }}</button>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>
