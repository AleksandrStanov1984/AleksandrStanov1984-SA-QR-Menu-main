{{-- resources/views/public/templates/united/blocks/modal/item-modal.blade.php --}}


<div id="itemModal" class="modal" aria-hidden="true">

    <div class="modal-box" role="dialog" aria-modal="true">

        <div class="modal-head"></div>

        <div class="modal-body">

            <img class="modal-image" src="" alt="" style="display:none">
            <button class="modal-back-btn" type="button" data-close-modal style="display:none">
                {{ __('public.back_to_menu', [], app()->getLocale()) }}
            </button>

            <div class="modal-badges" style="display:none"></div>

            <h2 class="modal-title"></h2>

            <p class="modal-description" style="display:none"></p>

            <div class="modal-details" style="display:none"></div>

            <div class="modal-spicy" style="display:none"></div>

            <div class="modal-price" style="display:none"></div>

        </div>

    </div>

</div>
