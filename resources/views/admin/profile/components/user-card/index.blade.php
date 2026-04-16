{{-- resources/views/admin/profile/components/user-card/index.blade.php --}}
{{-- admin/profile/components/user-card/index --}}

@include('admin.profile.components.user-card._styles')


<div class="card profile-card">
    <h2>{{ __('admin.profile.h2') }}</h2>

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        <div class="profile-grid">
            <div class="profile-field">
                <label>{{ __('admin.fields.name') }}</label>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="profile-field">
                <label>{{ __('admin.fields.email') }}</label>
                <input value="{{ $user->email }}" disabled>
            </div>
        </div>

        <div class="profile-actions">
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
