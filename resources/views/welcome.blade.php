@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

{{-- NAVBAR --}}
<nav class="w-full bg-[#F7F2EC] shadow-md fixed top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

        <div class="text-2xl font-bold" style="color:#8B5E3C;">
            Caffinder
        </div>

        <ul class="flex gap-8 text-[#5A3A28] font-medium">
            <li><a href="#home" class="hover:text-[#8B5E3C]">Home</a></li>
            <li><a href="#about" class="hover:text-[#8B5E3C]">About</a></li>
            <li><a href="#gallery" class="hover:text-[#8B5E3C]">Gallery</a></li>
            <li><a href="{{ route('cafes.index') }}" class="hover:text-[#8B5E3C]">Explore</a></li>
        </ul>

        <a href="#" class="px-4 py-2 bg-[#8B5E3C] text-white rounded-full shadow">
            Contact
        </a>
    </div>
</nav>


{{-- HERO --}}
<section id="home" class="pt-32 pb-20 bg-[#EFE7DD]">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 px-6">

        <div class="flex flex-col justify-center">
            <h1 class="text-5xl font-bold leading-tight" style="color:#5A3A28;">
                Brewed Fresh,<br> Served Warm
            </h1>

            <p class="mt-4 text-[#5A3A28]/80">
                Temukan cafe terbaik, ambience premium, dan rekomendasi kopi sesuai selera kamu.
            </p>

            <a href="{{ route('cafes.index') }}"
               class="mt-6 inline-block bg-[#8B5E3C] text-white px-6 py-3 rounded-full shadow-md hover:bg-[#5A3A28] transition">
                Explore Cafes
            </a>
        </div>

        {{-- hero img --}}
        <div>
            <img src="/images/hero-coffee.jpg"
                 class="rounded-2xl shadow-lg border border-[#8B5E3C]/20">
        </div>
    </div>
</section>


{{-- ABOUT (bagian yang kamu lingkari hijau) --}}
<section id="about" class="py-20 bg-[#F7F2EC]">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-3xl font-bold mb-6" style="color:#5A3A28;">
            About Us
        </h2>

        <div class="grid md:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl shadow p-5 border border-[#8B5E3C]/20">
                <img src="/images/about1.jpg" class="rounded-xl mb-3">
                <h3 class="text-xl font-semibold" style="color:#5A3A28;">Tentang Caffinder</h3>
                <p class="text-[#5A3A28]/70 text-sm mt-2">
                    Platform untuk menemukan cafe terbaik di kotamu.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 border border-[#8B5E3C]/20">
                <img src="/images/about2.jpg" class="rounded-xl mb-3">
                <h3 class="text-xl font-semibold" style="color:#5A3A28;">Rekomendasi Real</h3>
                <p class="text-[#5A3A28]/70 text-sm mt-2">
                    Semua data dikurasi berdasarkan rating real pengguna.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow p-5 border border-[#8B5E3C]/20">
                <img src="/images/about3.jpg" class="rounded-xl mb-3">
                <h3 class="text-xl font-semibold" style="color:#5A3A28;">Quality First</h3>
                <p class="text-[#5A3A28]/70 text-sm mt-2">
                    Fokus pada ambience, rasa kopi, dan pengalaman pengunjung.
                </p>
            </div>

        </div>

    </div>
</section>

@endsection
