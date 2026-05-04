{{-- resources/views/admin/restaurants/components/import/_rules-modal.blade.php --}}

<div class="modal" id="mbImportRulesModal" aria-hidden="true">
    <div class="modal__overlay" data-mb-close></div>

    <div class="modal__panel" role="dialog" aria-modal="true" aria-labelledby="mbImportRulesTitle">
        <button type="button" class="modal-close" data-mb-close aria-label="Close"></button>

        <h3 id="mbImportRulesTitle" style="margin:0 0 10px 0;">
            {{ __('admin.import.rules_modal.title') }}
        </h3>

        <p class="mb-muted">{{ __('admin.import.rules_modal.intro') }}</p>

        {{-- PATCH --}}
        <h4 class="mb-h4">{{ __('admin.import.rules_modal.patch_title') }}</h4>
        <p class="mb-muted">{{ __('admin.import.rules_modal.patch_desc') }}</p>

        <pre class="mb-code"><code id="mbPatchExample">
{{ __('admin.import.rules_modal.patch_example') }}
    </code></pre>

        {{-- ASSETS --}}
        <h4 class="mb-h4">{{ __('admin.import.rules_modal.assets_title') }}</h4>
        <p class="mb-muted">{{ __('admin.import.rules_modal.assets_desc') }}</p>

        <pre class="mb-code"><code id="mbAssetsExample">
{{ __('admin.import.rules_modal.assets_example') }}
    </code></pre>

        {{-- EXPORT LINK --}}
        <h4 class="mb-h4">{{ __('admin.import.rules_modal.export_title') }}</h4>
        <p class="mb-muted">{{ __('admin.import.rules_modal.export_desc') }}</p>

        <div class="mb-import-export-link">
            <a href="{{ route('admin.restaurants.menu.export_json', $restaurant) }}"
               class="btn btn-primary">
                JSON
            </a>
        </div>

        {{-- NOTES --}}
        <h4 class="mb-h4">{{ __('admin.import.rules_modal.notes_title') }}</h4>
        <ul class="mb-list">
            <li>{{ __('admin.import.rules_modal.note_atomic') }}</li>
            <li>{{ __('admin.import.rules_modal.note_permissions') }}</li>
            <li>{{ __('admin.import.rules_modal.note_paths') }}</li>
        </ul>

        {{-- ACTIONS --}}
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px;">
            <button type="button" class="btn btn-ghost" data-mb-close>
                {{ __('admin.common.close') }}
            </button>
        </div>
    </div>
</div>
