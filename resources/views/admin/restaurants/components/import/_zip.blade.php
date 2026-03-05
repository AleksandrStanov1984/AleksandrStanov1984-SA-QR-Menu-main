@php
  use App\Support\Permissions;
@endphp

@if(Permissions::can(auth()->user(), 'import.images_zip'))
<details class="mb-import-acc" style="margin:0;">
  <summary class="mb-acc-summary" style="list-style:none;" onclick="return true;">
    <div class="mb-acc-head mb-acc-head-full">
      <div class="mb-acc-left">
        <span class="mb-acc-title">{{ __('admin.import.zip.title') }}</span>
      </div>
      <div class="mb-acc-right">
        <div class="mb-acc-caret"></div>
      </div>
    </div>
  </summary>

  <div class="mb-acc-body">
    <form method="POST"
          action="{{ route('admin.restaurants.menu.import_zip', $restaurant) }}"
          enctype="multipart/form-data"
          class="mb-import-form">
      @csrf

      <div class="mb-import-row">
        <div class="mb-import-left">
          <input type="file"
                 name="assets_zip"
                 accept=".zip,application/zip"
                 required>
        </div>

        <div class="mb-import-right">
          <button class="btn btn-primary" type="submit">
            {{ __('admin.import.zip.upload') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</details>
@endif
