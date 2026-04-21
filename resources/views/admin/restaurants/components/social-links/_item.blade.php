{{-- resources/views/admin/restaurants/components/social-links/_item.blade.php --}}

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
                @else
                    <span class="status">
                        <span class="status-dot {{ $inactive ? 'off' : 'on' }}"></span>
                    </span>
                @endif
            </div>

            <div class="sl-acc-caret" aria-hidden="true"></div>
        </div>
    </summary>

    <div class="sl-acc-body">
        <div style="display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap;">

            {{-- LEFT: ICON + TEXT --}}
            <div style="display:flex; gap:12px; align-items:center; min-width:260px; flex:1 1 auto;">

                <div class="sl-icon-box" style="position:relative;">

                    <img
                        src="{{ $iconUrl }}"
                        alt="icon"
                        data-sl-icon
                        data-fallback="{{ $img->socialIcon(null, $link->title) }}"
                        style="width:100%; height:100%; object-fit:contain; display:block;">

                    {{-- DELETE ICON --}}
                    @if($link->icon_path)
                        <button type="button"
                                class="sl-icon-delete"
                                data-sl-icon-delete
                                data-id="{{ $link->id }}"
                                data-url="{{ route('admin.restaurants.social_links.remove_icon', [$restaurant, $link]) }}"
                                data-delete-text="{{ __('social.socials.confirm_delete_icon') }}">
                            ✕
                        </button>
                    @endif

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

                <form method="POST"
                      action="{{ route('admin.restaurants.social_links.toggle_active', [$restaurant, $link]) }}"
                      style="margin:0;">

                    @csrf
                    @method('PATCH')

                    <label class="mb-switch" onclick="event.stopPropagation();">
                        <input type="checkbox"
                               onchange="this.form.submit()"
                            @checked(!$inactive)>

                        <span class="mb-switch__ui"></span>
                    </label>

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
                            data-delete-text="{{ __('social.socials.confirm_delete_socials') }}"
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
