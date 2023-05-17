<div wire:poll.10s>

    <header>
        <h2>WAN speed tests <small>Past 24hours</small></h2>
    </header>

    <div wire:ignore wire:key={{ $chart->id }}>
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
