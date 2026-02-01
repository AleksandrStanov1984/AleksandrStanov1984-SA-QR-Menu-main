@php
  $viewOnly = (bool)($viewOnly ?? false);
@endphp

<div class="perm-groups-bar" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:12px;">
  @foreach($grouped as $g => $items)
    <button type="button"
            class="btn secondary"
            data-open-perm-group="1"
            data-group-key="{{ $g }}"
            data-group-title="{{ __('admin.permissions.groups.'.$g) }}"
            {{ $viewOnly ? '' : '' }}>
      {{ __('admin.permissions.groups.'.$g) }}
      <span style="opacity:.65; font-weight:600;">({{ count($items) }})</span>
    </button>
  @endforeach
</div>
