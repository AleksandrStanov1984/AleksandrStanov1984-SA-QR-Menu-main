{{-- resources/views/legal/datenschutz.blade.php --}}
@extends('public.templates.united.layout.app')

@section('content')

    <section class="legal-page section">
        <div class="container legal-page__container">

            {{-- HEADER --}}
            <header class="legal-page__header">
                <div class="legal-page__kicker">{{ $legal['kicker'] ?? '' }}</div>
                <h1 class="legal-page__title">{{ $legal['title'] }}</h1>
                <div class="legal-page__meta">{{ $legal['updated'] ?? '' }}</div>
                <div class="legal-page__line"></div>
            </header>

            {{-- TOC --}}
            @if(!empty($legal['toc']))
                <div class="legal-page__toc">
                    <h2 class="legal-page__toc-title">
                        {{ $legal['toc_title'] }}
                    </h2>

                    <div class="legal-page__toc-grid">
                        @foreach($legal['toc'] as $item)
                            <a class="legal-page__toc-link" href="#{{ $item['id'] }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- CONTENT --}}
            <div class="legal-page__content prose">

                @foreach($legal['sections'] as $section)
                    <section class="legal-page__section" id="{{ $section['id'] }}">
                        <h2>{{ $section['title'] }}</h2>
                        {!! $section['body'] !!}
                    </section>
                @endforeach

            </div>

            {{-- FOOTER NOTE --}}
            @if(!empty($legal['source_note']))
                <div class="legal-page__footer-note">
                    {!! $legal['source_note'] !!}
                </div>
            @endif

        </div>
    </section>

@endsection
