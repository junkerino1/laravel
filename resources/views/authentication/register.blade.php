<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/zephyr/bootstrap.min.css"
        integrity="sha512-CWXb9sx63+REyEBV/cte+dE1hSsYpJifb57KkqAXjsN3gZQt6phZt7e5RhgZrUbaNfCdtdpcqDZtuTEB+D3q2Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<div class="container w-50">
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <h2 class='m-2'>Register</h2>
        <div class="row">
            <label class="col-form-label mt-2">Username</label>
            <input class="form-control" type="text" id="username" name="username" />
        </div>
        <div class="row">
            <label class="col-form-label mt-2">Password</label>
            <input class="form-control" type="password" id="password" name="password" />
        </div>
        <div class="mt-3">
            <button class="btn btn-success" type="submit">Register</button>
        </div>
    </form>
</div>
