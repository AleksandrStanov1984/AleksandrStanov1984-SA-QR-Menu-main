@php
    $indent = ($level ?? 0) * 18;

    $u = auth()->user();
    $canManage = $u?->is_super_admin || $u?->hasPerm('sections_manage');

    // safe fallbacks
    $title = $node->title ?? __('admin.sections.fallback_title', ['id' => $node->id]);
    $key = $node->key ?? __('admin.sections.no_key');
    $isActive = !isset($node->is_active) ? true : (bool) $node->is_active;
@endphp

<ul style="list-style:none; margin:0; padding:0;">
@foreach($nodes as $node)
  @php
      $indent = ($level ?? 0) * 18;

      $title = $node->title ?? __('admin.sections.fallback_title', ['id' => $node->id]);
      $key = $node->key ?? __('admin.sections.no_key');
      $isActive = !isset($node->is_active) ? true : (bool) $node->is_active;
  @endphp

  <li style="margin: 0 0 10px 0; margin-left: {{ $indent }}px;">
    <div class="card" style="padding:12px; border-radius:14px; @if(!$isActive) border-color: rgba(255,90,95,.35); @endif">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
        <div style="display:flex; flex-direction:column; gap:4px;">
          <div style="font-weight:700;">
            {{ $title }}
            <span class="mut" style="font-size:12px; font-weight:400;">({{ $key }})</span>
          </div>

          @if(!empty($node->description))
            <div class="mut" style="font-size:12px;">{{ $node->description }}</div>
          @endif
        </div>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
          <span class="pill {{ $isActive ? 'green' : 'red' }}">
            {{ $isActive ? __('admin.status.active') : __('admin.status.inactive') }}
          </span>

          @if($canManage)
            <a class="btn small"
               href="{{ route('admin.restaurants.sections.edit', [$restaurant, $node]) }}">
              {{ __('admin.actions.edit') }}
            </a>

            <form method="POST"
                  action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $node]) }}"
                  style="margin:0;">
              @csrf
              <button class="btn small {{ $isActive ? 'danger' : 'ok' }}" type="submit">
                {{ $isActive ? __('admin.actions.deactivate') : __('admin.actions.activate') }}
              </button>
            </form>
          @endif
        </div>
      </div>
    </div>

    @if($node->children && $node->children->count())
      <div style="margin-top:10px;">
        @include('admin.sections._tree', [
            'nodes' => $node->children,
            'restaurant' => $restaurant,
            'level' => ($level ?? 0) + 1
        ])
      </div>
    @endif
  </li>
@endforeach
</ul>
