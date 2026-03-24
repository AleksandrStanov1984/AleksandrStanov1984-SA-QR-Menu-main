<style>
    /* ===== LOADER ===== */

    .qr-loader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        pointer-events: all;
    }

    .qr-loader__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(4px);
    }

    .qr-loader__spinner {
        position: relative;
        z-index: 1;
        width: 56px;
        height: 56px;
        border: 4px solid rgba(255,255,255,0.25);
        border-top-color: #fff;
        border-radius: 50%;
        animation: qr-spin 0.9s linear infinite;
    }

    @keyframes qr-spin {
        to { transform: rotate(360deg); }
    }

    /* ===== FLASH ===== */

    .flash-success {
        background: #1f8f6a;
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .flash-error {
        background: #e74c3c;
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .flash-success,
    .flash-error {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-weight: 500;
    }
</style>
