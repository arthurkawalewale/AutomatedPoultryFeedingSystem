@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form
                    id="formAccountSettings"
                    method="POST"
                    action="{{ route('profile.update',auth()->id()) }}"
                    enctype="multipart/form-data"
                    class="needs-validation"
                    role="form"
                    novalidate
                >
                    @csrf
                    <div class="card my-5">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control" type="text" id="name" name="name" value="{{ auth()->user()->name }}" autofocus="" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">No of Birds.</label>
                                    <input class="form-control" type="text" id="birds_count" name="number_of_birds" value="{{ auth()->user()->number_of_birds}}" placeholder="eg:2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input class="form-control" type="text" id="email" name="email" value="{{ auth()->user()->email }}" placeholder="eg: john.doe@example.com">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Contact</label>
                                    <input class="form-control" type="tel" id="contact" name="contact" value="{{ auth()->user()->contact}}" placeholder="e.g: 0881 124 567">
                                </div>
                            </div>


                            <div class="mt-2">
                                <button type="submit" class="btn btn-md btn-success">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
