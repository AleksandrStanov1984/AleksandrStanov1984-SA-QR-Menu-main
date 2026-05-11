{{-- resources/views/admin/restaurants/components/import/_json.blade.php --}}

@php
    use App\Support\Permissions;
@endphp

@if(Permissions::can(auth()->user(), 'import.menu_json'))

    <div class="mb-import-block">

        <div class="mb-import-header">
            <span class="mb-import-title">
                {{ __('admin.import.json.title') }}
            </span>
        </div>

        <form method="POST"
              action="{{ route('admin.restaurants.menu.import_json', $restaurant) }}"
              enctype="multipart/form-data">

            @csrf

            {{-- ========================= --}}
            {{-- IMPORT MODE --}}
            {{-- ========================= --}}
            <input
                type="hidden"
                name="import_mode"
                value="create"
                data-import-mode-input
            >

            <div class="mb-import-row">

                {{-- FILE --}}
                <div class="mb-import-left">

                    <label class="branding-file-btn">

                        {{ __('admin.common.choose_file') }}

                        <input
                            type="file"
                            name="menu_json"
                            class="branding-file-input"
                            data-import-input="json"
                            accept=".json,application/json"
                            required
                        >

                        <div
                            class="branding-file-name"
                            data-import-file-name="json"
                        >
                            {{ __('admin.import.no_file_selected') }}
                        </div>

                    </label>

                </div>

                {{-- ACTIONS --}}
                <div class="mb-import-right">

                    {{-- ========================= --}}
                    {{-- CREATE --}}
                    {{-- ========================= --}}
                    <button
                        class="btn btn-primary"
                        type="submit"
                        data-import-mode="create"
                    >
                        {{ __('admin.import.json.create') }}
                    </button>

                    {{-- ========================= --}}
                    {{-- ADD --}}
                    {{-- ========================= --}}
                    <button
                        class="btn btn-secondary"
                        type="submit"
                        data-import-mode="add"
                    >
                        {{ __('admin.import.json.add') }}
                    </button>

                    {{-- ========================= --}}
                    {{-- UPDATE --}}
                    {{-- ========================= --}}
                    <button
                        class="btn btn-secondary"
                        type="submit"
                        data-import-mode="update"
                    >
                        {{ __('admin.import.json.update') }}
                    </button>

                    {{-- ========================= --}}
                    {{-- DELETE --}}
                    {{-- ========================= --}}
                    {{-- TODO:
                         delete mode reserved for future implementation

                         Planned:
                         - delete by keys
                         - safe asset cleanup
                         - translation cleanup
                         - cache cleanup
                    --}}
                    <button
                        class="btn btn-danger"
                        type="button"
                        disabled
                        title="{{ __('admin.import.json.delete_disabled') }}"
                    >
                        {{ __('admin.import.json.delete') }}
                    </button>

                    {{-- ========================= --}}
                    {{-- RULES --}}
                    {{-- ========================= --}}
                    <button
                        type="button"
                        class="btn btn-ghost"
                        data-mb-open="mbImportRulesModal"
                    >
                        {{ __('admin.import.rules') }}
                    </button>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- HINT --}}
            {{-- ========================= --}}
            <div class="mb-muted" style="margin-top:8px;">

                {{ __('admin.import.json.hint') }}

                <br>

                <span data-import-mode-label>
                    {{ __('admin.import.json.current_mode_create') }}
                </span>

            </div>

        </form>

    </div>

    {{-- ========================= --}}
    {{-- IMPORT MODE SWITCHER --}}
    {{-- ========================= --}}
    <script>

        document.addEventListener('DOMContentLoaded', () => {

            const form = document.querySelector(
                'form[action="{{ route('admin.restaurants.menu.import_json', $restaurant) }}"]'
            );

            if (!form) {
                return;
            }

            const modeInput = form.querySelector('[data-import-mode-input]');

            const modeLabel = form.querySelector('[data-import-mode-label]');

            const buttons = form.querySelectorAll('[data-import-mode]');

            if (!modeInput || !buttons.length) {
                return;
            }

            buttons.forEach((btn) => {

                btn.addEventListener('click', () => {

                    const mode = btn.dataset.importMode;

                    modeInput.value = mode;

                    if (!modeLabel) {
                        return;
                    }

                    switch (mode) {

                        case 'create':

                            modeLabel.textContent =
                                "{{ __('admin.import.json.current_mode_create') }}";

                            break;

                        case 'add':

                            modeLabel.textContent =
                                "{{ __('admin.import.json.current_mode_add') }}";

                            break;

                        case 'update':

                            modeLabel.textContent =
                                "{{ __('admin.import.json.current_mode_update') }}";

                            break;
                    }
                });
            });
        });

    </script>

@endif
