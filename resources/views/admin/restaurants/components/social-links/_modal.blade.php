@php
  // $canIcon передан из index
@endphp

<div id="slModal" class="modal" aria-hidden="true" style="display:none;">
  <div class="modal__backdrop" data-modal-close></div>

  <div class="modal__panel" role="dialog" aria-modal="true">
    <button type="button" class="modal-close" data-modal-close aria-label="Close"></button>

    <div class="modal__head" style="display:flex; align-items:center; gap:10px; padding-right:44px;">
      <div style="font-weight:800; font-size:16px;" id="slModalTitle">
        {{ __('admin.socials.add') }}
      </div>
    </div>

    <form id="slForm"
          method="POST"
          enctype="multipart/form-data"
          action="{{ route('admin.restaurants.social_links.store', $restaurant) }}">
      @csrf
      <input type="hidden" name="_method" value="POST" id="slMethod">
      <input type="hidden" name="remove_icon" value="0" id="slRemoveIcon">

      <div class="modal__body" style="margin-top:12px;">
        <div class="grid">
          <div class="col6">
            <label>{{ __('admin.socials.fields.title') }}</label>
            <input name="title"
                   id="slTitle"
                   placeholder="{{ __('admin.socials.placeholders.title') }}"
                   required
                   maxlength="120">
          </div>

          <div class="col6">
            <label>{{ __('admin.socials.fields.url') }}</label>
            <input name="url"
                   id="slUrl"
                   placeholder="{{ __('admin.socials.placeholders.url') }}"
                   required
                   maxlength="2048">
            <div class="mut" style="font-size:12px; margin-top:6px;">
              {{ __('admin.socials.url_example') }}
            </div>
          </div>
        </div>

        <div style="margin-top:12px;">
          <label style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <span>{{ __('admin.socials.fields.icon') }}</span>
            @if(!$canIcon)
              <span class="mut" style="font-size:12px;">(no permission)</span>
            @endif
          </label>

          <div style="display:flex; gap:12px; align-items:flex-start; flex-wrap:wrap; margin-top:8px;">
            <div class="sl-icon-box" style="width:84px; height:84px;">
              <img id="slIconPreview"
                   src=""
                   alt="icon"
                   style="width:100%; height:100%; object-fit:contain; display:none;">
              <div id="slIconEmpty" class="mut" style="font-size:12px; text-align:center; padding:10px;">
                SVG
              </div>
            </div>

            <div style="flex:1 1 auto; min-width:240px;">
              <input type="file"
                     name="icon"
                     id="slIconFile"
                     accept=".svg,image/svg+xml"
                     {{ $canIcon ? '' : 'disabled' }}>

              <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button"
                        class="btn small secondary"
                        id="slRemoveIconBtn"
                        style="display:none;">
                  {{ __('admin.common.remove') }}
                </button>
              </div>

              <div class="mut" style="font-size:12px; margin-top:8px;">
                SVG only, max 256KB.
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal__foot"
           style="display:flex; align-items:center; justify-content:flex-end; gap:10px; margin-top:14px;">
        <button type="button" class="btn secondary" data-modal-close>
          {{ __('admin.socials.cancel') }}
        </button>
        <button type="submit" class="btn ok">
          {{ __('admin.socials.save') }}
        </button>
      </div>
    </form>
  </div>
</div>
