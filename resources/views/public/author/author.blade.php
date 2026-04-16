{{-- resources/views/public/author/author.blade.php --}}
{{-- public/author/author --}}
@extends('public.layouts.base')

@section('content')

    @include('public.author.sections.hero', [
        'profileImage' => $profileImage ?? null,
        'icons' => $icons ?? [],
        'links' => $links ?? [],
    ])

@endsection
