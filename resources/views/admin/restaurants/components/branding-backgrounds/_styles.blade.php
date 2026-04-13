<style>
    .branding-card {
        max-width: 100%;
    }

    .branding-theme-block {
        margin-top: 12px;
    }

    .branding-theme-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 20px;
        align-items: center;
        margin-top: 10px;
    }

    .branding-theme-option {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 140px;
        margin: 0;
        cursor: pointer;
        color: var(--text, #fff);
    }

    .branding-theme-option input[type="radio"] {
        margin: 0;
        flex: 0 0 auto;
    }

    .branding-theme-option span {
        line-height: 1.2;
        word-break: break-word;
    }

    .branding-grid {
        display: grid;
        gap: 16px;
        margin: 12px auto;
        max-width: 900px;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }

    .branding-col {
        min-width: 0;
    }

    .branding-col label {
        display: block;
        margin-bottom: 8px;
    }

    .branding-preview-wrap {
        overflow: hidden;
        border-radius: 12px;
    }

    .branding-preview {
        width: 100%;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--line, rgba(255,255,255,.08));
        background: rgba(255,255,255,.03);
        transition: transform 0.3s ease;
        display: block;
    }

    .branding-preview-wrap:hover .branding-preview {
        transform: scale(1.05);
    }

    .branding-preview--empty {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted, #9aa4b2);
        font-size: 14px;
    }

    /* =========================
   MOBILE (≤768px)
========================= */
    @media (max-width: 768px) {

        .branding-card {
            padding: 14px;
        }

        .branding-theme-row {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .branding-theme-option {

            align-items: center !important;
            justify-content: flex-start !important;

            gap: 12px !important;
            width: 100%;
            min-width: 0;

            padding: 10px 12px;

            border: 1px solid var(--line, rgba(255,255,255,.08));
            border-radius: 10px;
            background: rgba(255,255,255,.02);

            text-align: left !important;

            display: flex !important;
            flex-direction: column;
        }

        .branding-theme-option input[type="radio"] {
            margin: 0 !important;
            flex: 0 0 auto !important;
            transform: scale(1.05);
        }

        .branding-theme-option span {
            flex: 1 1 auto !important;
            text-align: left !important;
            line-height: 1.3;
            word-break: break-word;
        }

        .branding-grid {
            grid-template-columns: 1fr;
            max-width: 100%;
        }

        .actions .btn {
            width: 100%;
        }
    }


    /* =========================
       SMALL MOBILE (≤480px)
    ========================= */
    @media (max-width: 480px) {

        .branding-card {
            padding: 12px;
        }

        .branding-card h2 {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .branding-theme-option {
            padding: 8px 10px !important;
            gap: 8px !important;
            display: flex !important;
            flex-direction: column;
        }

        .branding-theme-option input[type="radio"] {
            transform: scale(1);
        }

        .branding-theme-option span {
            font-size: 14px;
            line-height: 1.25;
        }

        .branding-preview {
            aspect-ratio: 4 / 3;
        }

        .branding-col input[type="file"] {
            width: 100%;
            font-size: 12px;
        }
    }


    /* =========================
       ULTRA SMALL (≤360px)
    ========================= */
    @media (max-width: 360px) {

        .branding-card {
            padding: 10px;
        }

        .branding-theme-option {
            padding: 7px 8px !important;
            gap: 6px !important;
            display: flex !important;
            flex-direction: column;
        }

        .branding-theme-option span {
            font-size: 13px;
        }

        .branding-card h2 {
            font-size: 16px;
            margin-bottom: 8px;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .branding-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1025px) {
        .branding-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
