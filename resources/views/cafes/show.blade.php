@extends('layouts.app')

@section('title', $cafe['name'])

@section('content')

@php
    function fmtCoord($v) {
        if (!$v) return '-';
        return number_format($v, 5);
    }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Bagian Kiri --}}
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-200">
        @if($cafe['foto'])
            <img src="{{ $cafe['foto'] }}"
                onerror="this.onerror=null; this.src='/images/logo.png';"
                class="w-full h-64 object-cover rounded-b-xl">
        @else
            <div class="w-full h-64 flex items-center justify-center bg-gray-200 text-gray-600 text-lg font-semibold rounded-b-xl">
                No Image
            </div>
        @endif

        <div class="p-5 space-y-1">
            <h2 class="text-2xl font-bold text-[#4A2F21]">{{ $cafe['name'] }}</h2>

           {{-- Rating + Bintang --}}
            @php
                $rating = isset($cafe['rating']) ? floatval($cafe['rating']) : 0;
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
            @endphp

            <div class="flex items-center gap-3 mt-1">

                {{-- Angka rating dulu --}}
                <span class="text-base font-semibold text-gray-700">
                    {{ number_format($rating, 1, ',', '.') }}
                </span>

                {{-- Ikon bintang --}}
                <div class="flex items-center">
                    {{-- Full star --}}
                    @for ($i = 0; $i < $fullStars; $i++)
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.974a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.384 2.46a1 1 0 00-.364 1.118l1.286 3.974c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.387 2.463c-.784.57-1.84-.197-1.54-1.118l1.286-3.974a1 1 0 00-.364-1.118L2.612 9.401c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69L9.05 2.927z"/>
                        </svg>
                    @endfor

                    {{-- Half star --}}
                    @if ($halfStar)
                        <svg class="w-5 h-5 text-yellow-500" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="halfGrad">
                                    <stop offset="50%" stop-color="currentColor"/>
                                    <stop offset="50%" stop-color="transparent"/>
                                </linearGradient>
                            </defs>
                            <path fill="url(#halfGrad)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.974a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.384 2.46a1 1 0 00-.364 1.118l1.286 3.974c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.387 2.463c-.784.57-1.84-.197-1.54-1.118l1.286-3.974a1 1 0 00-.364-1.118L2.612 9.401c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69L9.05 2.927z"/>
                        </svg>
                    @endif

                    {{-- Empty star --}}
                    @for ($i = 0; $i < $emptyStars; $i++)
                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.974a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.384 2.46a1 1 0 00-.364 1.118l1.286 3.974c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.387 2.463c-.784.57-1.84-.197-1.54-1.118l1.286-3.974a1 1 0 00-.364-1.118L2.612 9.401c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69L9.05 2.927z"/>
                        </svg>
                    @endfor
                </div>

            </div>

            <!-- Bagian kiri -->
            {{-- Alamat Resmi --}}
            <p class="text-gray-700 font-semibold mt-3">Alamat Resmi:</p>
            <p class="text-gray-800 leading-snug">
                {{ $cafe['address'] ?? '-' }}
            </p>

            {{-- Alamat OSM (Informasi Tambahan) --}}
            @if(isset($cafe['osm']['display_name']))
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h3 class="font-semibold text-blue-700 mb-1 text-sm">
                        Alamat berdasarkan koordinat (OSM)
                    </h3>

                    <p class="text-sm text-gray-700 leading-snug">
                        {{ $cafe['osm']['display_name'] }}
                    </p>

                    @if(isset($cafe['osm']['boundingbox']))
                        <p class="mt-1 text-xs text-gray-500">
                            Bounding Box: {{ implode(', ', $cafe['osm']['boundingbox']) }}
                        </p>
                    @endif
                </div>
            @endif

            {{-- Telepon --}}
            <p class="text-gray-600 flex items-center gap-2 mt-3">
                <svg class="w-5 h-5 text-[#6B4F3A]" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.13 1.13 0 00-1.173.417l-.97 1.293a1.125 1.125 0 01-1.21.378 12.035 12.035 0 01-7.143-7.143 1.125 1.125 0 01.378-1.21l1.293-.97c.34-.255.485-.694.417-1.173L6.713 3.102A1.125 1.125 0 005.622 2.25H4.25A2.25 2.25 0 002 4.5v2.25z"/>
                </svg>
                <span class="font-semibold">{{ $cafe['telepon'] ?? '-' }}</span>
            </p>
        </div>

            {{-- Map + Koordinat --}}
            @if($cafe['latitude'] && $cafe['longitude'])
                <div class="p-4">
                    <div class="rounded-xl overflow-hidden shadow-md mb-2">
                        <iframe
                            width="100%"
                            height="300"
                            frameborder="0"
                            style="border:0;"
                            src="https://www.google.com/maps?q={{ $cafe['latitude'] }},{{ $cafe['longitude'] }}&output=embed"
                            allowfullscreen>
                        </iframe>
                    </div>

                    <p class="text-sm text-gray-600">
                        Koordinat: {{ fmtCoord($cafe['latitude']) }}, {{ fmtCoord($cafe['longitude']) }}
                    </p>
                </div>
            @endif

    </div>

    {{-- Bagian Kanan --}}
    <div class="lg:col-span-2 bg-white shadow-lg rounded-2xl p-8 border border-gray-200 text-gray-800">

        {{-- Back button di atas --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}" 
               class="inline-flex items-center gap-2 text-sm font-semibold text-green-600 hover:text-green-700">
                {{-- icon panah --}}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
        </div>

        <h3 class="text-2xl font-semibold mb-6 text-[#4A2F21]">Informasi Cafe</h3>

        <div class="grid grid-cols-2 gap-4 text-gray-700">

            @php
                function facilityBadge($val) {
                    if ($val === true) {
                        return '
                            <span class="inline-flex items-center gap-2 ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Ya
                            </span>
                        ';
                    }

                    if ($val === false) {
                        return '
                            <span class="inline-flex items-center gap-2 ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tidak
                            </span>
                        ';
                    }

                    return '
                        <span class="inline-flex items-center gap-2 ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                            Tidak diketahui
                        </span>
                    ';
                }
            @endphp

            {{-- Fasilitas dengan badge --}}
            <p>
                <b class="text-[#6B4F3A]">WiFi:</b>
                {!! facilityBadge($cafe['wifi']) !!}
            </p>

            <p>
                <b class="text-[#6B4F3A]">Ramah Laptop:</b>
                {!! facilityBadge($cafe['laptop']) !!}
            </p>

            <p>
                <b class="text-[#6B4F3A]">Alcohol:</b>
                {!! facilityBadge($cafe['alcohol']) !!}
            </p>

            <p>
                <b class="text-[#6B4F3A]">Kursi Roda:</b>
                {!! facilityBadge($cafe['wheel']) !!}
            </p>


            <p>
                <b class="text-[#6B4F3A]">Live Music:</b>
                {!! facilityBadge($cafe['live_music']) !!}
            </p>

            <p>
                <b class="text-[#6B4F3A]">Pet Friendly:</b>
                {!! facilityBadge($cafe['pet_friendly']) !!}
            </p>

            {{-- Harga --}}
            <p><b class="text-[#6B4F3A]">Harga Min:</b> {{ number_format($cafe['harga_min'] ?? 0, 0, ',', '.') ? 'Rp ' . number_format($cafe['harga_min'], 0, ',', '.') : '-' }}</p>
            <p><b class="text-[#6B4F3A]">Harga Max:</b> {{ number_format($cafe['harga_max'] ?? 0, 0, ',', '.') ? 'Rp ' . number_format($cafe['harga_max'], 0, ',', '.') : '-' }}</p>

            {{-- Lain-lain --}}
            <p><b class="text-[#6B4F3A]">Menu Rekomendasi:</b> {{ $cafe['bestmenu'] ?? '-' }}</p>
            
        </div>

        <br>
       <div>

    <h3 class="text-xl font-semibold mb-4 text-[#4A2F21]">Jam Operasional</h3>

        <div class="bg-[#FAF5F0] border border-[#E8D8CC] rounded-2xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-6 text-gray-700">

                @php
                    function formatTime($time) {
                        if (!$time) return '--';
                        return substr($time, 0, 5);
                    }

                    $days = [
                        'Senin' => ['open' => formatTime($cafe['open_senin'] ?? null), 'close' => formatTime($cafe['close_senin'] ?? null)],
                        'Selasa' => ['open' => formatTime($cafe['open_selasa'] ?? null), 'close' => formatTime($cafe['close_selasa'] ?? null)],
                        'Rabu' => ['open' => formatTime($cafe['open_rabu'] ?? null), 'close' => formatTime($cafe['close_rabu'] ?? null)],
                        'Kamis' => ['open' => formatTime($cafe['open_kamis'] ?? null), 'close' => formatTime($cafe['close_kamis'] ?? null)],
                        'Jumat' => ['open' => formatTime($cafe['open_jumat'] ?? null), 'close' => formatTime($cafe['close_jumat'] ?? null)],
                        'Sabtu' => ['open' => formatTime($cafe['open_sabtu'] ?? null), 'close' => formatTime($cafe['close_sabtu'] ?? null)],
                        'Minggu' => ['open' => formatTime($cafe['open_minggu'] ?? null), 'close' => formatTime($cafe['close_minggu'] ?? null)],
                    ];
                @endphp

                @foreach($days as $day => $time)
                    <div class="flex items-center justify-start space-x-4 py-1 border-b border-[#E8D8CC]">
                        <span class="w-20 font-semibold text-[#6B4F3A]">{{ $day }}</span>
                        <span>{{ $time['open'] }} - {{ $time['close'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection