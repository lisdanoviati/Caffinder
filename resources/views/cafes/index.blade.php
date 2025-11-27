@extends('layouts.app')

@section('title', 'Home')

@section('content')

<!-- NAVBAR -->
<div class="navbar fixed top-0 left-0 w-full flex justify-between items-center px-6 py-3 shadow z-50"
     style="background:#8B5E3C;">
    
   <div class="flex items-center gap-3">
        <img src="/images/logo.png" alt="Logo" class="h-9 w-9 object-cover">
        <span class="text-2xl font-bold text-white drop-shadow-md">
                Caffinder
        </span>
    </div>
    
    <div class="navbar-links flex items-center gap-6 text-lg text-white">
        <a href="/" class="hover:text-[#C49B75]">Home</a>
    </div>
</div>

<!-- Spacer biar konten tidak ketimpa navbar -->
<div class="h-16"></div>

<!-- SEARCH FORM SLIM -->
<form action="/search" 
      method="GET"
      class="mt-8 mb-10 flex items-center gap-3">

    <!-- SEARCH BAR -->
    <input type="text" name="q" value="{{ $q }}"
       placeholder="Cari cafe di Medan..."
       class="flex-[3] border rounded-full px-5 py-2.5 transition
              bg-[#F9F5F0] border-gray-300 text-gray-700 placeholder-gray-500
              focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]

              dark:bg-[#1f1b18] dark:border-[#8B5E3C]
              dark:text-white dark:placeholder-gray-300">

    <!-- FILTER MENU -->
    <div class="relative">
        <div onclick="document.getElementById('filterMenu').classList.toggle('hidden')"
            class="border border-gray-300 rounded-full px-4 py-2.5 cursor-pointer w-56
                   bg-[#F9F5F0] hover:border-[#8B5E3C] transition
                   flex items-center justify-between text-gray-700

                   dark:bg-[#1f1b18] dark:text-white dark:border-[#8B5E3C]">
            <span>Filter Lengkap</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <div id="filterMenu"
            class="absolute mt-2 w-64 bg-[#F9F5F0] border border-gray-300 rounded-lg shadow-lg p-4 hidden z-50
                   dark:bg-[#1f1b18] dark:border-[#8B5E3C]">

            <!-- Kategori -->
            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Kategori</label>
            <select name="category"
                class="w-full border rounded-lg px-3 py-2 mb-3
                       bg-white dark:bg-[#1f1b18] dark:border-[#8B5E3C] dark:text-gray-200">
                <option value="">Semua</option>

                @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}" 
                            @selected($cat['id'] == ($filters['category'] ?? ''))>
                        {{ $cat['name'] }}
                    </option>
                @endforeach
            </select>

            <!-- Fasilitas -->
            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Fasilitas</label>
            <div class="max-h-40 overflow-y-auto pr-2 space-y-1 mb-4 text-gray-700 dark:text-gray-300">
                @foreach($facilities as $f)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="facilities[]" value="{{ $f }}"
                            @checked(in_array($f, $selectedFacilities ?? []))>
                        <span>{{ $f }}</span>
                    </label>
                @endforeach
            </div>

            <!-- Harga -->
            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Harga</label>
            <select name="price"
                class="w-full border rounded-lg px-3 py-2 mb-3
                       bg-white dark:bg-[#1f1b18] dark:border-[#8B5E3C] dark:text-gray-200">
                <option value="">Semua</option>
                <option value="1-50000" @selected(request('price')=='1-50000')><=50k</option>
                <option value="50000-75000" @selected(request('price')=='50000-75000')>50–75k</option>
                <option value="75000-100000" @selected(request('price')=='75000-100000')>>=75k</option>
            </select>

            <!-- Rating -->
            <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-200">Rating</label>
            <select name="order"
                class="w-full border rounded-lg px-3 py-2
                       bg-white dark:bg-[#1f1b18] dark:border-[#8B5E3C] dark:text-gray-200">
                <option value="">Default</option>
                <option value="rating_desc" @selected(request('order')=='rating_desc')>
                    Tertinggi → Terendah
                </option>
                <option value="rating_asc" @selected(request('order')=='rating_asc')>
                    Terendah → Tertinggi
                </option>
            </select>

        </div>
    </div>

    <!-- DISTRICT (Kecamatan) -->
    <select name="district"
        class="flex-[1] border rounded-full px-4 py-2.5 transition
               bg-[#F9F5F0] text-gray-700 border-gray-300
               focus:ring-2 focus:ring-[#8B5E3C] focus:outline-none

               dark:bg-[#1f1b18] dark:border-[#8B5E3C] dark:text-gray-300">
        <option value="">Zona Kecamatan</option>

        @foreach($districts as $d)
            <option value="{{ $d }}"
                @selected($d == ($filters['district'] ?? ''))>
                {{ $d }}
            </option>
        @endforeach
    </select>

    <!-- BUTTON -->
    <button class="px-6 py-2.5 text-white rounded-full font-medium transition"
        style="background: #8B5E3C;">
        Cari
    </button>
</form>

<!-- CAFE GRID -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($cafes as $cafe)
    <a href="{{ route('cafes.show', $cafe['id']) }}"
       class="shadow rounded-xl overflow-hidden hover:shadow-lg transition
              bg-[#F9F8F6] dark:bg-gray-800">

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
</div>

@endsection