<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caffinder @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <header class="bg-white shadow-md">
        <!-- <div class="max-w-6xl mx-auto p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-green-600">Caffinder</h1>
        </div> -->
    </header>

    <main class="max-w-6xl mx-auto p-6">
        @yield('content')
    </main>

    <footer class="text-center text-gray-500 py-10">
        © {{ date('Y') }} Caffinder
    </footer>
</body>
</html>
