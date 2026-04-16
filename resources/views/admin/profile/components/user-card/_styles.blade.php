{{-- resources/views/admin/profile/components/user-card/_styles.blade.php --}}
{{-- admin/profile/components/user-card/_styles --}}
<style>

    .profile-card {
        width: 100%;
        max-width: 100%;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(260px, 1fr));
        gap: 18px 24px;
        align-items: start;
    }

    .profile-field {
        min-width: 0;
    }

    .profile-field label {
        display: block;
        margin-bottom: 8px;
    }

    .profile-field input {
        display: block;
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .profile-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 900px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
