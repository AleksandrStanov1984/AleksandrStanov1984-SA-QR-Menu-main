{{-- resources/views/admin/restaurants/components/import/_log.blade.php --}}

@php
    $errors    = session('import_log_errors', []);
    $logToken  = session('import_log_token');
    $status    = session('import_status');

    $hasErrors = $status === 'error' && !empty($errors);
    $hasSuccess = $status === 'ok';
@endphp

<div class="mb-import-log">

    <button type="button"
            class="btn
                {{ $hasErrors ? 'btn-danger' : '' }}
                {{ $hasSuccess ? 'btn-success' : '' }}
                {{ (!$hasErrors && !$hasSuccess) ? 'btn-outline' : '' }}"
            {{ !$logToken ? 'disabled' : '' }}
            onclick="window.mbToggleImportLog()">

        {{ __('admin.import.log.btn') }}
    </button>

    <div class="mb-log-panel" hidden>
        <h4>{{ __('admin.import.log.title') }}</h4>

        @if($hasErrors)
            <ul class="mb-log-errors">
                @foreach($errors as $e)
                    <li>
                        <code>{{ $e['path'] ?? '-' }}</code> —
                        {{ __($e['message_key'] ?? 'admin.import.errors.unknown', $e['params'] ?? []) }}
                    </li>
                @endforeach
            </ul>

            @if($logToken)
                <a class="btn btn-outline"
                   href="{{ route('admin.restaurants.menu.import_log', [$restaurant, $logToken]) }}">
                    {{ __('admin.import.log.download') }}
                </a>
            @endif

        @elseif($hasSuccess)
            <p>{{ __('admin.import.log.ok') }}</p>
        @else
            <p class="mb-muted">
                {{ __('admin.import.log.empty') }}
            </p>
        @endif
    </div>

</div>
