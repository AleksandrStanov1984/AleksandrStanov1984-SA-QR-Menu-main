{{-- resources/views/admin/restaurants/index.blade.php --}}

@extends('admin.layout')

@section('title', __('admin.restaurants.index.title'))
@section('subtitle', __('admin.restaurants.index.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.dashboard.home') }}</a>
    <span class="sep">›</span>
    <span>{{ __('admin.restaurants.index.h1') }}</span>
@endsection

@section('content')

    {{-- HEADER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
        <h1 style="margin:0; font-size:18px;">
            {{ __('admin.restaurants.index.h1') }}
        </h1>

        <a class="btn" href="{{ route('admin.restaurants.create') }}">
            {{ __('admin.restaurants.index.add') }}
        </a>
    </div>

    {{-- SEARCH + FILTERS --}}
    <div
        style="
            margin-bottom:12px;
            display:flex;
            flex-direction:column;
            gap:10px;
        "
    >

        {{-- SEARCH --}}
        <div>
            <input
                type="text"
                id="restaurantSearch"
                class="input"
                placeholder="{{ __('admin.restaurants.filters.search') }}"
                style="width:100%; max-width:320px;"
            >
        </div>

        {{-- FILTERS ROW --}}
        <div
            style="
                display:flex;
                gap:10px;
                flex-wrap:wrap;
                align-items:center;
            "
        >

            {{-- ACTIVE --}}
            <div
                class="ui-select ui-select--button"
                style="min-width:200px;"
            >

                <button
                    type="button"
                    class="ui-select-btn"
                >
                    {{ __('admin.restaurants.filters.all_statuses') }}
                </button>

                <div class="ui-select-menu">

                    <div
                        class="ui-select-option active"
                        data-target="restaurantStatusFilter"
                        data-value=""
                    >
                        {{ __('admin.restaurants.filters.all_statuses') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="restaurantStatusFilter"
                        data-value="1"
                    >
                        {{ __('admin.restaurants.filters.active_only') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="restaurantStatusFilter"
                        data-value="0"
                    >
                        {{ __('admin.restaurants.filters.inactive_only') }}
                    </div>

                </div>

                <input
                    type="hidden"
                    id="restaurantStatusFilter"
                    value=""
                >

            </div>

            {{-- BILLING WARNING --}}
            <div
                class="ui-select ui-select--button"
                style="min-width:220px;"
            >

                <button
                    type="button"
                    class="ui-select-btn"
                >
                    {{ __('admin.restaurants.filters.billing_warnings') }}
                </button>

                <div class="ui-select-menu">

                    <div
                        class="ui-select-option active"
                        data-target="billingWarningFilter"
                        data-value=""
                    >
                        {{ __('admin.restaurants.filters.billing_warnings') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="billingWarningFilter"
                        data-value="warning"
                    >
                        {{ __('admin.restaurants.filters.warning_days') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="billingWarningFilter"
                        data-value="danger"
                    >
                        {{ __('admin.restaurants.filters.danger_days') }}
                    </div>

                </div>

                <input
                    type="hidden"
                    id="billingWarningFilter"
                    value=""
                >

            </div>

            {{-- BILLING TYPE --}}
            <div
                class="ui-select ui-select--button"
                style="min-width:220px;"
            >

                <button
                    type="button"
                    class="ui-select-btn"
                >
                    {{ __('admin.restaurants.filters.billing_type') }}
                </button>

                <div class="ui-select-menu">

                    <div
                        class="ui-select-option active"
                        data-target="billingTypeFilter"
                        data-value=""
                    >
                        {{ __('admin.restaurants.filters.billing_type') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="billingTypeFilter"
                        data-value="trial"
                    >
                        {{ __('admin.restaurants.filters.trial') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="billingTypeFilter"
                        data-value="paid"
                    >
                        {{ __('admin.restaurants.filters.paid') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="billingTypeFilter"
                        data-value="expired"
                    >
                        {{ __('admin.restaurants.filters.expired') }}
                    </div>

                    <div
                        class="ui-select-option"
                        data-target="billingTypeFilter"
                        data-value="inactive"
                    >
                        {{ __('admin.restaurants.filters.inactive') }}
                    </div>

                </div>

                <input
                    type="hidden"
                    id="billingTypeFilter"
                    value=""
                >

            </div>

            {{-- PLAN --}}
            <div
                class="ui-select ui-select--button"
                style="min-width:200px;"
            >

                <button
                    type="button"
                    class="ui-select-btn"
                >
                    {{ __('admin.restaurants.filters.all_plans') }}
                </button>

                <div class="ui-select-menu">

                    <div
                        class="ui-select-option active"
                        data-target="planFilter"
                        data-value=""
                    >
                        {{ __('admin.restaurants.filters.all_plans') }}
                    </div>

                    @foreach($plans as $plan)

                        <div
                            class="ui-select-option"
                            data-target="planFilter"
                            data-value="{{ $plan->key }}"
                        >
                            {{ ucfirst($plan->name) }}
                        </div>

                    @endforeach

                </div>

                <input
                    type="hidden"
                    id="planFilter"
                    value=""
                >

            </div>

        </div>

    </div>

    <div class="card" id="restaurantsTable">

        <div class="table-scroll">
            <table class="table">

                <thead>
                <tr>
                    <th>{{ __('admin.fields.name') }}</th>
                    <th>{{ __('admin.fields.template') }}</th>
                    <th>{{ __('admin.fields.languages') }}</th>
                    <th>{{ __('admin.fields.status') }}</th>
                    <th class="right">{{ __('admin.fields.actions') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($restaurants as $r)

                    @php
                        $billingLevel = null;

                        if ($r->is_active) {
                            $billingLevel = $r->billingWarningLevel();
                        }

                        $billingStatus = $r->billingStatus();
                    @endphp

                    <tr
                        data-billing-warning="{{ $billingLevel }}"
                        data-billing-status="{{ $billingStatus }}"
                        data-plan="{{ $r->plan_key }}"
                        data-active="{{ $r->is_active ? '1' : '0' }}"
                        data-id="{{ $r->id }}"
                        data-name="{{ strtolower(e($r->name)) }}"
                        data-slug="{{ strtolower(e($r->slug)) }}"
                    >

                        {{-- NAME --}}
                        <td
                            data-label="{{ __('admin.fields.name') }}"
                            class="
                                @if($billingLevel === 'warning') billing-warning
                                @elseif($billingLevel === 'danger') billing-danger
                                @endif
                            "
                        >
                            <div class="restaurant-name js-name">

                                {{ $r->name }}

                                <div class="restaurant-sub js-slug">
                                    #{{ $r->id }} · {{ $r->slug }}
                                </div>

                                {{-- BILLING BADGES --}}
                                <div
                                    style="
                                        display:flex;
                                        gap:6px;
                                        flex-wrap:wrap;
                                        margin-top:6px;
                                    "
                                >

                                    {{-- PLAN --}}
                                    <span class="pill small">
                                        {{ strtoupper($r->plan_key) }}
                                    </span>

                                    {{-- BILLING --}}
                                    @if($billingStatus === 'trial')

                                        <span class="pill small warning">
                                            {{ __('admin.billing.trial') }}
                                        </span>

                                    @elseif($billingStatus === 'paid')

                                        <span class="pill small success">
                                            {{ __('admin.billing.paid') }}
                                        </span>

                                    @elseif($billingStatus === 'expired')

                                        <span class="pill small danger">
                                            {{ __('admin.billing.expired') }}
                                        </span>

                                    @elseif($billingStatus === 'inactive')

                                        <span class="pill small">
                                            {{ __('admin.billing.inactive') }}
                                        </span>

                                    @endif

                                    {{-- DAYS LEFT --}}
                                    @if($r->is_active && $r->billingDaysLeft() !== null)

                                        <span class="pill small">
                                            {{ __('admin.billing.days_left', [
                                                'days' => $r->billingDaysLeft()
                                            ]) }}
                                        </span>

                                    @endif

                                </div>

                            </div>
                        </td>

                        {{-- TEMPLATE --}}
                        <td data-label="{{ __('admin.fields.template') }}">
                            <span class="pill">
                                {{ __('admin.templates.'.$r->template_key) }}
                            </span>
                        </td>

                        {{-- LANGUAGES --}}
                        <td data-label="{{ __('admin.fields.languages') }}" class="mut">

                            {{ implode(', ', $r->enabled_locales ?: ['de']) }}

                            <span class="pill small">
                                {{ $r->default_locale ?: 'de' }}
                            </span>

                        </td>

                        {{-- STATUS --}}
                        <td data-label="{{ __('admin.fields.status') }}">

                            <span class="status">

                                <span class="status-dot {{ $r->is_active ? 'on' : 'off' }}"></span>

                                {{ $r->is_active
                                    ? __('admin.status.active')
                                    : __('admin.status.inactive')
                                }}

                            </span>

                        </td>

                        {{-- ACTIONS --}}
                        <td
                            class="right actions-desktop"
                            data-label="{{ __('admin.fields.actions') }}"
                        >

                            <div class="actions-inline">

                                <a
                                    class="btn small"
                                    href="{{ route('admin.restaurants.edit', $r) }}"
                                >
                                    {{ __('admin.actions.edit') }}
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.restaurants.toggle', $r) }}"
                                    class="toggle-form"
                                >
                                    @csrf

                                    <label class="switch">

                                        <input
                                            type="checkbox"
                                            {{ $r->is_active ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                        >

                                        <span class="slider"></span>

                                    </label>

                                </form>

                            </div>

                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

         @if($restaurants->lastPage() > 1)

             <div
                 style="
                     margin-top:20px;
                     display:flex;
                     gap:8px;
                     justify-content:center;
                     align-items:center;
                     flex-wrap:wrap;
                 "
             >

                 {{-- PREV --}}
                 @if($restaurants->onFirstPage())

                     <span
                         class="btn small"
                         style="
                             opacity:.4;
                             pointer-events:none;
                         "
                     >
                         ←
                     </span>

                 @else

                     <a
                         class="btn small"
                         href="{{ $restaurants->previousPageUrl() }}"
                     >
                         ←
                     </a>

                 @endif

                 {{-- PAGES --}}
                 @for($i = 1; $i <= $restaurants->lastPage(); $i++)

                     @if($i === $restaurants->currentPage())

                         <span
                             class="btn small"
                             style="
                                 background:#2563eb;
                                 border-color:#2563eb;
                                 color:#fff;
                                 font-weight:700;
                                 transform:scale(1.06);
                                 pointer-events:none;
                             "
                         >
                             {{ $i }}
                         </span>

                     @else

                         <a
                             class="btn small"
                             href="{{ $restaurants->url($i) }}"
                         >
                             {{ $i }}
                         </a>

                     @endif

                 @endfor

                 {{-- NEXT --}}
                 @if($restaurants->hasMorePages())

                     <a
                         class="btn small"
                         href="{{ $restaurants->nextPageUrl() }}"
                     >
                         →
                     </a>

                 @else

                     <span
                         class="btn small"
                         style="
                             opacity:.4;
                             pointer-events:none;
                         "
                     >
                         →
                     </span>

                 @endif

             </div>

         @endif

    </div>

@endsection
