@inject('img', 'App\Services\ImageService')

@php
    $isSuper = auth()->user()?->is_super_admin ?? false;

    $deleted = !empty($link->deleted_at);
    $inactive = !$link->is_active;

    if (!$isSuper && $deleted) {
        return;
    }

    $iconUrl = $img->socialIcon($link->icon_path, $link->title);

    $payload = [
        'id' => $link->id,
        'title' => $link->title,
        'url' => $link->url,
        'icon_url' => $iconUrl,
        'icon_path' => $link->icon_path,
        'is_active' => (bool)$link->is_active,
        'deleted' => $deleted ? 1 : 0,
    ];
@endphp

<details class="sl-acc sl-acc-item {{ $deleted ? 'is-deleted' : '' }} {{ $inactive ? 'is-inactive' : '' }}"
         open
         data-sl-item
         data-id="{{ $link->id }}">

    <summary class="sl-acc-summary">
        <div class="sl-acc-head">

            {{-- handle --}}
            <span class="mb-handle"
                  title="Drag"
                  data-no-accordion
                  onclick="event.stopPropagation();"
                  style="display:inline-flex; align-items:center; justify-content:center;">
    ≡
</span>

            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                <span style="font-weight:700; white-space:normal; word-break:break-word; line-height:1.25;">
                    {{ $link->title }}
                </span>

                @if($deleted)
                    <span class="pill red">{{ __('admin.socials.deleted') }}</span>
                @elseif($inactive)
                    <span class="pill red">{{ __('admin.socials.inactive') }}</span>
                @else
                    <span class="pill green">{{ __('admin.socials.active') }}</span>
                @endif
            </div>

            <div class="sl-acc-caret" aria-hidden="true"></div>
        </div>
    </summary>

    <div class="sl-acc-body">
        <div style="display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap;">

            {{-- LEFT: ICON + TEXT --}}
            <div style="display:flex; gap:12px; align-items:center; min-width:260px; flex:1 1 auto;">

                <div class="sl-icon-box">
                    <img
                        src="{{ $iconUrl }}"
                        alt="icon"
                        style="width:100%; height:100%; object-fit:contain; display:block;">
                </div>

                <div style="min-width:0; flex:1 1 auto;">
                    <div style="font-weight:700; white-space:normal; word-break:break-word; line-height:1.25;">
                        {{ $link->title }}
                    </div>

                    <a href="{{ $link->url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       style="display:block; margin-top:4px; font-size:13px; word-break:break-all; opacity:.85;">
                        {{ $link->url }}
                    </a>
                </div>
            </div>

            {{-- RIGHT: ACTIONS --}}
            <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end; flex:0 0 auto; margin-left:auto;">

                <form method="POST" action="{{ route('admin.restaurants.social_links.toggle_active', [$restaurant, $link]) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn small secondary" type="submit">
                        {{ $inactive ? __('admin.common.enable') ?? 'Enable' : __('admin.common.disable') ?? 'Disable' }}
                    </button>
                </form>

                @if(!$deleted)
                    <button type="button"
                            class="btn small secondary"
                            data-sl-edit
                            data-sl='@json($payload)'
                        {{ $inactive ? 'disabled aria-disabled=true' : '' }}>
                        {{ __('admin.socials.edit') }}
                    </button>

                    <button type="button"
                            class="btn small danger"
                            data-sl-delete
                            data-delete-url="{{ route('admin.restaurants.social_links.destroy', [$restaurant, $link]) }}"
                            data-delete-text="{{ __('admin.socials.confirm_delete') }}"
                            data-delete-hint="{{ $link->title }}"
                        {{ $inactive ? 'disabled aria-disabled=true' : '' }}>
                        {{ __('admin.socials.delete') }}
                    </button>
                @endif

            </div>
        </div>
    </div>
</details>
<br>

<style>
    .sl-acc.is-inactive .btn[disabled] {
        opacity: .5;
        pointer-events: none;
        filter: grayscale(1);
    }

    .drag-handle:active {
        cursor: grabbing;
    }
</style>
