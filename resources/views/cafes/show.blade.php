@extends('layouts.app')

@section('title', $cafe['name'])

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Bagian Kiri --}}
    <div class="bg-white shadow rounded-xl overflow-hidden">
        @if($cafe['foto'])
            <img src="{{ $cafe['foto'] }}" class="w-full h-64 object-cover">
        @endif

        <div class="p-4">
            <h2 class="text-xl font-bold">{{ $cafe['name'] }}</h2>
            <p class="text-gray-600">{{ $cafe['alamat'] }}</p>
            <p class="text-gray-600">Kategori: {{ $cafe['kategori'] ?? 'N/A' }}</p>
            <p class="text-gray-600">Rating: {{ $cafe['rating'] ?? 'N/A' }}</p>
            <p class="text-gray-600">Telepon: {{ $cafe['telepon'] ?? '-' }}</p>
        </div>

        {{-- Maps Static Image --}}
        @if($cafe['latitude'] && $cafe['longitude'])
        <div class="p-4">
            <iframe
                width="100%"
                height="300"
                frameborder="0"
                style="border:0; border-radius: 0.5rem;"
                src="https://www.google.com/maps?q={{ $cafe['latitude'] }},{{ $cafe['longitude'] }}&output=embed"
                allowfullscreen>
            </iframe>
        </div>
        @endif
    </div>

    {{-- Bagian Kanan --}}
    <div class="lg:col-span-2 bg-white shadow rounded-xl p-6 text-gray-800">

        <h3 class="text-2xl font-semibold mb-4">Informasi Cafe</h3>

        <div class="grid grid-cols-2 gap-4">
            
            {{-- Fasilitas --}}
            <p><b>WiFi:</b> {{ $cafe['wifi'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Ramah Laptop:</b> {{ $cafe['laptop'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Alcohol:</b> {{ $cafe['alcohol'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Kursi Roda:</b> {{ $cafe['wheel'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Live Music:</b> {{ $cafe['live_music'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Pet Friendly:</b> {{ $cafe['pet_friendly'] ? 'Ya' : 'Tidak' }}</p>

            {{-- Harga --}}
            <p><b>Harga Min:</b> {{ $cafe['harga_min'] ?? '-' }}</p>
            <p><b>Harga Max:</b> {{ $cafe['harga_max'] ?? '-' }}</p>

            {{-- Jam Operasional --}}
            
            {{-- Lain-lain --}}
            <p><b>Menu Rekomendasi:</b> {{ $cafe['bestmenu'] ?? '-' }}</p>
            <p><b>Latitude:</b> {{ $cafe['latitude'] }}</p>
            <p><b>Longitude:</b> {{ $cafe['longitude'] }}</p>
            
        </div>
        <br>
        <div>
            <h3 class="text-xl font-semibold mb-3">Jam Operasional</h3><br>
        
            <div class="grid grid-cols-2 gap-2 text-gray-700">
                @php
                    $days = [
                        'Senin' => ['open' => $cafe['open_senin'] ?? '--', 'close' => $cafe['close_senin'] ?? '--'],
                        'Selasa' => ['open' => $cafe['open_selasa'] ?? '--', 'close' => $cafe['close_selasa'] ?? '--'],
                        'Rabu' => ['open' => $cafe['open_rabu'] ?? '--', 'close' => $cafe['close_rabu'] ?? '--'],
                        'Kamis' => ['open' => $cafe['open_kamis'] ?? '--', 'close' => $cafe['close_kamis'] ?? '--'],
                        'Jumat' => ['open' => $cafe['open_jumat'] ?? '--', 'close' => $cafe['close_jumat'] ?? '--'],
                        'Sabtu' => ['open' => $cafe['open_sabtu'] ?? '--', 'close' => $cafe['close_sabtu'] ?? '--'],
                        'Minggu' => ['open' => $cafe['open_minggu'] ?? '--', 'close' => $cafe['close_minggu'] ?? '--'],
                    ];
                @endphp
        
                @foreach($days as $day => $time)
                    <p><b>{{ $day }}:</b> {{ $time['open'] }} - {{ $time['close'] }}</p>
                @endforeach
            </div>
        </div>
        <a href="{{ url()->previous() }}" class="text-green-600 mt-6 inline-block">← Kembali</a>
        
    </div>
</div>

@endsection