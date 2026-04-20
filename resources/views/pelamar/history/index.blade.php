@extends('layouts.admin')

@section('title', 'Histori Lamaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Notification --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800">Berhasil</h4>
                <p class="text-[0.75rem] text-gray-500">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Histori Lamaran</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau perkembangan status pendaftaran Anda di berbagai posisi secara berkala.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold uppercase tracking-widest">{{ $lamarans->count() }} Lamaran</span>
        </div>
    </div>

    {{-- Table History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Posisi & Prodi</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Tanggal Melamar</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($lamarans as $lamaran)
                    <tr class="hover:bg-gray-50/30 transition-colors group">
                        <td class="py-5 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 group-hover:bg-[#8b1515]/5 group-hover:text-[#8b1515] flex items-center justify-center transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800">{{ $lamaran->lowongan->nama_posisi }}</h4>
                                    <p class="text-xs text-gray-400">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-5 px-6">
                            <span class="text-[0.8rem] text-gray-600 font-medium">{{ $lamaran->created_at->format('d M Y') }}</span>
                            <div class="text-[0.65rem] text-gray-400">{{ $lamaran->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="py-5 px-6 text-center">
                            @php
                                $statusColors = [
                                    'menunggu'       => 'bg-gray-100 text-gray-500 border-gray-200',
                                    'seleksi_tahap1' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'seleksi_tahap2' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    'diterima'       => 'bg-green-50 text-green-600 border-green-100',
                                    'ditolak'        => 'bg-red-50 text-red-600 border-red-100',
                                ];
                                $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border {{ $colorClass }}">
                                {{ $lamaran->status_label }}
                            </span>
                        </td>
                        <td class="py-5 px-6 text-right">
                            <button class="px-4 py-1.5 border border-gray-100 text-gray-400 font-bold text-[0.7rem] rounded-lg group-hover:border-[#8b1515] group-hover:text-[#8b1515] transition-all">Lihat Detail</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center space-y-4">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-200">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="text-sm font-bold text-gray-400">Belum ada histori pendaftaran</div>
                            <a href="{{ route('pelamar.lowongan.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#8b1515] text-white text-xs font-bold rounded-xl shadow-md shadow-[#8b1515]/20">Ayo Mulai Melamar</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
