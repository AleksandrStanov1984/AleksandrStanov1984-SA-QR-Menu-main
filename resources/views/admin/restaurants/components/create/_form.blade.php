<div class="card">
    <h2>{{ __('admin.restaurants.create.h2') }}</h2>

    <form method="POST" action="{{ route('admin.restaurants.store') }}">
        @csrf

        <div class="grid">
            @include('admin.restaurants.components.create._restaurant-fields')
            @include('admin.restaurants.components.create._owner-fields')
        </div>

        <div style="margin-top:16px">
            <button class="btn ok">{{ __('admin.actions.create_restaurant') }}</button>
            <a href="{{ route('admin.restaurants.index') }}" class="btn secondary">
                {{ __('admin.actions.cancel') }}
            </a>
        </div>
    </form>
</div>
