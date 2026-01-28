@extends('admin.layout')

@section('title', __('admin.sections.manager.title'))
@section('subtitle', __('admin.sections.manager.subtitle'))

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom: 12px;">
    <h1 style="margin:0; font-size:18px;">{{ __('admin.sections.manager.h1') }}</h1>

    <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn secondary">
        {{ __('admin.sections.manager.back_to_restaurant') }}
    </a>
</div>

@if(session('success'))
    <div class="flash">{{ session('success') }}</div>
@endif

<div class="row">
    {{-- Add category --}}
    <div class="card" style="flex:1; min-width: 320px;">
        <h2>{{ __('admin.sections.manager.add_category.h2') }}</h2>

        <form method="POST" action="{{ route('admin.restaurants.sections.store', $restaurant) }}">
            @csrf
            <input type="hidden" name="parent_id" value="">

            <div class="grid">
                <div class="col12">
                    <label>{{ __('admin.fields.title') }}</label>
                    <input name="title" value="{{ old('title') }}" required>
                </div>

                <div class="col12">
                    <label>{{ __('admin.fields.description') }}</label>
                    <textarea name="description" rows="2"
                              style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text);">{{ old('description') }}</textarea>
                </div>

                <div class="col6">
                    <label>{{ __('admin.fields.key_optional') }}</label>
                    <input name="key" value="{{ old('key') }}">
                </div>

                <div class="col3">
                    <label>{{ __('admin.fields.sort_order') }}</label>
                    <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="col3">
                    <label>{{ __('admin.fields.type') }}</label>
                    <input name="type" value="{{ old('type', 'default') }}">
                </div>
            </div>

            <div style="margin-top:14px;">
                <button class="btn ok" type="submit">{{ __('admin.actions.create') }}</button>
            </div>
        </form>
    </div>

    {{-- Add subcategory --}}
    <div class="card" style="flex:1; min-width: 320px;">
        <h2>{{ __('admin.sections.manager.add_subcategory.h2') }}</h2>

        <form method="POST" action="{{ route('admin.restaurants.sections.store', $restaurant) }}">
            @csrf

            <div class="grid">
                <div class="col12">
                    <label>{{ __('admin.sections.manager.parent_category') }}</label>
                    <select name="parent_id" required>
                        <option value="" disabled selected>{{ __('admin.common.choose') }}</option>
                        @foreach($allParents as $p)
                            @php
                                $t = $p->title ?? __('admin.sections.fallback_title', ['id' => $p->id]);
                                $inactive = (isset($p->is_active) && !$p->is_active);
                            @endphp
                            <option value="{{ $p->id }}">
                                {{ $inactive ? '🟥 ' : '' }}{{ $t }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col12">
                    <label>{{ __('admin.fields.title') }}</label>
                    <input name="title" value="{{ old('title') }}" required>
                </div>

                <div class="col12">
                    <label>{{ __('admin.fields.description') }}</label>
                    <textarea name="description" rows="2"
                              style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text);">{{ old('description') }}</textarea>
                </div>

                <div class="col6">
                    <label>{{ __('admin.fields.key_optional') }}</label>
                    <input name="key" value="{{ old('key') }}">
                </div>

                <div class="col3">
                    <label>{{ __('admin.fields.sort_order') }}</label>
                    <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="col3">
                    <label>{{ __('admin.fields.type') }}</label>
                    <input name="type" value="{{ old('type', 'default') }}">
                </div>
            </div>

            <div style="margin-top:14px;">
                <button class="btn ok" type="submit">{{ __('admin.actions.create') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.sections.manager.structure.h2') }}</h2>

    @if($sections->isEmpty())
        <div class="mut" style="font-size:13px;">{{ __('admin.sections.manager.structure.empty') }}</div>
    @else
        @include('admin.sections._tree', ['nodes' => $sections, 'restaurant' => $restaurant, 'level' => 0])
    @endif
</div>

@endsection
