@extends('admin.layout')

@section('title', 'Dashboard')
@section('subtitle')
    @if($user?->is_super_admin)
        Super Admin
    @else
        Restaurant Admin
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="card" style="flex:1; min-width: 320px;">
            <h2>Current context</h2>

            @if ($currentRestaurant)
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <span class="pill {{ $currentRestaurant->is_active ? 'green' : 'red' }}">
                        {{ $currentRestaurant->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                    <div>
                        <div style="font-weight:700;">{{ $currentRestaurant->name }}</div>
                        <div class="mut" style="font-size:12px;">Slug: {{ $currentRestaurant->slug }}</div>
                    </div>
                </div>

                <div style="margin-top: 14px; display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="btn" href="{{ route('admin.restaurants.edit', $currentRestaurant) }}">Open restaurant editor</a>
                </div>
            @else
                <div class="mut">No restaurant selected.</div>
            @endif
        </div>

        @if($user?->is_super_admin)
            <div class="card" style="flex:1; min-width: 320px;">
                <h2>Pick a restaurant</h2>
                <form method="POST" action="{{ route('admin.select_restaurant') }}">
                    @csrf
                    <label>Restaurant</label>
                    <select name="restaurant_id" required>
                        <option value="" disabled selected>Select…</option>
                        @foreach($restaurants as $r)
                            <option value="{{ $r->id }}">
                                {{ $r->is_active ? '' : '🟥 ' }}{{ $r->name }} ({{ $r->slug }})
                            </option>
                        @endforeach
                    </select>
                    <div style="margin-top: 12px; display:flex; gap:10px;">
                        <button class="btn" type="submit">Select</button>
                        <a class="btn secondary" href="{{ route('admin.restaurants.create') }}">+ Add restaurant</a>
                        <a class="btn secondary" href="{{ route('admin.restaurants.index') }}">All restaurants</a>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <div class="card" style="margin-top:16px;">
        <h2>Next steps (MVP)</h2>
        <ul style="margin:0; padding-left:18px;" class="mut">
            <li>Restaurant editor: languages import, categories, items, banners, socials, themes.</li>
            <li>Asset uploads go to <code>storage/app/public/restaurants/{id}/…</code></li>
            <li>Menu generation: first create full file, later update only data blocks.</li>
        </ul>
    </div>
@endsection
