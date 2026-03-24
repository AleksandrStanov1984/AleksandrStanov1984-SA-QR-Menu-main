<style>
    .form-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }

    .form-group {
        width: 100%;
        max-width: 320px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 6px;
        font-size: 13px;
        opacity: 0.8;
    }

    .form-group input {
        width: 100%;
        text-align: center; /* 🔥 вот это даёт визуальный центр */
    }

    .form-actions {
        width: 100%;
        max-width: 320px;
        display: flex;
        justify-content: flex-end;
    }

</style>
