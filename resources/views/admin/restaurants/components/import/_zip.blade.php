{{-- resources/views/admin/restaurants/components/import/_zip.blade.php --}}

@php
    use App\Support\Permissions;
@endphp

@if(Permissions::can(auth()->user(), 'import.images_zip'))

    <div class="mb-import-block">

        <div class="mb-import-header">
        <span class="mb-import-title">
            {{ __('admin.import.zip.title') }}
        </span>
        </div>

        <form method="POST"
              action="{{ route('admin.restaurants.menu.import_zip', $restaurant) }}"
              enctype="multipart/form-data">
            @csrf

            <div class="mb-import-row">

                {{-- FILE --}}
                <div class="mb-import-left">

                    <label class="branding-file-btn">
                        {{ __('admin.common.choose_file') }}

                        <input
                            type="file"
                            name="assets_zip"
                            class="branding-file-input"
                            data-import-input="zip"
                            accept=".zip,application/zip"
                            required
                        >
                        <div id="zip-file-name" class="mb-file-name hidden"></div>
                    </label>

                </span>

                </div>

                {{-- ACTION --}}
                <div class="mb-import-right">
                    <button class="btn btn-primary" type="submit">
                        {{ __('admin.import.zip.upload') }}
                    </button>
                </div>

            </div>

            <div class="mb-muted" style="margin-top:8px;">
                {{ __('admin.import.zip.hint') }}
            </div>

        </form>

        <div id="import-status-block" class="mb-import-result hidden">

            <button type="button" class="mb-import-close" id="import-close-btn">
                ×
            </button>

            <div class="mb-import-loader">
                <span class="mb-spinner"></span>
                <span id="import-status-text">
            {{ __('admin.import.status.checking') }}
        </span>
            </div>

        </div>

    </div>

@endif
