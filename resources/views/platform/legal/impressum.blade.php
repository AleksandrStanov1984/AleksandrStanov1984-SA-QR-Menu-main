{{-- resources/views/platform/legal/impressum.blade.php --}}
@extends('platform.layouts.legal')

@section('content')

    <section class="legal-page section">
        <div class="container legal-page__container">

            {{-- HEADER --}}
            <header class="legal-page__header">
                <div class="legal-page__kicker">
                    {{ $legal['kicker'] ?? '' }}
                </div>

                <h1 class="legal-page__title">
                    {{ $legal['title'] ?? 'Legal' }}
                </h1>

                @if(!empty($legal['updated']))
                    <div class="legal-page__meta">
                        {{ $legal['updated'] }}
                    </div>
                @endif

                <div class="legal-page__line"></div>
            </header>

            {{-- TOC --}}
            @if(!empty($legal['toc']) && is_array($legal['toc']))
                <div class="legal-page__toc">
                    <h2 class="legal-page__toc-title">
                        {{ $legal['toc_title'] ?? '' }}
                    </h2>

                    <div class="legal-page__toc-grid">
                        @foreach($legal['toc'] as $item)
                            @if(!empty($item['id']) && !empty($item['label']))
                                <a href="#{{ $item['id'] }}" class="legal-page__toc-link">
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- CONTENT --}}
            <div class="legal-page__content prose">

                @if(!empty($legal['sections']) && is_array($legal['sections']))

                    @foreach($legal['sections'] as $section)

                        @if(!empty($section['id']) && !empty($section['title']))

                            <section id="{{ $section['id'] }}" class="legal-page__section">

                                <h2>{{ $section['title'] }}</h2>

                                @if(!empty($section['body']))
                                    {!! $section['body'] !!}
                                @endif

                            </section>

                        @endif

                    @endforeach

                @endif

            </div>

        </div>
    </section>

@endsection
