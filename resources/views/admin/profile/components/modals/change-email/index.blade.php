{{-- resources/views/admin/profile/components/modals/change-email/index.blade.php --}}
{{-- admin/profile/components/modals/change-email/index --}}

@include('admin.profile.components.modals.change-email._styles')

<div id="emailModal" class="modal" aria-hidden="true">
    <div class="modal__backdrop" onclick="closeModal('emailModal')"></div>

    <div class="modal__panel modal__panel--sm">
        <div class="modal__head">
            <h2>{{ __('admin.profile.change_email.h2') }}</h2>
            <button class="btn secondary small" type="button" onclick="closeModal('emailModal')">✕</button>
        </div>

        <form method="POST"
              action="{{ route('admin.profile.change_email') }}"
              class="modal-form"
              autocomplete="off">
            @csrf

            <div class="modal-form__field">
                <label>{{ __('admin.profile.change_email.current_email') }}</label>
                <input name="current_email" type="email" autocomplete="email" required>
            </div>

            <div class="modal-form__field">
                <label>{{ __('admin.profile.change_email.current_password') }}</label>
                <div class="pw-field">
                    <input name="current_password" type="password" autocomplete="current-password" required>
                    <button type="button" class="pw-toggle">👁</button>
                </div>
            </div>

            <div class="modal-form__field">
                <label>{{ __('admin.profile.change_email.new_email') }}</label>
                <input name="new_email" type="email" autocomplete="email" required>
            </div>

            <div class="modal-form__actions">
                <button class="btn secondary" type="button" onclick="closeModal('emailModal')">
                    {{ __('admin.common.cancel') }}
                </button>
                <button class="btn ok" type="submit">
                    {{ __('admin.common.change') }}
                </button>
            </div>
        </form>
    </div>
</div>
