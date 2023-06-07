<div>
    <header>
        <h6 class="text-center">Water Report</h6>
    </header>

    <div wire:ignore>
        <div wire:key={{ $chart?->id }}>
            @if($chart)
                {!! $chart->container() !!}
            @endif
        </div>
    </div>

    <div>
        <label for="interval">Select Interval:</label>
        <select wire:model="interval" id="interval">
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>
</div>

@if($chart)
    @push('scripts')
        {!! $chart->script() !!}
    @endpush
@endif


