@php
    $user = auth()->user();
@endphp

<div class="sb-user">
    {{-- logo placeholder --}}
    <div class="sb-logo">
        <div class="sb-logo-circle">{{ __('admin.sidebar.logo') }}</div>
    </div>

    <div class="sb-username">
        {{ $user->name }}
    </div>
</div>
