<nav
    id="categoryNav"
    class="category-nav"
    role="navigation"
    aria-label="Menu categories"
>
    @foreach($vm->categories as $category)

        <a
            href="#section-{{ $category['id'] }}"
            class="category-link"
            data-target="section-{{ $category['id'] }}"
        >
            {{ $category['title'] }}
        </a>

    @endforeach
</nav>
