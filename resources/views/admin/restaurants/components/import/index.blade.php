<div class="card mb-import-card">
    <div class="card__head">
        <div class="card__title">
            {{ __('admin.import.title') }}
        </div>
    </div>

    <div class="card__body">
        @include('admin.restaurants.components.import._json')
        @include('admin.restaurants.components.import._zip')
        @include('admin.restaurants.components.import._log')
    </div>

    @include('admin.restaurants.components.import._rules-modal')
</div>

@include('admin.restaurants.components.import._styles')
@include('admin.restaurants.components.import._scripts')

