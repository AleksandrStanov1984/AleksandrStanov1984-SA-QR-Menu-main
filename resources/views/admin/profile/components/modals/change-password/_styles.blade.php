{{-- resources/views/admin/profile/components/modals/change-password/_styles.blade.php --}}
{{-- admin/profile/components/modals/change-password/_styles --}}
<style>
    .modal__panel--sm{
        width: min(760px, calc(100vw - 32px));
    }

    .modal__head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
    }

    .modal__head h2{
        margin:0;
    }

    .modal-form{
        margin-top:16px;
        display:flex;
        flex-direction:column;
        gap:14px;
    }

    .modal-form__field{
        display:flex;
        flex-direction:column;
        gap:6px;
        min-width:0;
    }

    .modal-form__field input,
    .modal-form__field .pw-field{
        width:100%;
    }

    .modal-form__field input{
        box-sizing:border-box;
    }

    .pw-field{
        position:relative;
        display:flex;
        align-items:center;
    }

    .pw-field input{
        width:100%;
        padding-right:42px;
        box-sizing:border-box;
    }

    .pw-toggle{
        position:absolute;
        right:10px;
        top:50%;
        transform:translateY(-50%);
        background:none;
        border:none;
        cursor:pointer;
    }

    .modal-form__actions{
        display:flex;
        justify-content:flex-end;
        gap:10px;
        flex-wrap:wrap;
    }

    @media (max-width: 640px){
        .modal__panel--sm{
            width: calc(100vw - 16px);
        }
    }
</style>
