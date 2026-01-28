<div class="card">
    <h2>{{ __('admin.profile.h2') }}</h2>

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        <div class="grid">
            <div class="col6">
                <label>{{ __('admin.fields.name') }}</label>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.email') }}</label>
                <input value="{{ $user->email }}" disabled>
            </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn ok" type="submit">{{ __('admin.common.save') }}</button>
            <button class="btn secondary" type="button" onclick="openModal('emailModal')">
                {{ __('admin.profile.change_email_btn') }}
            </button>
            <button class="btn secondary" type="button" onclick="openModal('passModal')">
                {{ __('admin.profile.change_password_btn') }}
            </button>
        </div>
    </form>
</div>
