<style>

    .crumbs a {
        opacity: 0.7;
    }

    .crumbs a:hover {
        opacity: 1;
    }

    .crumbs span:last-child {
        color: #fff;
        opacity: 1;
    }

    .ui-select {
        position: relative;
        display: inline-block;
    }

    .ui-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;

        min-width: 60px;

        padding: 6px 10px;

        border-radius: 8px;
        border: 1px solid var(--line);

        background: var(--card);
        color: var(--text-primary);

        font-size: 13px;
        cursor: pointer;
    }

    .ui-select-trigger:hover {
        border-color: rgba(255,255,255,0.2);
    }

    .ui-select-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;

        min-width: 100%;

        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 8px;

        box-shadow: 0 10px 30px rgba(0,0,0,.4);

        overflow: hidden;
        display: none;
        z-index: 100;
    }

    .ui-select.open .ui-select-dropdown {
        display: block;
    }

    .ui-select-option {
        padding: 8px 12px;
        cursor: pointer;
        font-size: 13px;
    }

    .ui-select-option:hover {
        background: rgba(255,255,255,0.06);
    }

    .ui-select-option.active {
        background: #2563eb;
        color: #fff;
    }
</style>
