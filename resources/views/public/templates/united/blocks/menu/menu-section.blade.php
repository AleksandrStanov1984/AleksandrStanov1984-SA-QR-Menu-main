{{-- resources/views/public/templates/united/blocks/menu/menu-section.blade.php --}}
{{-- public/templates/united/blocks/menu/menu-section --}}

@include('public.templates.united.blocks.categories.category-nav')

@foreach($vm->categories as $category)

    <section
        id="section-{{ $category['id'] }}"
        class="menu-section"
    >

        <h2 class="menu-section-title">
            {{ $category['title'] }}
        </h2>


        {{-- items категории --}}
        <div class="menu-grid">

            @foreach($category['items'] as $item)

                @include('public.templates.united.blocks.menu.item-card', ['item' => $item])

            @endforeach

        </div>


        {{-- подкатегории --}}
        @foreach($category['subcategories'] as $subcategory)

            @if(!empty($subcategory['items']))

                <h3 class="menu-subcategory-title">
                    {{ $subcategory['title'] }}
                </h3>

                <div class="menu-grid">

                    @foreach($subcategory['items'] as $item)

                        @include('public.templates.united.blocks.menu.item-card', ['item' => $item])

                    @endforeach

                </div>

            @endif

        @endforeach

    </section>

@endforeach
