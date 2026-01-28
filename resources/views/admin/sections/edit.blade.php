@extends('admin.layout')

@section('title', __('admin.restaurant.edit_title'))
@section('subtitle', $restaurant->name)

@section('content')
@php
    $u = auth()->user();

    $canLanguages = $u?->is_super_admin || $u?->hasPerm('languages_manage') || $u?->hasPerm('import_manage');
    $canSections  = $u?->is_super_admin || $u?->hasPerm('sections_manage');
@endphp

    <div class="row">
        <div class="card" style="flex: 1; min-width: 320px;">
            <h2>{{ __('admin.restaurant.block_title') }}</h2>

            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom: 10px;">
                <span class="pill {{ $restaurant->is_active ? 'green' : 'red' }}">
                    {{ $restaurant->is_active ? __('admin.common.active') : __('admin.common.inactive') }}
                </span>
                <span class="pill">{{ __('admin.common.id') }}: {{ $restaurant->id }}</span>
                <span class="pill">{{ __('admin.common.slug') }}: {{ $restaurant->slug }}</span>
            </div>

            <form method="POST" action="{{ route('admin.restaurants.update', $restaurant) }}">
                @csrf
                @method('PUT')

                <div class="grid">
                    <div class="col6">
                        <label>{{ __('admin.restaurant.name') }}</label>
                        <input name="name" value="{{ old('name', $restaurant->name) }}" required>
                    </div>

                    <div class="col6">
                        <label>{{ __('admin.restaurant.template') }}</label>
                        <select name="template_key" required>
                            @foreach(['classic','fastfood','bar','services'] as $tpl)
                                <option value="{{ $tpl }}" @selected(old('template_key', $restaurant->template_key)===$tpl)>
                                    {{ __('admin.templates.'.$tpl) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-top: 12px; display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="btn" type="submit">{{ __('admin.common.save') }}</button>
                </div>
            </form>

            @if($u?->is_super_admin)
                <form method="POST" action="{{ route('admin.restaurants.toggle', $restaurant) }}" style="margin-top:10px;">
                    @csrf
                    <button class="btn {{ $restaurant->is_active ? 'danger' : 'ok' }}" type="submit">
                        {{ $restaurant->is_active ? __('admin.common.deactivate') : __('admin.common.activate') }}
                    </button>
                </form>
            @endif
        </div>

        {{-- ===========================
           Languages (by permission)
        ============================ --}}
        @if($canLanguages)
        <div class="card" style="flex: 1; min-width: 320px;">
            <h2>{{ __('admin.languages.block_title') }}</h2>

            <div class="mut" style="font-size:12px;">
                {{ __('admin.languages.default') }}:
                <b>{{ $restaurant->default_locale ?: 'de' }}</b>
                ·
                {{ __('admin.languages.enabled') }}:
                <b>{{ implode(', ', $restaurant->enabled_locales ?: ['de']) }}</b>
            </div>

            <hr style="border:none; border-top: 1px solid var(--line); margin: 12px 0;">

            <form method="POST" action="{{ route('admin.restaurants.languages.import', $restaurant) }}" enctype="multipart/form-data">
                @csrf

                <div class="grid">
                    <div class="col4">
                        <label>{{ __('admin.languages.locale_label') }}</label>
                        <input name="locale" value="{{ old('locale') }}" required>
                    </div>

                    <div class="col4">
                        <label>{{ __('admin.languages.json_label') }}</label>
                        <input type="file" name="file" accept="application/json,text/plain" required>
                    </div>

                    <div class="col4" style="display:flex; align-items:flex-end; gap:10px;">
                        <label style="margin:0; display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" name="is_default" value="1" style="width:auto;">
                            {{ __('admin.languages.set_default') }}
                        </label>
                    </div>
                </div>

                <div style="margin-top: 12px; display:flex; gap:10px;">
                    <button class="btn" type="submit">{{ __('admin.languages.add_language') }}</button>
                </div>

                <div class="mut" style="margin-top:8px; font-size:12px;">
                    {!! __('admin.languages.default_hint', ['default' => '<b>DE</b>']) !!}
                </div>
            </form>

            <hr style="border:none; border-top: 1px solid var(--line); margin: 12px 0;">

            <form method="POST" action="{{ route('admin.restaurants.languages.default', $restaurant) }}">
                @csrf
                <label>{{ __('admin.languages.change_default') }}</label>

                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <select name="default_locale" required>
                        @foreach(($restaurant->enabled_locales ?: ['de']) as $loc)
                            <option value="{{ $loc }}" @selected(($restaurant->default_locale ?: 'de')===$loc)>
                                {{ strtoupper($loc) }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn secondary" type="submit">{{ __('admin.languages.set_default_btn') }}</button>
                </div>
            </form>
        </div>
        @else
        <div class="card" style="flex: 1; min-width: 320px;">
            <h2>{{ __('admin.languages.block_title') }}</h2>
            <div class="mut" style="font-size:13px;">
                {{ __('admin.languages.no_access') }}
            </div>
        </div>
        @endif
    </div>

    <div class="card" style="margin-top:16px;">
        <h2>{{ __('admin.uploads.block_title') }}</h2>
        <div class="mut" style="font-size:13px;">
            {{ __('admin.uploads.path_hint') }}
            <div style="margin-top:8px;"><code>storage/app/public/restaurants/{{ $restaurant->id }}/</code></div>
            <div style="margin-top:6px;">
                {{ __('admin.uploads.folders_hint') }}
                <code>items</code>, <code>banners</code>, <code>backgrounds</code>, <code>socials</code>, <code>imports</code>, <code>misc</code>.
            </div>
        </div>
    </div>

    {{-- ===========================
       Sections (by permission)
    ============================ --}}
    @if($canSections)
    <div class="card" style="margin-top:16px;">
        <h2>{{ __('admin.sections.block_title') }}</h2>
        <div class="mut" style="font-size:13px;">
            {{ __('admin.sections.block_hint') }}
        </div>
        <div style="margin-top:12px;">
            <a class="btn" href="{{ route('admin.restaurants.sections.index', $restaurant) }}">
                {{ __('admin.sections.open_manager') }}
            </a>
        </div>
    </div>
    @endif
@endsection
