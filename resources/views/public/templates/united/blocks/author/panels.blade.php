{{-- resources/views/public/templates/united/blocks/author/panels.blade.php --}}

<div class="section section--full author-panels">

    {{-- ========================= --}}
    {{-- CONTACT --}}
    {{-- ========================= --}}
    <div class="author-panel">

        <h2 class="author-panel__title">
            {{ __('author.contact.title') }}
        </h2>

        <p class="author-panel__text">
            {{ __('author.contact.text') }}
        </p>

        <div class="author-panel__list">

            <div>
                <span>{{ __('author.contact.location_label') }}</span>
                {{ __('author.contact.location') }}
            </div>

            <div>
                <span>{{ __('author.contact.email_label') }}</span>
                <a href="mailto:{{ __('author.contact.email') }}">
                    {{ __('author.contact.email') }}
                </a>
            </div>

            <div>
                <span>{{ __('author.contact.phone_label') }}</span>
                <a href="tel:{{ __('author.contact.phone_raw') }}">
                    {{ __('author.contact.phone') }}
                </a>
            </div>

            <div>
                <span>{{ __('author.contact.github_label') }}</span>
                <a href="{{ __('author.contact.github_url') }}" target="_blank" rel="noopener">
                    {{ __('author.contact.github') }}
                </a>
            </div>

            <div>
                <span>{{ __('author.contact.linkedin_label') }}</span>
                <a href="{{ __('author.contact.linkedin_url') }}" target="_blank" rel="noopener">
                    {{ __('author.contact.linkedin') }}
                </a>
            </div>

            <div>
                <span>{{ __('author.contact.langs_label') }}</span>
                {{ __('author.contact.langs') }}
            </div>

        </div>

    </div>

    {{-- ========================= --}}
    {{-- ABOUT --}}
    {{-- ========================= --}}
    <div class="author-panel">

        <h2 class="author-panel__title">
            {{ __('author.about.title') }}
        </h2>

        <p>{{ __('author.about.p1') }}</p>
        <p>{{ __('author.about.p2') }}</p>
        <p>{{ __('author.about.p3') }}</p>
        <p>{{ __('author.about.p4') }}</p>

    </div>

</div>
