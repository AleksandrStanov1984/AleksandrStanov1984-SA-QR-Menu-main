@php
    $status = $restaurant->billingStatus();
    $warning = $restaurant->billingWarningLevel();
    $daysLeft = $restaurant->billingDaysLeft();
@endphp

<div class="billing-page">

    {{-- ================= HEADER ================= --}}
    <div class="billing-header-card">

        <div class="billing-header-left">

            <h1>{{ __('billing.title') }}</h1>

            <div class="billing-status-row">

                <span class="billing-badge billing-status-{{ $status }}">
                    {{ __('billing.status.' . $status) }}
                </span>

                @if($restaurant->plan_key)
                    <span class="billing-plan">
                        {{ strtoupper($restaurant->plan_key) }}
                    </span>
                @endif

            </div>

        </div>

        <div class="billing-header-right">

            <div class="billing-meta">
                <span>{{ __('billing.fields.paid_until') }}</span>

                <strong>
                    {{ $restaurant->activeUntil()?->format('d.m.Y') ?? '—' }}
                </strong>
            </div>

            <div class="billing-meta">
                <span>{{ __('billing.fields.days_left') }}</span>

                <strong>
                    {{ $daysLeft ?? '—' }}
                </strong>
            </div>

            <div class="billing-meta">
                <span>{{ __('billing.fields.monthly_price') }}</span>

                <strong>

                    @if($restaurant->plan?->price)

                        {{ number_format($restaurant->plan->price, 2) }} €

                    @else
                        —
                    @endif

                </strong>
            </div>

        </div>

    </div>


    {{-- ================= WARNING ================= --}}
    @if($warning && $warning !== 'ok')

        <div class="billing-warning billing-warning-{{ $warning }}">
            {{ __('billing.warnings.' . $warning) }}
        </div>

    @endif


    {{-- ================= USER ACTIONS ================= --}}
    <div class="billing-actions-card">

        {{-- DEACTIVATE --}}
        <form method="POST"
              action="{{ route('admin.restaurants.billing.deactivate', $restaurant) }}">
            @csrf

            <button type="submit"
                    class="billing-btn billing-btn-danger"
                @disabled(!$restaurant->is_active)>

                ⏸ {{ __('billing.actions.deactivate') }}

            </button>
        </form>


        {{-- KEEP DATA --}}
        <form method="POST"
              action="{{ route('admin.restaurants.billing.keep_data', $restaurant) }}">

            @csrf

            <label class="billing-toggle">

                <input type="checkbox"
                       name="keep_data"
                       value="1"
                       onchange="this.form.submit()"
                    @checked($restaurant->keep_data)>

                <span class="billing-toggle-slider"></span>

                <span>
                    {{ __('billing.fields.keep_data') }}
                </span>

            </label>

        </form>

    </div>


    {{-- ================= SUPER ADMIN ACTIONS ================= --}}
    @if(auth()->user()?->is_super_admin)

        <div class="billing-actions-card">

            {{-- RESUME --}}
            @if(!$restaurant->is_active)

                <form method="POST"
                      action="{{ route('admin.restaurants.billing.resume', $restaurant) }}">
                    @csrf

                    <button type="submit"
                            class="billing-btn billing-btn-success">

                        ▶ {{ __('billing.actions.resume') }}

                    </button>
                </form>

            @endif


            {{-- CONFIRM PAYMENT --}}
            <form method="POST"
                  action="{{ route('admin.restaurants.billing.confirm', $restaurant) }}">
                @csrf

                <button type="submit"
                        class="billing-btn billing-btn-success">

                    {{ __('billing.actions.confirm_payment') }}

                </button>
            </form>


            {{-- START TRIAL --}}
            <form method="POST"
                  action="{{ route('admin.restaurants.billing.trial', $restaurant) }}">
                @csrf

                <button type="submit"
                        class="billing-btn billing-btn-info">

                    {{ __('billing.actions.start_trial') }}

                </button>
            </form>


            {{-- EXTEND TRIAL --}}
            <form method="POST"
                  action="{{ route('admin.restaurants.billing.extend_trial', $restaurant) }}">
                @csrf

                <button type="submit"
                        class="billing-btn billing-btn-warning">

                    {{ __('billing.actions.extend_trial') }}

                </button>
            </form>

        </div>

    @endif


