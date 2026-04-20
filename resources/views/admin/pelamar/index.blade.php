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

<div class="max-w-6xl mx-auto">

    {{-- Header Content --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pelamar Global</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar seluruh berkas lamaran dari pelamar di berbagai lowongan aktif maupun ditutup.</p>
        </div>
    </div>

    {{-- Daftar Pelamar Global Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" placeholder="Cari nama pelamar atau no hp..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter Lowongan
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[200px]">Nama Pelamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Lowongan Diajukan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Jenjang Pendidikan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">No Handphone</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Status</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lamarans as $lamaran)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-5">
                            <div class="text-sm font-semibold text-gray-800">{{ $lamaran->pelamar->nama }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $lamaran->pelamar->user?->email }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-[#8b1515] font-semibold">{{ $lamaran->lowongan->nama_posisi }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-700 font-medium">{{ $lamaran->pelamar->jenjang }}</div>
                            <div class="text-[0.7rem] text-gray-400 uppercase tracking-widest mt-0.5">{{ $lamaran->pelamar->prodi_pendidikan }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <span class="text-sm text-gray-600 font-mono">{{ $lamaran->pelamar->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-5">
                            @php
                                $statusColors = [
                                    'menunggu'       => 'bg-gray-100 text-gray-600 border-gray-200',
                                    'seleksi_tahap1' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'seleksi_tahap2' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'diterima'       => 'bg-green-50 text-green-700 border-green-200',
                                    'ditolak'        => 'bg-red-50 text-red-700 border-red-200',
                                ];
                                $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border {{ $colorClass }}">
                                {{ $lamaran->status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.lamaran.show', $lamaran) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Lihat Detail Lamaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.lamaran.destroy', $lamaran) }}"
                                      onsubmit="return confirm('Menghapus lamaran atas nama {{ addslashes($lamaran->pelamar->nama) }} pada posisi {{ addslashes($lamaran->lowongan->nama_posisi) }}? Data berkasnya tetap ada di akun pelamar.')" class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus Lamaran">
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
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Pelamar Sama Sekali</h3>
                                <p class="text-gray-400 text-xs">Semua data lamaran dari berbagai lowongan akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
            <span>Total: <strong>{{ $lamarans->count() }}</strong> berkas lamaran</span>
        </div>
    </div>

</div>

@endsection
