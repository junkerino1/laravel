<!-- resources/views/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootswatch CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/zephyr/bootstrap.min.css"
        integrity="sha512-CWXb9sx63+REyEBV/cte+dE1hSsYpJifb57KkqAXjsN3gZQt6phZt7e5RhgZrUbaNfCdtdpcqDZtuTEB+D3q2Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>@yield('title')</title>

    {{-- Inject the user_type into a global JavaScript variable --}}

</head>

<body>
    <!-- Header -->
    @include('layouts.header')

    @if (session('token'))
        <p>Token: {{ session('token') }}</p>
    @endif

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        @yield('footer')
    </footer>

    <!-- Include Vue Components -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>

</html>
