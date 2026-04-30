{{-- resources/views/legal/impressum.blade.php --}}
@extends('public.templates.united.layout.app')

@section('content')

    <section class="legal-page section">
        <div class="container legal-page__container">

            {{-- HEADER --}}
            <header class="legal-page__header">
                <div class="legal-page__kicker">{{ $legal['kicker'] ?? '' }}</div>
                <h1 class="legal-page__title">{{ $legal['title'] }}</h1>
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
                            <a href="#{{ $item['id'] }}" class="legal-page__toc-link">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- CONTENT --}}
            <div class="legal-page__content">

                @foreach($legal['sections'] as $section)
                    <section id="{{ $section['id'] }}" class="legal-page__section">
                        <h2>{{ $section['title'] }}</h2>
                        {!! $section['body'] !!}
                    </section>
                @endforeach

            </div>

        </div>
    </section>

@endsection
