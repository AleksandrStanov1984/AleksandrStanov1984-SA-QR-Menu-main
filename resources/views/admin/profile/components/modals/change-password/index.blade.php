{{-- resources/views/admin/profile/components/modals/change-password/index.blade.php --}}

<div id="passModal" class="modal" aria-hidden="true">
    <div class="modal__backdrop" onclick="closeModal('passModal')"></div>

    <div class="modal__panel modal__panel--sm">
        <div class="modal__head">
            <h2>{{ __('admin.profile.change_password.h2') }}</h2>
            <button class="btn secondary small" type="button" onclick="closeModal('passModal')">✕</button>
        </div>

        <form method="POST"
              action="{{ route('admin.profile.change_password') }}"
              class="modal-form"
              autocomplete="off">
            @csrf

            {{-- CURRENT PASSWORD --}}
            <div class="modal-form__field">
                <label>{{ __('admin.profile.change_password.current_password') }}</label>
                <div class="pw-field">
                    <input name="current_password"
                           type="password"
                           autocomplete="current-password"
                           required>
                    <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
                </div>
            </div>

            {{-- NEW PASSWORD --}}
            <div class="modal-form__field">
                <label>{{ __('admin.profile.change_password.new_password') }}</label>
                <div class="pw-field">
                    <input name="new_password"
                           type="password"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
                </div>
            </div>

            {{-- CONFIRM NEW PASSWORD --}}
            <div class="modal-form__field">
                <label>{{ __('admin.profile.change_password.confirm_new_password') }}</label>
                <div class="pw-field">
                    <input name="new_password_confirm"
                           type="password"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
                </div>
            </div>

            <div class="modal-form__actions">
                <button class="btn secondary" type="button" onclick="closeModal('passModal')">
                    {{ __('admin.common.cancel') }}
                </button>

                <button class="btn ok" type="submit">
                    {{ __('admin.common.change') }}
                </button>
            </div>
        </form>
    </div>
</div>
