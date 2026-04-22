{{-- resources/views/admin/restaurants/components/social-links/_modal.blade.php --}}

@php
    $limit = (int) $restaurant->feature('social_limit', 1);
@endphp

<div id="slModal" class="modal" aria-hidden="true" style="display:none;">
    <div class="modal__backdrop" data-modal-close></div>

    <div class="modal__panel" role="dialog" aria-modal="true">
        <button type="button" class="modal-close" data-modal-close></button>

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

                <div class="mut" style="font-size:12px; margin-bottom:10px;">
                    {{ __('admin.socials.limit_info', ['limit' => $limit]) }}
                </div>

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

                <div class="sidebar-divider"></div>
                {{-- ICON --}}
                <div style="margin-top:12px;">
                    <label>{{ __('admin.socials.fields.icon') }}</label>

                    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:8px;">

                        {{-- PREVIEW --}}
                        <div class="sl-icon-preview-wrapper">

                            <img id="slIconPreview"
                                 class="sl-icon-preview"
                                 src="">

                            <div id="slIconEmpty" class="sl-icon-empty">
                                SVG
                            </div>

                        </div>

                        {{-- UPLOAD --}}
                        <div style="flex:1 1 auto; min-width:240px;">

                            <label class="sl-file-btn">
                                {{ __('admin.common.choose_file')}}

                                <input type="file"
                                       name="icon"
                                       id="slIconFile"
                                       accept=".svg,image/svg+xml"
                                       hidden>
                            </label>

                            <div class="mut" style="font-size:12px; margin-top:8px;">
                                SVG, max 256KB.
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <div class="modal__foot" style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px;">
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
