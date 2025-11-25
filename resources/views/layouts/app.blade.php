<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caffinder @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll('.theme-icon');
    const body = document.body;

    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark');
        toggles.forEach(btn => btn.textContent = "☀️");
    }

    toggles.forEach(btn => {
        btn.addEventListener('click', () => {
            body.classList.toggle('dark');
            if (body.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                toggles.forEach(b => b.textContent = "☀️");
            } else {
                localStorage.setItem('theme', 'light');
                toggles.forEach(b => b.textContent = "🌙");
            }
        });
    });
});
</script>

<body>
    <header class="bg-white shadow-md">
    </header>

    <main class="max-w-6xl mx-auto p-6">
        @yield('content')
    </main>

    <footer class="text-center text-gray-500 py-10">
        © {{ date('Y') }} Caffinder
    </footer>
</body>
</html>
