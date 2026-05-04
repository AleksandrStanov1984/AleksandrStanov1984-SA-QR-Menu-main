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
                    </label>
                </span>

                </div>

                {{-- ACTIONS --}}
                <div class="mb-import-right">
                    <button class="btn btn-primary" type="submit">
                        {{ __('admin.import.json.upload') }}
                    </button>

                    <button type="button"
                            class="btn btn-ghost"
                            data-mb-open="mbImportRulesModal">
                        {{ __('admin.import.rules') }}
                    </button>
                </div>

            </div>

            <div class="mb-muted" style="margin-top:8px;">
                {{ __('admin.import.json.hint') }}
            </div>

        </form>

    </div>

@endif
