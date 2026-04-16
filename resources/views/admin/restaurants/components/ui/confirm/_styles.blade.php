{{-- resources/views/admin/restaurants/components/ui/confirm/_styles.blade.php --}}
{{-- admin/restaurants/components/ui/confirm/_styles --}}
<style>
    .confirm-modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .confirm-modal.hidden {
        display: none;
    }

    .confirm-box {
        background: #1c1c1c;
        padding: 20px;
        border-radius: 12px;
        width: 320px;
        text-align: center;
    }

    .confirm-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
    }
</style>
