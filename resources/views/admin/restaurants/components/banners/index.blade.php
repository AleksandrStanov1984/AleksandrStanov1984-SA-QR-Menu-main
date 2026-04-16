{{-- resources/views/admin/restaurants/components/banners/index.blade.php --}}

@extends('admin.layout')

@include('admin.restaurants.components.banners._styles')

@section('content')

    <div class="card wh-card">

        <div class="wh-card__header">
            <strong>{{ __('banners.title') }}</strong>
        </div><br>

        @php
            $banners = $banners ?? collect();
            $bannersBySlot = $banners->keyBy('slot');
            $images = app(\App\Services\ImageService::class);
        @endphp

        <div class="banners-grid"
             data-banners
             data-restaurant-id="{{ $restaurant->id }}"
             data-save-url="{{ route('admin.restaurants.banners.save', $restaurant) }}"
             data-delete-all-url="{{ route('admin.restaurants.banners.destroyAll', $restaurant) }}"
             data-reorder-url="{{ route('admin.restaurants.banners.reorder', $restaurant) }}">

            @foreach(range(1,5) as $slot)

                @php
                    $banner = $bannersBySlot[$slot] ?? null;
                    $image = $images->banner($banner?->image_path);
                @endphp

                <div class="banner-card"
                     data-slot="{{ $slot }}"
                     data-id="{{ $banner?->id }}"
                     draggable="{{ $banner ? 'true' : 'false' }}">

                    {{-- DRAG --}}
                    @if($banner)
                        <div class="banner-drag">☰</div>
                    @endif

                    {{-- PREVIEW --}}
                    <div class="banner-preview">
                        <img src="{{ $image }}" alt="banner">
                    </div>

                    {{-- INPUT --}}
                    <label class="banner-file-btn">

                        {{ __('admin.common.choose_file') ?? 'Datei wählen' }}

                        <input type="file"
                               class="banner-input banner-file-input"
                               data-slot="{{ $slot }}"
                               accept=".jpg,.jpeg,.png,.webp">

                    </label>

                    {{-- ACTIONS --}}
                    <div class="banner-actions">

                        {{-- SAVE ONE --}}
                        <button type="button"
                                class="btn small primary btn-save-one"
                                data-slot="{{ $slot }}">
                            {{ __('banners.actions.save_one') }}
                        </button>

                        {{-- DELETE ONE --}}
                        @if($banner)
                            <button type="button"
                                    class="btn small danger btn-delete"
                                    data-id="{{ $banner->id }}">
                                {{ __('banners.actions.delete') }}
                            </button>
                        @else
                            <button type="button"
                                    class="btn small danger"
                                    disabled>
                                {{ __('banners.actions.delete') }}
                            </button>
                        @endif

                    </div>

                </div>

            @endforeach

        </div>

        {{-- GLOBAL ACTIONS --}}
        <div class="banner-global-actions">

            <button type="button"
                    class="btn danger"
                    id="deleteAll"
                {{ $banners->isEmpty() ? 'disabled' : '' }}>
                {{ __('banners.actions.delete_all') }}
            </button>

            <button type="button"
                    class="btn primary"
                    id="saveAll">
                {{ __('banners.actions.save_all') }}
            </button>

        </div>

    </div>

@endsection

@include('admin.restaurants.components.banners._scripts')
