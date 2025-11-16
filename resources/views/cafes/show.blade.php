@extends('layouts.app')

@section('title', $cafe['name'])

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Bagian Kiri --}}
    <div class="bg-white shadow rounded-xl overflow-hidden">
        <img src="{{ $cafe['image'] }}" class="w-full h-64 object-cover">

        <div class="p-4">
            <h2 class="text-xl font-bold">{{ $cafe['name'] }}</h2>
            <p class="text-gray-600">{{ $cafe['address'] }}</p>
        </div>

        <div class="p-4">
            <a target="_blank"
               href="https://www.google.com/maps?q={{ $cafe['latitude'] }},{{ $cafe['longitude'] }}">
                <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $cafe['latitude'] }},{{ $cafe['longitude'] }}&zoom=15&size=600x300&markers=color:red%7C{{ $cafe['latitude'] }},{{ $cafe['longitude'] }}&key=YOUR_API_KEY"
                     class="rounded-lg">
            </a>
        </div>
    </div>

    {{-- Bagian Kanan --}}
    <div class="lg:col-span-2 bg-white shadow rounded-xl p-6 text-gray-800">

        <h3 class="text-2xl font-semibold mb-4">Informasi Cafe</h3>

        <div class="grid grid-cols-2 gap-4">
            <p><b>Rating:</b> {{ $cafe['rating'] }}</p>
            <p><b>Telepon:</b> {{ $cafe['phone'] ?? '-' }}</p>

            <p><b>WiFi:</b> {{ $cafe['wifi'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Ramah Laptop:</b> {{ $cafe['ramah_laptop'] ? 'Ya' : 'Tidak' }}</p>

            <p><b>Alcohol:</b> {{ $cafe['serves_alcohol'] ? 'Ya' : 'Tidak' }}</p>
            <p><b>Kategori:</b> {{ $cafe['category'] }}</p>

            <p><b>Latitude:</b> {{ $cafe['latitude'] }}</p>
            <p><b>Longitude:</b> {{ $cafe['longitude'] }}</p>
        </div>

        <a href="/" class="text-green-600 mt-6 inline-block">← Kembali</a>

    </div>
</div>

@endsection
