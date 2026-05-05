@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- ── Quick Stats ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-7">

    {{-- Card 1: Jumlah Lowongan --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
        <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($totalLowongan) }}</p>
        <p class="text-sm font-medium text-gray-500">Jumlah Lowongan</p>
    </div>

    {{-- Card 2: Jumlah Pelamar --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
        <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($totalPelamar) }}</p>
        <p class="text-sm font-medium text-gray-500">Jumlah Pelamar</p>
    </div>

    {{-- Card 3: Pelamar Diterima --}}
    <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md sm:col-span-2 lg:col-span-1">
        <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($totalDiterima) }}</p>
        <p class="text-sm font-medium text-gray-500">Jumlah Pelamar Diterima</p>
    </div>

</div>

{{-- ── Statistics Section ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Chart Area (spans 2 cols) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-[0.95rem] font-bold text-gray-800">Statistik Pelamar Tahun {{ $currentYear }}</h2>
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

        {{-- Dynamic bar chart --}}
        <div class="flex items-end justify-between gap-1 sm:gap-2 h-44 px-1 sm:px-2">
            @foreach($chartData as $data)
                @php
                    $hLamaran = ($data['lamaran'] / $maxChartValue) * 100; // Percentage of max container height
                    $hDiterima = $data['lamaran'] > 0 ? ($data['diterima'] / $maxChartValue) * 100 : 0;
                @endphp
                <div class="flex flex-col items-center gap-1 flex-1 group relative">
                    {{-- Tooltip --}}
                    <div class="absolute bottom-[calc(100%+0.5rem)] left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[0.65rem] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                        Masuk: {{ $data['lamaran'] }}<br>Diterima: {{ $data['diterima'] }}
                    </div>

                    <div class="w-full flex flex-col justify-end items-center h-full relative">
                        {{-- lamaran bar --}}
                        <div class="w-[70%] sm:w-4/5 rounded-t-md bg-[#8b1515] transition-all duration-500 hover:opacity-90 relative overflow-hidden"
                             style="height: {{ max($hLamaran, 1) }}%;">
                             
                             {{-- diterima bar (stacked inside or overlaid at bottom) --}}
                             @if($hDiterima > 0)
                                <div class="absolute bottom-0 left-0 w-full bg-emerald-400 transition-all duration-500" style="height: {{ ($data['diterima'] / $data['lamaran']) * 100 }}%"></div>
                             @endif
                        </div>
                    </div>
                    <span class="text-[0.55rem] sm:text-[0.6rem] text-gray-400 font-medium">{{ $data['month'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Y-axis hint --}}
        <div class="mt-3 border-t border-gray-100 pt-3 flex items-center justify-between text-[0.65rem] text-gray-400 px-1">
            <span>*Data terhubung langsung ke database</span>
            <a href="{{ route('admin.pelamar.index') }}" class="text-[#8b1515] font-semibold cursor-pointer hover:underline text-xs">Lihat detail pelamar →</a>
        </div>
    </div>

    {{-- Right Column: Status Donut + Ringkasan --}}
    <div class="flex flex-col gap-5">

        {{-- Status Donut --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex-1">
            <h3 class="text-[0.875rem] font-bold text-gray-800 mb-4">Status Lamaran</h3>

            @php
                $t = $statusData['total'] ?: 1; // prevent div/0
                $pMenunggu = ($statusData['menunggu'] / $t) * 100;
                $pProses = ($statusData['proses'] / $t) * 100;
                $pDiterima = ($statusData['diterima'] / $t) * 100;
                $pDitolak = ($statusData['ditolak'] / $t) * 100;

                $offMenunggu = 0;
                $offProses = -($pMenunggu);
                $offDiterima = -($pMenunggu + $pProses);
                $offDitolak = -($pMenunggu + $pProses + $pDiterima);
            @endphp

            <div class="flex items-center justify-center py-2 relative group cursor-pointer">
                {{-- Tooltip for Donut --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[0.65rem] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                    Menunggu: {{ $statusData['menunggu'] }} | Proses: {{ $statusData['proses'] }}<br>Diterima: {{ $statusData['diterima'] }} | Ditolak: {{ $statusData['ditolak'] }}
                </div>

                <div class="relative w-32 h-32">
                    <svg viewBox="0 0 36 36" class="w-32 h-32 -rotate-90">
                        <circle cx="18" cy="18" r="15.915494309" fill="none" stroke="#f3f4f6" stroke-width="3.5"/>
                        @if($statusData['total'] > 0)
                            {{-- Menunggu (gray) --}}
                            @if($pMenunggu > 0)
                                <circle cx="18" cy="18" r="15.915494309" fill="none" stroke="#e5e7eb" stroke-width="3.5" stroke-dasharray="{{ $pMenunggu }} {{ 100 - $pMenunggu }}" stroke-dashoffset="{{ $offMenunggu }}"/>
                            @endif
                            {{-- Proses (blue) --}}
                            @if($pProses > 0)
                                <circle cx="18" cy="18" r="15.915494309" fill="none" stroke="#3b82f6" stroke-width="3.5" stroke-dasharray="{{ $pProses }} {{ 100 - $pProses }}" stroke-dashoffset="{{ $offProses }}"/>
                            @endif
                            {{-- Diterima (green) --}}
                            @if($pDiterima > 0)
                                <circle cx="18" cy="18" r="15.915494309" fill="none" stroke="#10b981" stroke-width="3.5" stroke-dasharray="{{ $pDiterima }} {{ 100 - $pDiterima }}" stroke-dashoffset="{{ $offDiterima }}"/>
                            @endif
                            {{-- Ditolak (red) --}}
                            @if($pDitolak > 0)
                                <circle cx="18" cy="18" r="15.915494309" fill="none" stroke="#ef4444" stroke-width="3.5" stroke-dasharray="{{ $pDitolak }} {{ 100 - $pDitolak }}" stroke-dashoffset="{{ $offDitolak }}"/>
                            @endif
                        @endif
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-bold text-gray-800">{{ number_format($statusData['total']) }}</span>
                        <span class="text-[0.6rem] text-gray-400">total</span>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="space-y-2 mt-3">
                @php $statusItems = [
                    ['label' => 'Menunggu',  'color' => 'bg-gray-300', 'count' => $statusData['menunggu']],
                    ['label' => 'Proses',    'color' => 'bg-blue-500', 'count' => $statusData['proses']],
                    ['label' => 'Diterima',  'color' => 'bg-emerald-500', 'count' => $statusData['diterima']],
                    ['label' => 'Ditolak',   'color' => 'bg-red-500', 'count' => $statusData['ditolak']],
                ]; @endphp
                @foreach($statusItems as $s)
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $s['color'] }} inline-block"></span>
                        {{ $s['label'] }}
                    </div>
                    <span class="font-semibold text-gray-700">{{ number_format($s['count']) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan Cepat --}}
        <div class="bg-gradient-to-br from-[#8b1515] to-[#6e1010] rounded-2xl shadow-sm p-5 text-white">
            <p class="text-[0.7rem] uppercase tracking-widest text-white/60 font-semibold mb-1">Rekrutmen Aktif</p>
            <p class="text-2xl font-bold mb-3">Periode {{ $currentYear }}</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-white/70">Lowongan dibuka</span>
                    <span class="font-semibold">{{ number_format($activeLowongan) }}</span>
                </div>
                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-white/70">Total lamaran</span>
                    <span class="font-semibold">{{ number_format($totalLamaran) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/70">Tingkat penerimaan</span>
                    <span class="font-semibold">{{ $acceptanceRate }}%</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
