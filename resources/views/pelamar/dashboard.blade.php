@extends('layouts.admin')

@section('title', 'Dashboard Pelamar')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Welcome Card --}}

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
       
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($totalLamaran) }}</p>
            <p class="text-sm font-medium text-gray-500">Total Lamaran</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($lamaranAktif) }}</p>
            <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($lamaranDiterima) }}</p>
            <p class="text-sm font-medium text-gray-500">Diterima</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ number_format($lamaranDitolak) }}</p>
            <p class="text-sm font-medium text-gray-500">Ditolak</p>
        </div>
    </div>

    {{-- Lamaran Terakhir --}}
    <div>
        <div class="flex items-center justify-between mb-5 px-1">
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Lamaran Terakhir</h3>
            <a href="{{ route('pelamar.history.index') }}" class="text-xs font-bold text-[#8b1515] hover:underline transition-colors">Lihat Semua</a>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-[#8b1515]">
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-red-200 uppercase tracking-widest whitespace-nowrap w-[40%]">Posisi & Prodi</th>
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-red-200 uppercase tracking-widest whitespace-nowrap w-[30%]">Tanggal</th>
                            <th class="py-4 px-6 text-[0.65rem] font-bold text-red-200 uppercase tracking-widest whitespace-nowrap w-[30%]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLamarans as $lamaran)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-5 px-6">
                                <div class="text-sm font-bold text-gray-800">{{ $lamaran->lowongan->nama_posisi }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</div>
                            </td>
                            <td class="py-5 px-6">
                                <div class="text-sm font-semibold text-gray-700">{{ $lamaran->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $lamaran->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-5 px-6">
                                @php
                                    $statusColors = [
                                        'menunggu'       => 'bg-gray-50 text-gray-600 border-gray-200',
                                        'seleksi_tahap1' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'seleksi_tahap2' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                        'diterima'       => 'bg-green-50 text-green-700 border-green-100',
                                        'ditolak'        => 'bg-red-50 text-red-700 border-red-100',
                                    ];
                                    $colorClass = $statusColors[$lamaran->status] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                                @endphp
                                <span class="inline-flex px-3 py-1.5 rounded-lg border text-[0.65rem] font-bold uppercase tracking-widest {{ $colorClass }}">
                                    {{ $lamaran->status_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-16 px-6 text-center">
                                <p class="text-gray-400 text-sm font-medium mb-5">Belum ada aktivitas lamaran.</p>
                                <a href="{{ route('pelamar.lowongan.index') }}" class="inline-flex px-6 py-3 bg-[#8b1515] text-white font-bold text-sm rounded-xl hover:bg-[#7a1111] transition-colors shadow-sm">
                                    Cari Lowongan
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Profile Completeness Modal --}}
@if($showProfileModal && count($incompleteSections) > 0)
<div x-data="{ showModal: true }" 
     x-show="showModal" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;">
     
    <div class="w-full max-w-sm overflow-hidden shadow-2xl rounded-2xl"
         @click.away="showModal = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        {{-- Title Bar --}}
        <div class="bg-[#e8e8e8] px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <span class="text-sm font-bold text-gray-600 tracking-tight">Reminder</span>
            <div class="flex items-center gap-1.5">
                <button @click="showModal = false" class="w-3.5 h-3.5 rounded-full bg-[#ff5f57] hover:brightness-90 transition-all border border-[#e0443e] cursor-pointer"></button>
                <div class="w-3.5 h-3.5 rounded-full bg-[#febc2e] border border-[#d4a017]"></div>
                <div class="w-3.5 h-3.5 rounded-full bg-[#28c840] border border-[#1aab29]"></div>
            </div>
        </div>

        {{-- Body --}}
        <div class="bg-[#f2f2f2] px-6 py-8">
            <h3 class="text-base font-bold text-gray-800 mb-1">Profil belum lengkap</h3>
            <p class="text-xs text-gray-500 font-medium mb-6">Lengkapi data berikut sebelum melamar lowongan.</p>

            <div class="bg-white rounded-xl border border-gray-200 mb-8 overflow-hidden">
                @foreach($incompleteSections as $section)
                <div class="flex items-center p-4 border-b border-gray-100 last:border-0">
                    <span class="text-sm font-semibold text-gray-700">{{ $section }}</span>
                </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="flex justify-center">
                <a href="{{ route('pelamar.profil.index') }}" class="w-full sm:w-auto px-10 py-2.5 bg-white text-gray-700 text-sm font-bold rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-[#8b1515] hover:border-gray-300 transition-all shadow-sm text-center">Lengkapi Sekarang</a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
