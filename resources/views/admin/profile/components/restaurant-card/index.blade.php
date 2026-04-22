{{-- resources/views/admin/profile/components/restaurant-card/index.blade.php --}}

@php
    $profileMode = $profileMode ?? 'self';
    $isSuper = auth()->user()?->isSuperAdmin();
@endphp

<div class="card" style="margin-top:16px;">

    {{-- USER BLOCK --}}
    <h2>
        @if($profileMode === 'restaurant')
            {{ __('profile.user.restaurant') }}
        @elseif($isSuper)
            {{ __('profile.user.admin') }}
        @else
            {{ __('profile.user.default') }}
        @endif
    </h2>

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        <div class="grid">
            <div class="col6">
                <label>{{ __('profile.fields.name') }}</label>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="col6">
                <label>{{ __('profile.fields.email') }}</label>
                <input value="{{ $user->email }}" disabled>
            </div>
        </div>

        <div style="margin-top:16px;">
            <button class="btn ok" type="submit">
                {{ __('profile.actions.save') }}
            </button>
        </div>
    </form>

</div>

@if($restaurant)
    <div class="card" style="margin-top:16px;">
        <h2>{{ __('profile.restaurant.title') }}</h2>

        <div class="sidebar-divider"></div>

        <form method="POST" action="{{ route('admin.restaurants.profile.update', $restaurant) }}">
            @csrf

            <div class="grid">
                <div class="col6">
                    <label>{{ __('profile.restaurant.name') }}</label>
                    <input name="restaurant_name"
                           value="{{ old('restaurant_name', $restaurant->name) }}"
                           required>
                </div>

                <div class="col6">
                    <label>{{ __('profile.restaurant.contact_name') }}</label>
                    <input name="contact_name"
                           value="{{ old('contact_name', $restaurant->contact_name) }}">
                </div>
            </div>

            @php
                $template = old('template_key', $restaurant->template_key ?? 'classic');
                $planKey = old('plan_key', $restaurant->plan_key ?? 'starter');
            @endphp

            <div class="grid">

                <div class="col6">
                    <label>{{ __('profile.restaurant.template') }}</label>

                    @if($isSuper)
                        <div class="ui-select ui-select--button" data-name="template_key">

                            <button type="button" class="ui-select-btn">
                                {{ $templates->firstWhere('key', $template)->name ?? 'Select' }}
                            </button>

                            <div class="ui-select-menu">
                                @foreach($templates as $tplItem)
                                    <div class="ui-select-option {{ $template === $tplItem->key ? 'active' : '' }}"
                                         data-value="{{ $tplItem->key }}">
                                        {{ $tplItem->name }}
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="template_key" value="{{ $template }}">
                        </div>
                    @else
                        <input value="{{ __('admin.templates.' . ($restaurant->template_key ?? 'classic')) }}" disabled>
                    @endif
                </div>

                <div class="col6">
                    <label>{{ __('profile.restaurant.plan') }}</label>

                    @if($isSuper)
                        <div class="ui-select ui-select--button" data-name="plan_key">

                            <button type="button" class="ui-select-btn">
                                {{ $plans->firstWhere('key', $planKey)->name ?? 'Select' }}
                            </button>

                            <div class="ui-select-menu">
                                @foreach($plans as $plan)
                                    <div class="ui-select-option {{ $planKey === $plan->key ? 'active' : '' }}"
                                         data-value="{{ $plan->key }}">
                                        {{ $plan->name }} (€{{ $plan->price }})
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="plan_key" value="{{ $planKey }}">
                        </div>
                    @else
                        <input value="{{ __('admin.plans.' . ($restaurant->plan_key ?? 'starter')) }}" disabled>
                    @endif
                </div>

            </div>

            <div class="grid">
                <div class="col6">
                    <label>{{ __('profile.restaurant.phone') }}</label>
                    <input name="phone" value="{{ old('phone', $restaurant->phone) }}">
                </div>

                <div class="col6">
                    <label>{{ __('profile.restaurant.email') }}</label>
                    <input name="contact_email" type="email" value="{{ old('contact_email', $restaurant->contact_email) }}">
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <h3 style="margin:14px 0 8px; font-size:14px;">
                {{ __('profile.restaurant.address_title') }}
            </h3>

            <div class="grid">
                <div class="col6">
                    <label>{{ __('profile.restaurant.city') }}</label>
                    <input name="city" value="{{ old('city', $restaurant->city) }}">
                </div>

                <div class="col6">
                    <label>{{ __('profile.restaurant.postal_code') }}</label>
                    <input name="postal_code" value="{{ old('postal_code', $restaurant->postal_code) }}">
                </div>
            </div>

            <div class="grid">
                <div class="col6">
                    <label>{{ __('profile.restaurant.street') }}</label>
                    <input name="street" value="{{ old('street', $restaurant->street) }}">
                </div>

                <div class="col6">
                    <label>{{ __('profile.restaurant.house_number') }}</label>
                    <input name="house_number" value="{{ old('house_number', $restaurant->house_number) }}">
                </div>
            </div>

            <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn ok" type="submit">
                    {{ __('profile.actions.save') }}
                </button>
            </div>
        </form>
    </div>
@endif
