<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caffinder @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @vite(['resources/css/app.css'])
</head>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body;

    // Apply stored theme
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark');
        if (toggleBtn) toggleBtn.textContent = "☀️";
    }

    // If there IS a button (not all pages have)
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark');

            if (body.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                toggleBtn.textContent = "☀️";
            } else {
                localStorage.setItem('theme', 'light');
                toggleBtn.textContent = "🌙";
            }
        });
    }
});
</script>

<!-- <body class="bg-[#F7F2EC] dark:bg-[#0f0f0f] transition-colors duration-300"> -->
<body class="!bg-transparent !bg-none">
<!-- <body class=""> -->
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
