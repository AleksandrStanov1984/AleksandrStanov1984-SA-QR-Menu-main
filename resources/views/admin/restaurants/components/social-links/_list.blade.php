{{-- resources/views/admin/restaurants/components/social-links/_list.blade.php --}}
@php
    $links = $links ?? collect();

    $links = $links->sortBy(function ($l) {
      return $l->is_active ? 0 : 1;
    })->values();

    $limit = $limit ?? 1;
@endphp

<div class="sl-list" data-sl-list>
    @forelse($links as $index => $link)

        @php
            $forceInactive = $index >= $limit;
        @endphp

        @include('admin.restaurants.components.social-links._item', [
          'restaurant' => $restaurant,
          'link' => $link,
          'canToggle' => $canToggle,
          'canEdit' => $canEdit,
          'canDelete' => $canDelete,
          'canIcon' => $canIcon,
          'isSuper' => $isSuper,
          'forceInactive' => $forceInactive,
        ])

    @empty
        <div class="mut" style="font-size:13px; padding:10px 0;">
            —
        </div>
    @endforelse

</div>
