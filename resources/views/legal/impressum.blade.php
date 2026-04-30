{{-- resources/views/legal/impressum.blade.php --}}

@extends('public.templates.united.layout.app')

@php
    $isLegalPage = true;

    $toc = __('legal.impressum.toc');
    $sections = __('legal.impressum.sections');

    if (!is_array($toc)) {
        $toc = [];
    }

    if (!is_array($sections)) {
        $sections = [];
    }

    $ownerSearch = [
        ':owner_name',
        ':owner_address_line_1',
        ':owner_address_line_2',
        ':owner_country',
        ':owner_email',
        ':owner_phone',
    ];

    $ownerReplace = [
        __('legal_owner.name'),
        __('legal_owner.address_line_1'),
        __('legal_owner.address_line_2'),
        __('legal_owner.country'),
        __('legal_owner.email'),
        __('legal_owner.phone'),
    ];
@endphp

@section('content')

    <section class="section">
        <div class="container legal-page__container">

            <header class="legal-page__header">
                <div class="legal-page__kicker">{{ __('legal.impressum.kicker') }}</div>
                <h1 class="legal-page__title">{{ __('legal.impressum.title') }}</h1>
                <div class="legal-page__line"></div>
            </header>

            @if(count($toc))
                <div class="legal-page__toc">
                    <h2 class="legal-page__toc-title">{{ __('legal.impressum.toc_title') }}</h2>

                    <div class="legal-page__toc-grid">
                            <?php foreach ($toc as $item): ?>
                        <a href="#{{ $item['id'] }}" class="legal-page__toc-link">
                            {{ $item['label'] }}
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            @endif

            <div class="legal-page__content">

                    <?php foreach ($sections as $section): ?>

                @php
                    $body = str_replace($ownerSearch, $ownerReplace, $section['body']);
                    $body = str_replace(
                        [
                            'mailto::owner_email',
                            'tel::owner_phone',
                        ],
                        [
                            'mailto:' . __('legal_owner.email'),
                            'tel:' . preg_replace('/\s+/', '', __('legal_owner.phone')),
                        ],
                        $body
                    );
                @endphp

                <section id="{{ $section['id'] }}" class="legal-page__section">
                    <h2>{{ $section['title'] }}</h2>
                    {!! $body !!}
                </section>

                <?php endforeach; ?>

            </div>

        </div>
    </section>

@endsection
