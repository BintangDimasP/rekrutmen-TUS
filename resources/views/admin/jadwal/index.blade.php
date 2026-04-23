@extends('layouts.admin')

@section('title', 'Jadwal Wawancara & Micro Teaching')

@section('content')

    {{-- Toast --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px] max-w-sm">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-1 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Jadwal Wawancara & Micro Teaching</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua jadwal wawancara dan micro teaching yang telah dibuat.</p>
        </div>
        <a href="{{ route('admin.jadwal.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors self-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Jadwalkan Seleksi
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Filter Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                       class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Filter Penguji</label>
                <select name="penguji_id"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white min-w-[200px]">
                    <option value="">Semua Penguji</option>
                    @foreach($pengujis as $p)
                        <option value="{{ $p->id }}" {{ request('penguji_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">Filter</button>
                @if(request()->hasAny(['tanggal', 'penguji_id']))
                    <a href="{{ route('admin.jadwal.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-800">Daftar Jadwal</h2>
                <p class="text-xs text-gray-500 mt-0.5">Total <strong>{{ $jadwals->count() }}</strong> jadwal ditemukan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Tanggal</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Tipe & Sesi</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Penguji</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Pelamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Lowongan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-5">
                            <div class="text-sm font-bold text-gray-800">{{ $jadwal->tanggal->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $jadwal->tanggal->translatedFormat('l') }}</div>
                        </td>
                        <td class="py-3 px-5">
                            @php
                                $isTahap1 = $jadwal->tipe_seleksi === 'tahap1';
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[0.7rem] font-bold border
                                {{ $isTahap1 ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' }}">
                                {{ $isTahap1 ? '🎙 Wawancara' : '🏫 Micro Teaching' }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">{{ $jadwal->session_label }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm font-semibold text-gray-800">{{ $jadwal->penguji->nama }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $jadwal->penguji->kode }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm font-semibold text-gray-800">{{ $jadwal->pelamar->nama }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $jadwal->pelamar->user?->email ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-[#8b1515] font-semibold">{{ $jadwal->lowongan->nama_posisi }}</div>
                            <div class="text-xs text-gray-400">{{ $jadwal->lowongan->prodi->nama ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex justify-end">
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $jadwal) }}"
                                      onsubmit="return confirm('Hapus jadwal seleksi {{ addslashes($jadwal->pelamar->nama ?? '') }} — {{ $jadwal->session_label }}?')"
                                      class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus Jadwal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Jadwal</h3>
                                <p class="text-gray-400 text-xs">Buat jadwal seleksi pertama dengan mengklik tombol di atas.</p>
                                <a href="{{ route('admin.jadwal.create') }}" class="mt-1 px-4 py-2 bg-[#8b1515] text-white text-xs font-semibold rounded-lg hover:bg-red-900 transition-colors">
                                    + Jadwalkan Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
