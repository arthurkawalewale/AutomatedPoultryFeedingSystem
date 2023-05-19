<div wire:poll.100ms>

    <header>
        <h5>Water levels <button id="Rec" class="mx-2">Recording</button></h5>

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
