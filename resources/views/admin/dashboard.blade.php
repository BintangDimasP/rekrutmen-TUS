@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- ── Quick Stats ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-7">

    {{-- Card 1: Jumlah Lowongan --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
        <p class="font-bold text-3xl text-gray-800 mb-1">—</p>
        <p class="text-sm font-medium text-gray-500">Jumlah Lowongan</p>
    </div>

    {{-- Card 2: Jumlah Pelamar --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
        <p class="font-bold text-3xl text-gray-800 mb-1">—</p>
        <p class="text-sm font-medium text-gray-500">Jumlah Pelamar</p>
    </div>

    {{-- Card 3: Pelamar Diterima --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md sm:col-span-2 lg:col-span-1">
        <p class="font-bold text-3xl text-gray-800 mb-1">—</p>
        <p class="text-sm font-medium text-gray-500">Jumlah Pelamar Diterima</p>
    </div>

</div>

{{-- ── Statistics Section ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Chart Area (spans 2 cols) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-[0.95rem] font-bold text-gray-800">Statistik Pelamar</h2>
                <p class="text-xs text-gray-400 mt-0.5">Jumlah pelamar mendaftar per bulan</p>
            </div>
            {{-- Legend --}}
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-[#8b1515] inline-block"></span>Lamaran masuk
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>Diterima
                </span>
            </div>
        </div>

        {{-- Placeholder bar chart --}}
        <div class="flex items-end justify-between gap-2 h-44 px-2">
            @php
                $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt'];
                $bars   = [45, 62, 38, 74, 55, 88, 67, 50];
                $accepted = [10, 20, 15, 30, 22, 35, 28, 18];
            @endphp
            @foreach($months as $i => $month)
            <div class="flex flex-col items-center gap-1 flex-1">
                <div class="w-full flex flex-col items-center gap-0.5">
                    {{-- lamaran bar --}}
                    <div class="w-4/5 rounded-t-md bg-[#8b1515]/80 transition-all duration-500"
                         style="height: {{ $bars[$i] * 1.5 }}px; opacity: 0.85;"></div>
                    {{-- diterima overlay tiny bar below --}}
                </div>
                <span class="text-[0.6rem] text-gray-400 font-medium">{{ $month }}</span>
            </div>
            @endforeach
        </div>

        {{-- Y-axis hint --}}
        <div class="mt-3 border-t border-gray-100 pt-3 flex items-center justify-between text-[0.65rem] text-gray-400 px-1">
            <span>*Data dummy — akan terhubung database</span>
            <span class="text-[#8b1515] font-semibold cursor-pointer hover:underline text-xs">Lihat detail →</span>
        </div>
    </div>

    {{-- Right Column: Status Donut + Ringkasan --}}
    <div class="flex flex-col gap-5">

        {{-- Status Donut --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex-1">
            <h3 class="text-[0.875rem] font-bold text-gray-800 mb-4">Status Lamaran</h3>

            {{-- Fake Donut (pure CSS) --}}
            <div class="flex items-center justify-center py-2">
                <div class="relative w-32 h-32">
                    <svg viewBox="0 0 36 36" class="w-32 h-32 -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f3f4f6" stroke-width="3.5"/>
                        {{-- Menunggu (gray) --}}
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3.5" stroke-dasharray="35 65" stroke-dashoffset="0"/>
                        {{-- Proses (blue) --}}
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#3b82f6" stroke-width="3.5" stroke-dasharray="30 70" stroke-dashoffset="-35"/>
                        {{-- Diterima (green) --}}
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3.5" stroke-dasharray="20 80" stroke-dashoffset="-65"/>
                        {{-- Ditolak (red) --}}
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#ef4444" stroke-width="3.5" stroke-dasharray="15 85" stroke-dashoffset="-85"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-bold text-gray-800">—</span>
                        <span class="text-[0.6rem] text-gray-400">total</span>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="space-y-2 mt-3">
                @php $statusItems = [
                    ['label' => 'Menunggu',  'color' => 'bg-gray-300'],
                    ['label' => 'Proses',    'color' => 'bg-blue-500'],
                    ['label' => 'Diterima',  'color' => 'bg-emerald-500'],
                    ['label' => 'Ditolak',   'color' => 'bg-red-500'],
                ]; @endphp
                @foreach($statusItems as $s)
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $s['color'] }} inline-block"></span>
                        {{ $s['label'] }}
                    </div>
                    <span class="font-semibold text-gray-700">—</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Cepat --}}
        <div class="bg-gradient-to-br from-[#8b1515] to-[#6e1010] rounded-2xl shadow-sm p-5 text-white">
            <p class="text-[0.7rem] uppercase tracking-widest text-white/60 font-semibold mb-1">Rekrutmen Aktif</p>
            <p class="text-2xl font-bold mb-3">Periode 2025</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-white/70">Lowongan dibuka</span>
                    <span class="font-semibold">—</span>
                </div>
                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-white/70">Pelamar aktif</span>
                    <span class="font-semibold">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/70">Tingkat penerimaan</span>
                    <span class="font-semibold">—%</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
