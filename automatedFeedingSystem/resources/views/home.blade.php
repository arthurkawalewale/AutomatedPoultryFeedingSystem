@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <header>
            <h5 class="text-bold">Live Monitoring</h5>
        </header>

        <div class="pb-2 col-md-6">
            <livewire:dashboard.water-level-data-sets/>
        </div>

        <div class="pb-2 col-md-6">
            <livewire:dashboard.feed-level-datasets/>
        </div>

    </div>

    <div class="row g-5">
        <div class="col-md-6">
            <livewire:dashboard.water-level-stats/>
        </div>

        <div class="col-md-6">
            <livewire:dashboard.feed-level-stats/>
        </div>
    </div>

    <br>

    <!--<div>
        <button type="button" class="btn btn-secondary mb-3" id="controls"> <x-feathericon-settings class="align-text-bottom" style="height: 20px"/> Controls</button>

        <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
            <div class="col-lg-6">
                <form class="border p-4 rounded-5">
                    <fieldset>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>
                                    <legend><h5>Water Valve</h5></legend>
                                    <div class="form-check form-switch d-inline-block align-text-bottom">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"/>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-md-6">
                                <fieldset>
                                    <legend><h5>Feed Valve</h5></legend>
                                    <div class="form-check form-switch d-inline-block align-text-bottom">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"/>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>-->
</div>

<button onclick="topFunction()" id="myBtn" class="btn btn-sm btn-primary" title="Go to top"><x-feathericon-chevron-up style="height: 40px"/></button>
@endsection
