@php
  $links = $links ?? collect();
@endphp

<div class="sl-list" data-sl-list>
  @forelse($links as $link)
    @include('admin.restaurants.components.social-links._item', [
      'restaurant' => $restaurant,
      'link' => $link,
      'canToggle' => $canToggle,
      'canEdit' => $canEdit,
      'canDelete' => $canDelete,
      'canIcon' => $canIcon,
      'isSuper' => $isSuper,
    ])
  @empty
    <div class="mut" style="font-size:13px; padding:10px 0;">
      —
    </div>
  @endforelse
</div>
