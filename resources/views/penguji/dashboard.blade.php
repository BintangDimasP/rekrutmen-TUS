@extends('layouts.admin')

@section('title', 'Dashboard Penguji')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Penguji</h1>
            <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}. Berikut ringkasan penugasan pengujian Anda.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center relative overflow-hidden group hover:border-blue-200 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 7c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.76 1.29 6 2H6z"/></svg>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Total Pelamar Diuji</p>
            <h3 class="text-4xl font-extrabold text-blue-600">{{ $totalDiuji }}</h3>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center relative overflow-hidden group hover:border-green-200 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Selesai Dinilai</p>
            <h3 class="text-4xl font-extrabold text-green-600">{{ $totalDinilai }}</h3>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center relative overflow-hidden group hover:border-red-200 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>
            </div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Belum Dinilai</p>
            <h3 class="text-4xl font-extrabold text-red-600">{{ $totalBelumDinilai }}</h3>
        </div>
    </div>

    <!-- Info Section -->
    <div class="bg-[#8b1515]/5 rounded-2xl border border-[#8b1515]/10 p-6 mt-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-[#8b1515]/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#8b1515]">Petunjuk Pengujian</h4>
                <p class="text-sm text-gray-600 mt-1">Anda dapat melihat daftar pelamar yang ditugaskan kepada Anda melalui menu <strong>Pengujian</strong>. Pastikan untuk mengisi form penilaian segera setelah selesai melakukan pengujian (Wawancara atau Micro Teaching).</p>
                <a href="{{ route('penguji.pengujian.index') }}" class="inline-flex items-center gap-1.5 mt-3 text-sm font-bold text-[#8b1515] hover:underline">
                    Mulai Menguji
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
