{{-- resources/views/public/templates/united/blocks/menu/menu-section.blade.php --}}

{{-- resources/views/public/templates/united/blocks/menu/menu-section.blade.php --}}

{{-- 🔥 АНКОР (для фиксации) --}}
<div id="menuStickyAnchor" class="menu-sticky-anchor"></div>

{{-- 🔥 STICKY GROUP --}}
<div id="menuStickyGroup" class="menu-sticky-group">
    <div class="menu-sticky-group__inner">

        {{-- SCROLLSPY --}}
        @include('public.templates.united.blocks.categories.category-nav')

        {{-- SEARCH --}}
        <div class="menu-search-wrap">
            <div class="menu-search">
                <input
                    type="text"
                    id="publicSearchInput"
                    placeholder="{{ __('menu.search') }}"
                    autocomplete="off"
                >

                <div
                    id="publicSearchResults"
                    class="menu-search-results"
                ></div>
            </div>
        </div>

    </div>
</div>

@foreach($vm->categories as $category)

    <section
        id="section-{{ $category['id'] }}"
        class="menu-section"
        data-search="{{ mb_strtolower($category['title']) }}"
        data-type="category"
        data-id="{{ $category['id'] }}"
    >

        <h2 class="menu-section-title" >
            {{ $category['title'] }}
        </h2>

        <div class="menu-grid">

            @foreach($category['items'] as $item)

                @include('public.templates.united.blocks.menu.item-card', ['item' => $item])

            @endforeach

        </div>


        @foreach($category['subcategories'] as $subcategory)

            @if(!empty($subcategory['items']))

                <h3
                    class="menu-subcategory-title"
                    id="subcategory-{{ $subcategory['id'] }}"
                    data-search="{{ mb_strtolower($subcategory['title']) }}"
                    data-type="subcategory"
                    data-id="{{ $subcategory['id'] }}">

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
