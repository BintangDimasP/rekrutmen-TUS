@extends('layouts.admin')

@section('title', 'Daftar Pelamar — ' . auth()->user()->prodi?->nama)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('kaprodi.pelamar.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            {{-- Left: Filter Lowongan & Status --}}
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Filter Lowongan --}}
                <div class="relative w-full sm:w-56">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <select name="lowongan_id" onchange="this.form.submit()"
                            class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="">Semua Lowongan</option>
                        @foreach($lowongans as $low)
                            <option value="{{ $low->id }}" {{ request('lowongan_id') == $low->id ? 'selected' : '' }}>{{ $low->nama_posisi }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="relative w-full sm:w-48">
                    <select name="status" onchange="this.form.submit()"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="seleksi_tahap1" {{ request('status') == 'seleksi_tahap1' ? 'selected' : '' }}>Seleksi Tahap 1</option>
                        <option value="seleksi_tahap2" {{ request('status') == 'seleksi_tahap2' ? 'selected' : '' }}>Seleksi Tahap 2</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                @if(request()->filled('lowongan_id') || request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('kaprodi.pelamar.index') }}" class="text-xs text-red-600 hover:underline">Reset</a>
                @endif
            </div>

            {{-- Right: Search --}}
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau no hp..."
                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                <button type="submit" class="hidden"></button>
            </div>
        </form>
    </div>

    {{-- Tabel Pelamar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Nama Pelamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Jenjang Pendidikan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">No Handphone</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Lowongan Dilamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelamars as $pelamar)
                    @php
                        // Ambil hanya lamaran di prodi ini
                        $lamaransProdi = $pelamar->lamarans;
                        $statusColors = [
                            'menunggu'       => 'text-gray-500',
                            'seleksi_tahap1' => 'text-blue-600',
                            'seleksi_tahap2' => 'text-indigo-600',
                            'diterima'       => 'text-green-600',
                            'ditolak'        => 'text-red-600',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-5">
                            <div class="text-sm font-semibold text-gray-800">{{ $pelamar->nama }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $pelamar->user?->email }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-700 font-medium">{{ $pelamar->jenjang ?? '-' }}</div>
                            <div class="text-[0.7rem] text-gray-400 uppercase tracking-widest mt-0.5">{{ $pelamar->prodi_pendidikan ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <span class="text-sm text-gray-600 font-mono">{{ $pelamar->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-5">
                            @if($lamaransProdi->count() > 0)
                                <div class="flex flex-col gap-1">
                                    @foreach($lamaransProdi->take(2) as $lamaran)
                                        <span class="inline-flex items-center gap-1 text-xs text-[#8b1515] font-semibold">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $lamaran->lowongan->nama_posisi ?? '-' }}
                                        </span>
                                    @endforeach
                                    @if($lamaransProdi->count() > 2)
                                        <span class="text-xs text-gray-400">+{{ $lamaransProdi->count() - 2 }} lainnya</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-5 text-center">
                            @foreach($lamaransProdi->take(1) as $lamaran)
                                @php $color = $statusColors[$lamaran->status] ?? 'text-gray-500'; @endphp
                                <span class="text-sm font-bold {{ $color }}">{{ $lamaran->status_label }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Pelamar</h3>
                                <p class="text-gray-400 text-xs">Belum ada pelamar yang mendaftar ke lowongan di prodi Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
            <span>Total: <strong>{{ $pelamars->count() }}</strong> pelamar di prodi {{ auth()->user()->prodi?->nama }}</span>
        </div>
    </div>

</div>
@endsection
