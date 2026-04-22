{{-- resources/views/public/layouts/base.blade.php --}}

<!DOCTYPE html>
<html lang="{{ $vm->locale ?? app()->getLocale() }}">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $vm->merchant->name ?? 'Oleksandr Stanov' }}</title>

    @yield('styles')

</head>

<body @yield('body-attrs')>

@yield('content')

@yield('scripts')

</body>
</html>
