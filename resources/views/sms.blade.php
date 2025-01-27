@extends('default')

@section('content')
    <div class="container w-50 h-50">
        <h3 class="text-primary">Request OTP</h3>
        <form method="post" action="{{ route('sendSMS') }}">
            @csrf
            <div class="row">
                <label class="col-form-label mt-2">Phone number: </label>
                <input class="form-control" type="text" name="to" placeholder="eg: 60123456789" required>
            </div>
            <div class="mt-3">
                <button class="btn btn-success" type="submit">Request</button>
            </div>
        </form>
    </div>
@endsection
