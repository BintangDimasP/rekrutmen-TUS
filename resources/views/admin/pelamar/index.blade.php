@extends('layouts.admin')

@section('title', 'Manajemen Pelamar')

@section('content')

    {{-- Toast Notification --}}
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

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('admin.pelamar.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            {{-- Left: Filter Lowongan --}}
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
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
                @if(request()->filled('lowongan_id') || request()->filled('search'))
                    <a href="{{ route('admin.pelamar.index') }}" class="text-xs text-red-600 hover:underline">Reset</a>
                @endif
            </div>

            {{-- Right: Search --}}
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelamar atau no hp..." 
                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                <button type="submit" class="hidden"></button>
            </div>
        </form>
    </div>

    {{-- Daftar Pelamar Global Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[220px]">Nama Pelamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Jenjang Pendidikan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">No Handphone</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Lamaran Diajukan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelamars as $pelamar)
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
                            @if($pelamar->lamarans->count() > 0)
                                <div class="flex flex-col gap-1">
                                    @foreach($pelamar->lamarans->take(2) as $lamaran)
                                        <span class="inline-flex items-center gap-1 text-xs text-[#8b1515] font-semibold">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $lamaran->lowongan->nama_posisi ?? '-' }}
                                        </span>
                                    @endforeach
                                    @if($pelamar->lamarans->count() > 2)
                                        <span class="text-xs text-gray-400">+{{ $pelamar->lamarans->count() - 2 }} lainnya</span>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-gray-100 text-gray-500 border-gray-200">
                                    Belum Melamar
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pelamar.show', $pelamar) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Detail & Edit Pelamar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
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
                                <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Pelamar Terdaftar</h3>
                                <p class="text-gray-400 text-xs">Semua pelamar yang telah melakukan registrasi akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
            <span>Total: <strong>{{ $pelamars->count() }}</strong> pelamar terdaftar</span>
        </div>
    </div>

</div>

@endsection

