{{-- resources/views/admin/layout/breadcrumbs.blade.php --}}
@if(!empty($breadcrumbs))
    <div class="crumbs-wrap">
        <div class="crumbs">
            @foreach($breadcrumbs as $i => $crumb)

                @if($i > 0)
                    <span class="sep">›</span>
                @endif

                @if(!empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}">
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span>{{ $crumb['label'] }}</span>
                @endif

            @endforeach
        </div>
    </div>
@endif
