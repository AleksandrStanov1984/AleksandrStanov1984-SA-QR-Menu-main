@php
  use App\Support\Permissions;

  $user = auth()->user();
  $isSuper = (bool)($user?->is_super_admin);

  $links = $socialLinks ?? collect();

  // обычный пользователь не видит удалённые
  if (!$isSuper) {
    $links = $links->filter(fn($l) => empty($l->deleted_at));
  }

  $aliveCount = $links->filter(fn($l) => empty($l->deleted_at))->count();

  $canToggle = Permissions::can($user, 'socials.toggle.active');
  $canEdit   = Permissions::can($user, 'socials.edit');
  $canDelete = Permissions::can($user, 'socials.delete');
  $canIcon   = Permissions::can($user, 'socials.icon.upload');

  // добавление: первые 2 без прав, 3/4/5 по правам
  $canAdd = false;
  if ($aliveCount < 5) {
    if ($aliveCount < 2) $canAdd = true;
    elseif ($aliveCount === 2) $canAdd = Permissions::can($user, 'socials.add.3');
    elseif ($aliveCount === 3) $canAdd = Permissions::can($user, 'socials.add.4');
    elseif ($aliveCount === 4) $canAdd = Permissions::can($user, 'socials.add.5');
  }
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

  <div style="margin-top:12px;">
    <details class="sl-acc sl-acc-master" open data-sl-master>
      <summary class="sl-acc-summary">
        <div class="sl-acc-head">
          <div style="font-weight:700;">
            {{ __('admin.socials.title') }} ({{ $aliveCount }}/5)
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
