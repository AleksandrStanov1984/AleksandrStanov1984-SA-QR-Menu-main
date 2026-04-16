{{-- resources/views/public/layouts/menu.blade.php --}}
{{-- public/layouts/menu --}}
@extends('public.layouts.base')

@section('styles')
    @yield('template-styles')
@endsection

@section('scripts')
    @yield('template-scripts')
@endsection

@section('body-attrs')
    @yield('template-body-attrs')
@endsection

@section('content')
    <div class="min-h-dvh bg-background text-foreground flex flex-col">

        {{-- HEADER --}}
        @yield('menu-header')

        {{-- MAIN --}}
        <div class="flex-1 mx-auto max-w-7xl w-full px-4">
            <div class="grid grid-cols-1 md:grid-cols-[280px_1fr] gap-6">

                {{-- важно: backdrop должен быть ВНЕ sidebar, но в DOM --}}
                <div id="menu-backdrop" class="menu-backdrop"></div>

                @yield('menu-sidebar')

                <main id="menu-content" class="pb-20">
                    @yield('menu-content')
                </main>

            </div>
        </div>

        {{-- FOOTER --}}
        @yield('menu-footer')

    </div>

    <div id="portal"></div>
@endsection
