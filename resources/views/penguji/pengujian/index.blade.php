@extends('layouts.admin')

@section('title', 'Daftar Pengujian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl shadow-black/5 border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white shadow-inner">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">{{ session('success') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div x-data="{ tab: 'all' }">
        <!-- Filter Dropdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <select x-model="tab" class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="all">Semua Tipe Seleksi</option>
                        <option value="wawancara">Wawancara</option>
                        <option value="micro">Micro Teaching</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#8b1515] text-white">
                        <tr>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Tanggal</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Waktu</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Seleksi</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jadwals as $jadwal)
                            @php
                                $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
                                $sudahDinilai = $jadwal->penilaian !== null;
                            @endphp
                            <tr x-show="tab === 'all' || tab === '{{ $jadwal->tipe_seleksi == 'tahap1' ? 'wawancara' : 'micro' }}'" 
                                class="hover:bg-gray-50/50 transition-colors">
                                
                                {{-- Tanggal --}}
                                <td class="py-3 px-5 text-sm font-semibold text-gray-800">{{ $jadwal->tanggal->format('d/m/Y') }}</td>
                                
                                {{-- Waktu --}}
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">Sesi {{ $jadwal->sesi }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $sesiInfo ? $sesiInfo['start'] . ' - ' . $sesiInfo['end'] : '-' }}</div>
                                </td>

                                {{-- Seleksi --}}
                                <td class="py-3 px-5 text-sm font-semibold text-gray-700">
                                    {{ $jadwal->tipe_seleksi == 'tahap1' ? 'Wawancara' : 'Micro Teaching' }}
                                </td>

                                {{-- Pelamar --}}
                                <td class="py-3 px-5 text-sm font-semibold text-gray-800">{{ $jadwal->pelamar->nama }}</td>

                                {{-- Lowongan --}}
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $jadwal->lowongan->nama_posisi }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase tracking-widest mt-0.5">{{ $jadwal->lowongan->prodi->nama ?? '-' }}</div>
                                </td>

                                {{-- Status --}}
                                <td class="py-3 px-5 text-center">
                                    @if($sudahDinilai)
                                        <span class="text-sm font-bold text-green-600">Dinilai</span>
                                    @else
                                        <span class="text-sm font-bold text-yellow-600">Pending</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3 px-5 text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('penguji.pengujian.show', $jadwal->id) }}" class="p-1.5 text-gray-400 hover:text-[#8b1515] transition-colors" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 px-6 text-center">
                                    <p class="text-gray-400 text-sm font-medium mb-5">Belum Ada Pengujian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
                <span>Total: <strong>{{ $jadwals->count() }}</strong> jadwal pengujian</span>
            </div>
        </div>
    </div>
</div>
@endsection
