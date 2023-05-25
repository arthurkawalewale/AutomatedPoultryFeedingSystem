<div wire:poll.1s>
    <header>
        <h6 class="text-center">Feed Levels</h6>
    </header>

    <div wire:ignore>
        <div wire:key={{ $chart?->id }}>
            @if($chart)
                {!! $chart->container() !!}
            @endif
        </div>
    </div>
</div>

@if($chart)
    @push('scripts')
        {!! $chart->script() !!}
    @endpush
@endif
