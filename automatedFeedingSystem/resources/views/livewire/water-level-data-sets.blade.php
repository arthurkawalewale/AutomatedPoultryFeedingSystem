<div wire:poll.10s>

    <header>
        <h5>Water levels</h5>
    </header>

    <div wire:ignore wire:key={{ $chart_id }}>
        @if($chart)
            {!! $chart->container() !!}
        @endif
    </div>

</div>

@if($chart)
    @push('scripts')
        {!! $chart->script() !!}
    @endpush
@endif
