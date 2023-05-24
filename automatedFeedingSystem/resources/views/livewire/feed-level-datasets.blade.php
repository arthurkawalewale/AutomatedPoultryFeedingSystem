<div wire:poll.1s>

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
