@extends('default')

@section('content')
    <div class="container w-50">

        @if (session('message'))
            <div class="container">
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <h3 class="text-primary">Enter OTP</h3>
        <form method="post" action="{{ route('checkOTP') }}">
            @csrf
            <div class="row">
                <label class="col-form-label mt-2">OTP: </label>
                <input class="form-control" type="text" name="otp" placeholder="eg: 456789" required>
            </div>
            <div class="mt-3">
                <button class="btn btn-success" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection
