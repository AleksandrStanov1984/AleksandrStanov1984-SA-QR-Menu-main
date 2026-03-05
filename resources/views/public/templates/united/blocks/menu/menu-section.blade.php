@foreach($vm->categories as $category)

    <section
        id="section-{{ $category['id'] }}"
        class="menu-section"
    >

        <h2 class="menu-section-title">
            {{ $category['title'] }}
        </h2>

        <div class="menu-grid">

            {{-- items категории --}}

            @foreach($category['items'] as $item)

                @include('public.templates.united.blocks.menu.item-card', ['item' => $item])

            @endforeach


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

        </div>

    </section>

@endforeach
