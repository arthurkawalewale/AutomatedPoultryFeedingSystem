<div>
    <div id="reports" class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="dropdown">
                <select wire:model="interval" id="interval" class="form-select">
                    @foreach($intervals as $key=>$value):
                        <option {{($key === $interval) ? "selected": "" }} value={{$key}}>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

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
    </div>
</div>

@if($chart)
    @push('scripts')
        {!! $chart->script() !!}
    @endpush
@endif
