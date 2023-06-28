<div>

    <header>
        <h6 class="text-center">Water Levels</h6>
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
