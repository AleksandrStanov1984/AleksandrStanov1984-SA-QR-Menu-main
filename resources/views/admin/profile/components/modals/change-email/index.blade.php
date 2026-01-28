<div id="emailModal" class="modal" aria-hidden="true">
    <div class="modal__backdrop" onclick="closeModal('emailModal')"></div>
    <div class="modal__panel">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
            <h2 style="margin:0;">{{ __('admin.profile.change_email.h2') }}</h2>
            <button class="btn secondary small" type="button" onclick="closeModal('emailModal')">X</button>
        </div>

        <form method="POST"
              action="{{ route('admin.profile.change_email') }}"
              style="margin-top:12px;"
              autocomplete="off">
            @csrf

            <label>{{ __('admin.profile.change_email.current_email') }}</label>
            <input name="current_email" type="email" autocomplete="email" required>

            <label>{{ __('admin.profile.change_email.current_password') }}</label>
            <div class="pw-field">
                <input name="current_password" type="password" autocomplete="current-password" required>
                <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
            </div>

            <label>{{ __('admin.profile.change_email.new_email') }}</label>
            <input name="new_email" type="email" autocomplete="email" required>

            <div style="margin-top:14px; display:flex; gap:10px; justify-content:flex-end;">
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
