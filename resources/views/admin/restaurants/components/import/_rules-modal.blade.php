{{-- resources/views/admin/restaurants/components/import/_rules-modal.blade.php --}}
{{-- admin/restaurants/components/import/_rules-modal --}}
<div class="modal" id="mbImportRulesModal" aria-hidden="true">
  <div class="modal__overlay" data-mb-close></div>

  <div class="modal__panel" role="dialog" aria-modal="true" aria-labelledby="mbImportRulesTitle">
    <button type="button" class="modal-close" data-mb-close aria-label="Close"></button>

    <h3 id="mbImportRulesTitle" style="margin:0 0 10px 0;">
      {{ __('admin.import.rules_modal.title') }}
    </h3>

    <p class="mb-muted">{{ __('admin.import.rules_modal.intro') }}</p>

    <h4 class="mb-h4">{{ __('admin.import.rules_modal.patch_title') }}</h4>
    <p class="mb-muted">{{ __('admin.import.rules_modal.patch_desc') }}</p>
    <pre class="mb-code"><code>{{ __('admin.import.rules_modal.patch_example') }}</code></pre>

    <h4 class="mb-h4">{{ __('admin.import.rules_modal.assets_title') }}</h4>
    <p class="mb-muted">{{ __('admin.import.rules_modal.assets_desc') }}</p>
    <pre class="mb-code"><code>{{ __('admin.import.rules_modal.assets_example') }}</code></pre>

    <h4 class="mb-h4">{{ __('admin.import.rules_modal.notes_title') }}</h4>
    <ul class="mb-list">
      <li>{{ __('admin.import.rules_modal.note_atomic') }}</li>
      <li>{{ __('admin.import.rules_modal.note_permissions') }}</li>
      <li>{{ __('admin.import.rules_modal.note_paths') }}</li>
    </ul>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px;">
      <button type="button" class="btn btn-ghost" data-mb-close>{{ __('admin.common.close') }}</button>
      <button type="button" class="btn btn-outline js-copy-import-rules">{{ __('admin.import.rules_modal.copy') }}</button>
    </div>
  </div>
</div>
