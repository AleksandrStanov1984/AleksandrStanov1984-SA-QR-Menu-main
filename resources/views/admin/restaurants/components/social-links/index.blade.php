{{-- resources/views/admin/restaurants/components/social-links/index.blade.php --}}

@php
    use App\Support\Permissions;

    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);

    $links = $linksArr ?? collect();

    if (!$isSuper) {
        $links = $links->filter(fn($l) => empty($l->deleted_at));
    }

    $limit = (int) $restaurant->feature('social_limit', 1);

    $aliveCount = $links->filter(fn($l) => empty($l->deleted_at))->count();
    $activeCount = $links->filter(fn($l) => empty($l->deleted_at) && (bool)$l->is_active)->count();

    $canToggle = true;
    $canEdit   = true;
    $canDelete = true;
    $canIcon   = true;
    $canAdd = true;
@endphp

<div class="card" style="margin-top:16px;">
    <h2 style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
        <span>{{ __('admin.socials.title') }}</span>

        <span style="display:flex; gap:10px; align-items:center;">
            @if($canAdd)
                <button type="button" class="btn ok" data-sl-add>
                    {{ __('admin.socials.add') }}
                </button>
            @endif
        </span>
    </h2>

    <div class="mut" style="font-size:13px; margin-top:6px;">
        {{ __('admin.socials.hint') }}
    </div>

    <div class="mut" style="font-size:12px; margin-top:4px;">
        {{ __('admin.socials.limit_info', ['limit' => $limit]) }}
    </div>

    <div style="margin-top:12px;">
        <details class="sl-acc sl-acc-master" open data-sl-master>
            <summary class="sl-acc-summary">
                <div class="sl-acc-head">
                    <div style="font-weight:700;">
                        {{ __('admin.socials.title') }}
                    </div>
                    <div class="sl-acc-caret" aria-hidden="true"></div>
                </div>
            </summary>

            <div class="sl-acc-body">
                @include('admin.restaurants.components.social-links._list', [
                    'restaurant' => $restaurant,
                    'links' => $links,
                    'canToggle' => $canToggle,
                    'canEdit' => $canEdit,
                    'canDelete' => $canDelete,
                    'canIcon' => $canIcon,
                    'isSuper' => $isSuper,
                    'limit' => $limit,
                    'activeCount' => $activeCount,
                ])
            </div>
        </details>
    </div>
</div>

@include('admin.restaurants.components.social-links._modal', [
    'restaurant' => $restaurant,
    'canIcon' => $canIcon,
])

@include('admin.restaurants.components.social-links._styles')
@include('admin.restaurants.components.social-links._scripts', [
    'restaurant' => $restaurant,
])
