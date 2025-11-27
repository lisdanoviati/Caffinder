<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caffinder @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @vite(['resources/css/app.css'])
</head>

<!-- <body class="!bg-transparent !bg-none"> -->
<!-- <body class=""> -->
<body style="background:#F7F2EC; min-height:100vh;">

    <header class="bg-white shadow-md"></header>

    {{-- MAIN: full width khusus halaman welcome --}}
    <main class="@if(Request::is('/')) w-full p-0 m-0 @else max-w-6xl mx-auto p-6 @endif">
        @yield('content')
    </main>

    <footer class="text-center text-gray-500 py-2">
        © {{ date('Y') }} Caffinder
    </footer>

</body>
</html>
