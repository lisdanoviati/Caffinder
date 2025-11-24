@extends('layouts.app')

@section('title', 'Home')

@section('content')

<!-- NAVBAR -->
<div class="navbar fixed top-0 left-0 w-full flex justify-between items-center px-6 py-3 shadow z-50"
     style="background:#5C4033;">
    
    <div class="logo font-bold text-[22px] text-white">
        Caffinder
    </div>

   <div class="navbar-links flex items-center gap-6 text-lg text-white">
        <a href="/" class="hover:text-[#C49B75]">Home</a>
        <span id="themeToggle" class="theme-icon cursor-pointer text-xl">🌙</span>
    </div>

</div>

<!-- Spacer -->
<div class="h-16"></div>

<!-- SEARCH FORM SLIM -->
<form action="/search" 
      class="mt-8 mb-10 flex items-center gap-3">

    <!-- SEARCH BAR -->
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari cafe di Medan..."
       class="flex-[3] border rounded-full px-5 py-2.5 transition
              bg-[#F9F5F0] border-gray-300 text-gray-700 placeholder-gray-500
              focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]

              dark:bg-[#1f1b18] dark:border-[#8B5E3C]
              dark:text-white dark:placeholder-gray-300">

    <!-- CATEGORY -->
    <select name="category"
        class="flex-[1] border rounded-full px-4 py-2.5 transition
               bg-[#F9F5F0] text-gray-700 border-gray-300
               focus:ring-2 focus:ring-[#8B5E3C] focus:outline-none

               dark:bg-[#1f1b18] dark:border-[#8B5E3C]
               dark:text-white">
        <option value="">Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ $cat['id'] }}" @selected($cat['id'] == $category)>
                {{ $cat['name'] }}
            </option>
        @endforeach
    </select>

    <!-- DISTRICT -->
    <select name="district"
        class="flex-[1] border rounded-full px-4 py-2.5 transition
               bg-[#F9F5F0] text-gray-700 border-gray-300
               focus:ring-2 focus:ring-[#8B5E3C] focus:outline-none

               dark:bg-[#1f1b18] dark:border-[#8B5E3C]
               dark:text-white">
        <option value="">Zona Kecamatan</option>
        @foreach($districts as $d)
            <option value="{{ $d }}" @selected($d == ($filters['district'] ?? ''))>
                {{ $d }}
            </option>
        @endforeach
    </select>

    <!-- BUTTON -->
    <button class="px-6 py-2.5 text-white rounded-full font-medium transition"
        style="background: #5C4033;">
        Cari
    </button>
</form>

<!-- CAFE GRID -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($cafes as $cafe)
    <a href="{{ route('cafes.show', $cafe['id']) }}"
       class="shadow rounded-xl overflow-hidden hover:shadow-lg transition
              bg-[#F9F5F0] dark:bg-gray-800">

        <img src="{{ $cafe['image'] ?? '/default.jpg' }}"
             class="w-full h-48 object-cover">

        <div class="p-4">
            <h3 class="font-semibold text-lg text-black dark:text-white">
                {{ $cafe['name'] ?? 'Tanpa Nama' }}
            </h3>

            <p class="text-gray-600 dark:text-gray-400 text-sm">
                {{ $cafe['address'] ?? 'Alamat tidak tersedia' }}
            </p>

            <p class="mt-2 text-[#8B5E3C] font-semibold">⭐ {{ $cafe['rating'] ?? '0' }}</p>
        </div>
    </a>
    @endforeach
</div>

<!-- DARK MODE SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById('themeToggle');
    const body = document.body;

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
