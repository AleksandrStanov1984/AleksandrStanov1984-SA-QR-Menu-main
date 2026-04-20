<style>
    .modal-form__field {
        display: flex;
        flex-direction: column;
    }

    input.is-valid {
        border: 1px solid #22c55e !important;
        background: rgba(34,197,94,0.05);
        box-shadow: 0 0 0 1px rgba(34,197,94,0.25);
    }

    input.is-invalid {
        border: 1px solid #ef4444 !important;
        background: rgba(239,68,68,0.05);
        box-shadow: 0 0 0 1px rgba(239,68,68,0.25);
    }

    input {
        transition: all 0.2s ease;
    }

    .pw-field {
        position: relative;
    }

    .pw-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.6;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .input-error {
        color: #ef4444;
        font-size: 12px;
    }

    .input-msg {
        margin-top: 6px;
        font-size: 12px;
        line-height: 1.3;
    }

    .input-msg.error {
        color: #ef4444;
    }

    .input-msg.success {
        color: #22c55e;
    }

    .password-strength {
        margin-top: 6px;
        font-size: 12px;
        display: flex;
        gap: 6px;
    }

    .password-strength span {
        padding: 2px 6px;
        border-radius: 6px;
        background: rgba(255,255,255,0.05);
        opacity: 0.5;
    }

    .password-strength span.active {
        background: rgba(34,197,94,0.15);
        color: #22c55e;
        opacity: 1;
    }

    .modal-form__field {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .modal-form__field input {
        width: 100%;
        box-sizing: border-box;
    }

    .pw-field {
        position: relative;
        width: 100%;
    }

    .pw-field input {
        width: 100%;
        padding-right: 42px;
        box-sizing: border-box;
    }

    .pw-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>
