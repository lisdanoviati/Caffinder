@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

<!-- navbar -->
<nav id="navbar"
    class="w-full fixed top-0 left-0 z-50 transition-all duration-300
           bg-white/10 backdrop-blur-md shadow-sm">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

        <div class="text-2xl font-bold text-white drop-shadow-md">
            Caffinder
        </div>

        <ul class="flex gap-8 font-medium">
            <li><a href="#home" class="text-white hover:text-[#F7F2EC]">Home</a></li>
            <li><a href="#about" class="text-white hover:text-[#F7F2EC]">About</a></li>
            <li><a href="#gallery" class="text-white hover:text-[#F7F2EC]">Gallery</a></li>
        </ul>

        <div>
        <!-- Tombol Explore -->
        <a href="{{ route('cafes.index') }}"
           class="px-4 py-2 bg-[#8B5E3C] text-white rounded-full shadow">
            Explore
        </a>
    </div>
</div>
</nav>

{{-- HERO --}}
<section id="home"
class="pt-40 pb-40 bg-cover bg-center relative"
style="background-image: url('/images/welcome.jpg');">
    
    {{-- Hapus overlay & blur (di-remove) --}}

    <div class="relative max-w-7xl mx-auto grid md:grid-cols-2 gap-10 px-6">

        <div class="flex flex-col justify-center text-white drop-shadow-xl">
           <h1 class="text-4xl font-bold leading-tight">
            Explore Medan’s Best Cafes With Just One Tap!
        </h1>

            <p class="mt-4 text-white/90">
                Temukan cafe terbaik, ambience premium, dan rekomendasi menu terbaik sesuai selera kamu. Dapatkan info lengkap seperti rating, alamat, fasilitas, menu rekomendasi, dan jam buka setiap cafe.
            </p>

            <a href="{{ route('cafes.index') }}"
               class="mt-6 inline-block bg-[#8B5E3C] text-white px-6 py-3 rounded-full shadow-md hover:bg-[#5A3A28] transition">
                Explore Cafes
            </a>
        </div>

        <div></div>

    </div>
</section>


{{-- ABOUT --}}
<section id="about" class="pt-20 pb-6 bg-[#F7F2EC]">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-3xl font-bold mb-6" style="color:#5A3A28;">
            About Us
        </h2>

        <div class="grid md:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl shadow p-5 border border-[#8B5E3C]/20 h-full flex flex-col">
            <img src="/images/about1.jpg" class="rounded-xl mb-3 h-32 w-full object-cover">
            <h3 class="text-xl font-semibold" style="color:#5A3A28;">Tentang Caffinder</h3>
            <p class="text-[#5A3A28]/70 text-sm mt-2 mt-auto">
                Tempat terbaik untuk menemukan café favoritmu. Kami hadir untuk membantumu menjelajahi berbagai spot hangout dengan suasana nyaman, menu yang variatif, dan vibe yang cocok untuk siapa pun.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border border-[#8B5E3C]/20 h-full flex flex-col">
            <img src="/images/about2.jpg" class="rounded-xl mb-3 h-32 w-full object-cover">
            <h3 class="text-xl font-semibold" style="color:#5A3A28;">Rekomendasi Real</h3>
            <p class="text-[#5A3A28]/70 text-sm mt-2 mt-auto">
                Semua rekomendasi berasal dari penilaian asli para pengunjung. Transparan, jujur, dan selalu diperbarui agar kamu mendapatkan informasi yang benar-benar relevan dan dapat dipercaya.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border border-[#8B5E3C]/20 h-full flex flex-col">
            <img src="/images/about3.jpg" class="rounded-xl mb-3 h-32 w-full object-cover">
            <h3 class="text-xl font-semibold" style="color:#5A3A28;">Quality First</h3>
            <p class="text-[#5A3A28]/70 text-sm mt-2 mt-auto">
                Kami mengutamakan kualitas—mulai dari ambience, pelayanan, hingga kenyamanan pengunjung. Setiap café dipilih dan dinilai berdasarkan standar kualitas yang konsisten.
            </p>
        </div>
    </div>
</div>
</section>

{{-- GALLERY --}}
<section id="gallery" class="py-20 bg-[#F7F2EC]">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-3xl font-bold mb-8" style="color:#5A3A28;">
            Gallery
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery1.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

            <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery2.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

            <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery3.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

            <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery4.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

            <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery5.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

            <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery6.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

             <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery7.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

             <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery8.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

             <div class="bg-white rounded-2xl shadow p-3 border border-[#F3E9DC]/20 overflow-hidden hover:scale-105 transition">
                <img src="/images/gallery9.jpg" class="w-full h-64 object-cover rounded-xl">
            </div>

        </div>

    </div>
</section>
@endsection