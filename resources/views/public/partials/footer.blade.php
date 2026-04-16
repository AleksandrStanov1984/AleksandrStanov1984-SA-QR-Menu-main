{{-- resources/views/public/partials/footer.blade.php --}}
{{-- public/partials/footer --}}
<footer class="united-footer">

    @if(!empty($vm->footer['links'] ?? []))
        <div class="united-footer__socials">
            @foreach($vm->footer['links'] as $link)
                <a href="{{ $link['url'] ?? '#' }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="{{ $link['title'] ?? '' }}">
                    @if(!empty($link['icon']))
                        <img src="{{ $link['icon'] }}"
                             alt="{{ $link['title'] ?? '' }}">
                    @else
                        {{ $link['title'] ?? '' }}
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    <div class="united-footer__credits">
        © {{ date('Y') }} {{ $vm->merchant->name ?? '' }}
        <br>
        Created with SA Digital Menus
    </div>

</footer>
