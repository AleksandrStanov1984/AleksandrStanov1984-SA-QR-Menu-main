{{-- resources/views/admin/restaurants/components/branding-backgrounds/og/index.blade.php --}}

@php
    $locales = config('locales.all', ['de']);
    $limit = $restaurant->feature('og_limit');

    $meta = is_array($restaurant->meta ?? null) ? $restaurant->meta : [];
    $og = $meta['og'] ?? [];

    $filledCount = count(array_filter($og));

@endphp

@if($restaurant->feature('og_images'))

    <div class="card og-card">

        <div class="og-card__header">
            <h2>{{ __('admin.og.title') ?? 'OG Images' }}</h2>
        </div>

        <div class="og-grid">

            @foreach($locales as $locale)

                @php
                    $hasImage = !empty($og[$locale]);

                    $url = app(\App\Services\ImageService::class)->ogForLocale($restaurant, $locale);
                @endphp

                <div class="og-item">

                    <div class="og-item__title">
                        {{ strtoupper($locale) }}
                    </div>

                    <img src="{{ $url }}" class="og-preview">

                    {{-- UPLOAD --}}
                    @if(!$hasImage && ($limit === null || $filledCount < $limit))

                        <form method="POST"
                              enctype="multipart/form-data"
                              action="{{ route('admin.restaurants.og.upload', $restaurant) }}"
                              class="og-form">
                            @csrf

                            <input type="hidden" name="locale" value="{{ $locale }}">

                            <label class="btn-file">
                                {{ __('admin.common.choose_file') }}
                                <input type="file" name="image" class="og-input">
                            </label>

                            <button class="btn btn-primary">
                                {{ __('admin.common.save') }}
                            </button>
                        </form>

                    @endif

                    {{-- DELETE --}}
                    @if($hasImage)

                        <form method="POST"
                              action="{{ route('admin.restaurants.og.delete', [$restaurant, $locale]) }}">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger">
                                {{ __('admin.common.remove') }}
                            </button>
                        </form>

                    @endif

                </div>

            @endforeach

        </div>

    </div>

@endif

@include('admin.restaurants.components.branding-backgrounds.og._scripts')
