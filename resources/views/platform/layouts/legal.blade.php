<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $legal['title'] ?? 'Legal' }}</title>

    <link rel="icon" type="image/svg+xml" href="/assets/system/favicon/favicon.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/system/favicon/favicon-96x96.png">
    <link rel="shortcut icon" href="/assets/system/favicon/favicon.ico">
    <link rel="apple-touch-icon" href="/assets/system/favicon/apple-touch-icon.png">

    @vite(['resources/css/app.css'])
</head>
<body style="background:var(--bg);">

<header style="
    position:sticky;
    top:0;
    z-index:100;
    backdrop-filter:blur(10px);
    background:color-mix(in srgb, var(--card-bg) 85%, transparent);
    border-bottom:1px solid var(--border-color);
">

    <div style="
        max-width:980px;
        margin:0 auto;
        padding:14px 16px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        font-size:13px;
        color:var(--text-muted);
    ">

        <div style="font-weight:500;">
            SA QR Menu
        </div>

        <a href="{{ url()->previous() }}"
           style="color:var(--text-muted);text-decoration:none;"
           onmouseover="this.style.textDecoration='underline'"
           onmouseout="this.style.textDecoration='none'">
            ← Zurück
        </a>

    </div>
</header>


<main style="padding:40px 16px;">
    @yield('content')
</main>


<footer style="
    margin-top:60px;
    border-top:1px solid var(--border-color);
    background:color-mix(in srgb, var(--card-bg) 85%, transparent);
">

    <div style="
        max-width:980px;
        margin:0 auto;
        padding:16px;
        text-align:center;
        font-size:12px;
        color:var(--text-muted);
    ">
        © {{ date('Y') }} SA QR Menu
    </div>

</footer>

</body>
</html>
