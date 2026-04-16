{{-- resources/views/public/author/sections/panels.blade.php --}}
{{-- public/author/sections/panels --}}
@php
    $tPrefix = 'author';
@endphp

<div class="section section--full author-panels">
    <div class="author-panel">
        <h2 class="author-panel__title">{{ __($tPrefix.'.contact.title') }}</h2>
        <p class="author-panel__text">{{ __($tPrefix.'.contact.text') }}</p>

        <div class="author-panel__list">
            <div><span>{{ __($tPrefix.'.contact.location_label') }}</span> {{ __($tPrefix.'.contact.location') }}</div>
            <div><span>{{ __($tPrefix.'.contact.email_label') }}</span>
                <a href="mailto:{{ __($tPrefix.'.contact.email') }}">{{ __($tPrefix.'.contact.email') }}</a>
            </div>
            <div><span>{{ __($tPrefix.'.contact.phone_label') }}</span>
                <a href="tel:{{ __($tPrefix.'.contact.phone_raw') }}">{{ __($tPrefix.'.contact.phone') }}</a>
            </div>
            <div><span>{{ __($tPrefix.'.contact.github_label') }}</span>
                <a href="{{ __($tPrefix.'.contact.github_url') }}" target="_blank" rel="noopener">{{ __($tPrefix.'.contact.github') }}</a>
            </div>
            <div><span>{{ __($tPrefix.'.contact.linkedin_label') }}</span>
                <a href="{{ __($tPrefix.'.contact.linkedin_url') }}" target="_blank" rel="noopener">{{ __($tPrefix.'.contact.linkedin') }}</a>
            </div>
            <div><span>{{ __($tPrefix.'.contact.langs_label') }}</span> {{ __($tPrefix.'.contact.langs') }}</div>
        </div>
    </div>

    <div class="author-panel">
        <h2 class="author-panel__title">{{ __($tPrefix.'.about.title') }}</h2>
        <p>{{ __($tPrefix.'.about.p1') }}</p>
        <p>{{ __($tPrefix.'.about.p2') }}</p>
        <p>{{ __($tPrefix.'.about.p3') }}</p>
        <p>{{ __($tPrefix.'.about.p4') }}</p>
    </div>
</div>
