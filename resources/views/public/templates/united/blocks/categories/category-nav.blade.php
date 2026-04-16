{{-- resources/views/public/templates/united/blocks/categories/category-nav.blade.php --}}
{{-- public/templates/united/blocks/categories/category-nav --}}
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
