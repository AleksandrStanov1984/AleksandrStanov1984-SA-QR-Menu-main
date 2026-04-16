{{-- resources/views/public/templates/united/blocks/footer/footer.blade.php --}}
{{-- public/templates/united/blocks/footer/footer --}}

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

            <div class="footer-bottom__left">

                <span>© {{ date('Y') }}</span>

                <span class="dot">•</span>

                <span>{{ __('footer.crafted_by') }}</span>

                <a href="{{ route('author') }}" class="footer-author-badge">
                    {{ __('footer.author') }}
                </a>

            </div>

        </div>

    </div>

</footer>
