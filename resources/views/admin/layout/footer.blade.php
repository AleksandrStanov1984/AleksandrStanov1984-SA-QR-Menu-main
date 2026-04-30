{{-- resources/views/admin/layout/footer.blade.php --}}

<footer style="border-top:1px solid var(--line);padding:12px 14px;font-size:12px;color:var(--mut);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">

    <div>
        © {{ date('Y') }} {{ __('admin.brand') }}
    </div>

    <div style="display:flex;gap:12px;">
        <a href="{{ route('platform.legal.impressum') }}"
           target="_blank"
           style="color:var(--mut);text-decoration:none;">
            {{ __('admin.impressum') }}
        </a>

        <a href="{{ route('platform.legal.datenschutz') }}"
           target="_blank"
           style="color:var(--mut);text-decoration:none;">
            {{ __('admin.datenschutz') }}
        </a>
    </div>

</footer>