<label>
            {{ __('billing.filters.sort') }}
        </label>
    {{-- ================= FILTERS ================= --}}
    <form method="GET" class="billing-filters">

        <div class="billing-filter-group">

            <label>
                {{ __('billing.filters.date_from') }}
            </label>

            <input type="date"
                   name="date_from"
                   value="{{ request('date_from') }}">

        </div>

        <div class="billing-filter-group">

            <label>
                {{ __('billing.filters.date_to') }}
            </label>

            <input type="date"
                   name="date_to"
                   value="{{ request('date_to') }}">

        </div>

        <div class="billing-filter-group">

            <select name="sort">

                <option value="newest"
                    @selected(request('sort', 'newest') === 'newest')>

                    {{ __('billing.filters.newest') }}

                </option>

                <option value="oldest"
                    @selected(request('sort') === 'oldest')>

                    {{ __('billing.filters.oldest') }}

                </option>

            </select>

        </div>

        <button class="billing-btn billing-btn-dark">
            {{ __('billing.filters.apply') }}
        </button>

    </form>


    {{-- ================= TABLE ================= --}}
    <div class="billing-table-wrap">

        <table class="billing-table">

            <thead>
            <tr>
                <th>{{ __('billing.table.date') }}</th>
                <th>{{ __('billing.table.type') }}</th>
                <th>{{ __('billing.table.status') }}</th>
                <th>{{ __('billing.table.period') }}</th>
                <th>{{ __('billing.table.amount') }}</th>
                <th>{{ __('billing.table.user') }}</th>
                <th>{{ __('billing.table.notes') }}</th>
            </tr>
            </thead>

            <tbody>

            @forelse($records as $record)

                <tr>

                    <td>
                        {{ $record->created_at?->format('d.m.Y H:i') }}
                    </td>

                    <td>

                        <span class="billing-badge billing-type-{{ $record->type }}">

                            {{ ucfirst(str_replace('_', ' ', $record->type)) }}

                        </span>

                    </td>

                    <td>

                        <span class="billing-badge billing-record-status-{{ $record->status }}">

                            {{ ucfirst($record->status) }}

                        </span>

                    </td>

                    <td>

                        @if($record->period_from && $record->period_to)

                            {{ $record->period_from->format('d.m.Y') }}
                            —
                            {{ $record->period_to->format('d.m.Y') }}

                        @else
                            —
                        @endif

                    </td>

                    <td>

                        @if($record->amount)

                            {{ number_format($record->amount, 2) }}
                            {{ $record->currency }}

                        @else
                            —
                        @endif

                    </td>

                    <td>
                        {{ $record->confirmedBy?->name ?? 'System' }}
                    </td>

                    <td class="billing-notes">
                        {{ $record->notes ?: '—' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="billing-empty">
                        {{ __('billing.table.empty') }}
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- ================= PAGINATION ================= --}}
    @if($records->lastPage() > 1)

        <div class="pagination">

            {{-- PREV --}}
            @if($records->onFirstPage())

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
                    href="{{ $records->previousPageUrl() }}"
                >
                    ←
                </a>

            @endif


            {{-- PAGES --}}
            @for($i = 1; $i <= $records->lastPage(); $i++)

                @if($i === $records->currentPage())

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
                        href="{{ $records->url($i) }}"
                    >
                        {{ $i }}
                    </a>

                @endif

            @endfor


            {{-- NEXT --}}
            @if($records->hasMorePages())

                <a
                    class="btn small"
                    href="{{ $records->nextPageUrl() }}"
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
