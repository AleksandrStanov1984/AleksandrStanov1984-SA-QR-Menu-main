<nav id="categoryNav" class="category-nav">

    @foreach($vm->categories as $category)

<a href="#section-{{ $category['id'] }}" class="category-link">
{{ $category['title'] }}
</a>

@endforeach

</nav>
