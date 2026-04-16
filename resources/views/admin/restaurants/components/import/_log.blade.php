{{-- resources/views/admin/restaurants/components/import/_log.blade.php --}}
{{-- admin/restaurants/components/import/_log --}}
@php
    $hasErrors = session('import_status') === 'error';
    $logToken  = session('import_log_token');
    $errors    = session('import_log_errors', []);
@endphp

<div class="mb-import-log">

    <button type="button"
            class="btn {{ $hasErrors ? 'btn-danger' : 'btn-success' }}"
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
                        <code>{{ $e['path'] }}</code> —
                        {{ __($e['message_key'], $e['params'] ?? []) }}
                    </li>
                @endforeach
            </ul>

            @if($logToken)
                <a class="btn btn-outline"
                   href="{{ route('admin.restaurants.menu.import_log', [$restaurant, $logToken]) }}">
                    {{ __('admin.import.log.download') }}
                </a>
            @endif
        @else
            <p>{{ __('admin.import.log.ok') }}</p>
        @endif
    </div>

</div>
