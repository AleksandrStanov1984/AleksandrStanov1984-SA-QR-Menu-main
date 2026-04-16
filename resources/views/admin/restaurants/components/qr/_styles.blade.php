{{-- resources/views/admin/restaurants/components/qr/_styles.blade.php --}}
{{-- admin/restaurants/components/qr/_styles --}}
<style>
    /* ===== ОСНОВА ===== */

    .card .qr-block {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 18px !important;
        width: 100% !important;
        text-align: center !important;
    }

    .card .qr-left,
    .card .qr-right {
        width: 100% !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ===== QR CARD ===== */

    .card .qr-preview {
        width: 320px !important;
        height: 420px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #ffffff !important;
        border-radius: 22px !important;
        overflow: hidden !important;
        padding: 14px !important;
        margin: 6px 0 10px 0 !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28) !important;
        position: relative !important;
        transition: transform .2s ease, box-shadow .2s ease !important;
    }

    .card .qr-preview img {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 16px !important;
    }

    .card .qr-preview:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 24px 54px rgba(0, 0, 0, 0.34) !important;
    }

    /* ===== ACTIONS ===== */

    .card .qr-actions {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 12px !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card .qr-actions .btn,
    .card .qr-actions a.btn {
        min-width: 140px !important;
        display: inline-flex !important;
        justify-content: center !important;
        align-items: center !important;
    }

    /* ===== DOWNLOAD DROPDOWN ===== */

    .qr-download {
        position: relative;
    }

    .qr-dropdown {
        display: none;
        position: absolute;
        top: 110%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2a44;
        border-radius: 10px;
        padding: 8px;
        min-width: 140px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.35);
        z-index: 200;
    }

    .qr-dropdown a {
        display: block;
        padding: 8px 12px;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        transition: background 0.15s ease;
    }

    .qr-dropdown a:hover {
        background: rgba(255,255,255,0.12);
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }


    @media (max-width: 768px) {
        .card .qr-actions {
            flex-direction: column !important;
            width: 100% !important;
        }

        .card .qr-actions .btn {
            width: 100% !important;
            min-width: unset !important;
        }
    }
</style>
