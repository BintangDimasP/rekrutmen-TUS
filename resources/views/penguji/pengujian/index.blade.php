@extends('layouts.admin')

@section('title', 'Daftar Pengujian')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Pengujian</h1>
            <p class="text-sm text-gray-500 mt-1">Berikut adalah daftar pelamar yang ditugaskan kepada Anda untuk diuji.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{ tab: 'all' }">
        <!-- Tabs -->
        <div class="flex gap-6 mb-4 border-b border-gray-200 px-2">
            <button @click="tab = 'all'" 
                    :class="tab === 'all' ? 'border-[#8b1515] text-[#8b1515]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                    class="pb-3 text-sm font-bold border-b-2 transition-colors">
                Semua Jadwal
            </button>
            <button @click="tab = 'wawancara'" 
                    :class="tab === 'wawancara' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                    class="pb-3 text-sm font-bold border-b-2 transition-colors">
                Wawancara
            </button>
            <button @click="tab = 'micro'" 
                    :class="tab === 'micro' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                    class="pb-3 text-sm font-bold border-b-2 transition-colors">
                Micro Teaching
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" style="min-width: 900px;">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap">Tanggal & Waktu</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap">Tipe Seleksi</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap">Pelamar</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap">Lowongan</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center">Status Nilai</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwals as $jadwal)
                        @php
                            $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
                            $sudahDinilai = $jadwal->penilaian !== null;
                        @endphp
                        <tr x-show="tab === 'all' || tab === '{{ $jadwal->tipe_seleksi == 'tahap1' ? 'wawancara' : 'micro' }}'" 
                            class="hover:bg-gray-50/60 transition-colors align-middle">
                            
                            {{-- Tanggal & Waktu --}}
                            <td class="py-4 px-4 align-top">
                                <div class="text-sm font-bold text-gray-800">{{ $jadwal->tanggal->format('d M Y') }}</div>
                                <div class="text-[0.7rem] font-medium text-gray-500 mt-1">
                                    <span class="inline-flex items-center gap-1 bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $sesiInfo ? $sesiInfo['start'] . ' - ' . $sesiInfo['end'] : 'Sesi ' . $jadwal->sesi }}
                                    </span>
                                </div>
                            </td>

                            {{-- Tipe Seleksi --}}
                            <td class="py-4 px-4 align-top">
                                @if($jadwal->tipe_seleksi == 'tahap1')
                                    <span class="inline-flex px-2 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded border border-blue-200">Wawancara</span>
                                @else
                                    <span class="inline-flex px-2 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded border border-purple-200">Micro Teaching</span>
                                @endif
                                @if($jadwal->link_meeting)
                                    <div class="mt-2">
                                        <a href="{{ $jadwal->link_meeting }}" target="_blank" class="inline-flex items-center gap-1 text-[0.65rem] font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            Link Zoom
                                        </a>
                                    </div>
                                @endif
                            </td>

                            {{-- Pelamar --}}
                            <td class="py-4 px-4 align-top">
                                <div class="text-sm font-bold text-gray-800">{{ $jadwal->pelamar->nama }}</div>
                                <div class="text-[0.75rem] text-gray-500 font-mono mt-1">{{ $jadwal->pelamar->user?->email ?? '-' }}</div>
                            </td>

                            {{-- Lowongan --}}
                            <td class="py-4 px-4 align-top">
                                <div class="text-sm font-bold text-[#8b1515]">{{ $jadwal->lowongan->nama_posisi }}</div>
                                <div class="text-[0.75rem] font-medium text-gray-500 mt-1">{{ $jadwal->lowongan->prodi->nama ?? '-' }}</div>
                            </td>

                            {{-- Status Nilai --}}
                            <td class="py-4 px-4 align-top text-center">
                                @if($sudahDinilai)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded border border-green-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Selesai ({{ $jadwal->penilaian->total_nilai }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded border border-yellow-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-4 align-top text-center">
                                <a href="{{ route('penguji.pengujian.show', $jadwal->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail & Uji
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    <p class="text-sm font-medium">Belum ada jadwal pengujian untuk Anda saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
@endsection
