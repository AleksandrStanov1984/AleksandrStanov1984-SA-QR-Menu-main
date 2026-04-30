{{-- resources/views/public/templates/united/blocks/footer/footer.blade.php --}}

<footer class="section section--full site-footer">

    @include('public.templates.united.blocks.header.restaurant-info')

    {{-- ========================================
       SOCIAL ICONS
    ======================================== --}}

    <div class="footer-social-row">

        <div class="footer-social-row__inner">

            <div class="footer-socials">

                @foreach($vm->footer['links'] as $social)

                    @continue(empty($social['url']))

                    <a href="{{ $social['url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="{{ $social['title'] ?? '' }}">

                        <img
                            src="{{ $social['icon'] }}"
                            alt="{{ $social['title'] ?? '' }}"
                            loading="lazy"
                            decoding="async"
                            width="24"
                            height="24"
                        >

                    </a>

                @endforeach

            </div>

        </div>

    </div>


    {{-- ========================================
       FOOTER BOTTOM
    ======================================== --}}

    <div class="footer-bottom">

        <div class="footer-bottom__inner">

            {{-- LEFT --}}
            <div class="footer-bottom__left">

                <span>© {{ date('Y') }}</span>

                <span class="dot">•</span>

                <span>{{ __('footer.crafted_by') }}</span>

                <a href="{{ route('author', [
            'restaurant' => $vm->restaurant,
            'locale' => app()->getLocale()
        ]) }}"
                   class="footer-author-badge">
                    {{ __('footer.author') }}
                </a>

            </div>

            {{-- RIGHT (LEGAL) --}}
            <div class="footer-bottom__right">

                <a href="{{ route('legal.impressum', [
                    'restaurant' => $vm->restaurant,
                    'locale' => app()->getLocale()
                    ]) }}"
                   target="_blank"
                   rel="noopener">
                    {{ __('legal.nav.impressum') }}
                </a>

                <span class="dot">•</span>

                <a href="{{ route('legal.datenschutz', [
                    'restaurant' => $vm->restaurant,
                    'locale' => app()->getLocale()
                    ]) }}"
                   target="_blank"
                   rel="noopener">
                    {{ __('legal.nav.datenschutz') }}
                </a>

            </div>

        </div>

    </div>

</footer>
