@extends('layouts.app')

@section('title', 'Home')

@section('content')

<!-- NAVBAR -->
<div class="navbar fixed top-0 left-0 w-full flex justify-between items-center px-6 py-3 bg-white shadow z-50">
    <div class="logo font-bold text-[22px]" style="color: #8755b7ff;">Caffinder</div>


    <div class="navbar-links flex items-center gap-6 text-lg">
        <a href="/" class="hover:text-purple-600">Home</a>
        <span id="themeToggle" class="theme-icon cursor-pointer text-xl">🌙</span>
    </div>
</div>

<!-- Spacer biar konten tidak ketimpa navbar -->
<div class="h-16"></div>

<!-- SEARCH FORM SLIM -->
<form action="/search" 
      class="mt-8 mb-10 flex items-center gap-3"> <!-- mt-14 jadi mt-8 -->

    <input type="text" name="q" value="{{ $q }}" placeholder="Cari cafe di Medan..."
       class="flex-1 border rounded-full px-5 py-2.5
              bg-white border-gray-300 text-gray-700 placeholder-gray-400
              focus:outline-none focus:ring-2 focus:ring-purple-400
              transition duration-200
              dark:bg-black dark:border-white dark:text-white dark:placeholder-white dark:focus:ring-white">


    <select name="category"
            class="border border-gray-300 rounded-full px-4 py-2.5
                   focus:ring-2 focus:ring-purple-400 focus:outline-none transition">
        <option value="">Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}" @selected($cat == $category)>
                {{ $cat }}
            </option>
        @endforeach
    </select>

    <button class="px-6 py-2.5 text-white rounded-full font-medium transition"
        style="background: #8e44ad; hover:bg: #732d91;">
        Cari
    </button>

</form>

<!-- CAFE GRID -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($cafes as $cafe)
    <a href="{{ route('cafes.show', $cafe['id']) }}"
       class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden hover:shadow-lg transition">

        <img src="{{ $cafe['image'] }}"
             class="w-full h-48 object-cover">

        <div class="p-4">
            <!-- Judul tetap hitam di light mode, putih di dark mode -->
            <h3 class="font-semibold text-lg text-black dark:text-white">{{ $cafe['name'] }}</h3>

            <!-- Alamat bisa berubah abu-abu di light mode, lebih lembut di dark mode -->
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $cafe['address'] }}</p>

            <p class="mt-2 text-yellow-600">⭐ {{ $cafe['rating'] }}</p>
        </div>
    </a>
    @endforeach
</div>

<!-- DARK MODE SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body; // pakai body, bukan html

    if (!toggleBtn) return;

    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark');
        toggleBtn.textContent = "☀️";
    }

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
});
</script>


@endsection
