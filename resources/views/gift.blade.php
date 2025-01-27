@extends('default')

@section('content')
    <div class="container">
        @if (session('message'))
            <div class="alert alert-success">
                Credit successfully!
            </div>
        @endif

        <div class="container w-75">
            <form method="POST" action="{{ route('credit') }}">
                @csrf
                <div class="row">
                    <label class="col-form-label mt-2">Amount ($)</label>
                    <input class="form-control" type="number" name="amount" required>
                </div>
                <div class="my-2">
                    Balance: ${{ $wallet_balance }}
                </div>
                <div>
                    <button class="btn btn-success" type="submit">Credit</button>
                </div>
            </form>
        </div>

        <div class="container w-75 my-5">
            <a class="btn btn-primary mt-10" href="{{ route('checkGift') }}">Check Gift</a>

            @if (session('gift_message'))
            <div class="card bg-light p-3 my-3" style="max-width: 20rem;">
                    <p>{{ session('gift_message') }}</p>
                    <p>
                        @foreach (session('gift_issued') as $gift)
                            <h4>{{ $gift->gift_name }}</h4>
                        @endforeach
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

