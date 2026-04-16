{{-- resources/views/admin/auth/_styles.blade.php --}}
{{-- admin/auth/_styles --}}
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

    /* ===== AUTH CENTER ===== */

    .auth-layout{
        min-height: calc(100vh - 120px); /* учитывает header */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .auth-card{
        width: 420px;
        max-width: 100%;
    }

    .auth-title{
        margin: 0 0 24px;
        text-align: center;
    }

</style>
