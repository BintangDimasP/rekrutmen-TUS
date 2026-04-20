@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

@section('content')

    {{-- Toast --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Lowongan</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola semua posisi lowongan dosen yang tersedia di sistem.</p>
            </div>
            <a href="{{ route('admin.lowongan.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Lowongan
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Toolbar --}}
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" placeholder="Cari nama posisi..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 inline-block"></span>Aktif
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block ml-2"></span>Draft
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-block ml-2"></span>Ditutup
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Posisi / Prodi</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Persyaratan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Kuota</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Ditutup</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lowongans as $lowongan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5">
                                <div class="text-sm font-semibold text-gray-800">{{ $lowongan->nama_posisi }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $lowongan->prodi->nama ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex flex-wrap gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-indigo-50 text-indigo-700">{{ $lowongan->jenjang_minimal }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-gray-100 text-gray-600">IPK ≥ {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                                </div>
                                @if($lowongan->skill_dibutuhkan)
                                    <div class="text-xs text-gray-400 mt-1">{{ $lowongan->skill_dibutuhkan }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-center">
                                <div class="text-sm font-bold text-gray-800">{{ $lowongan->sisa_kuota }}</div>
                                <div class="text-[0.65rem] text-gray-400">dari {{ $lowongan->kuota }}</div>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#8b1515]/10 text-[#8b1515] text-sm font-bold">
                                    {{ $lowongan->lamarans->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600 whitespace-nowrap">
                                {{ $lowongan->tanggal_tutup->format('d M Y') }}
                                @if($lowongan->tanggal_tutup->isPast())
                                    <div class="text-[0.65rem] text-red-500 font-medium">Sudah lewat</div>
                                @else
                                    <div class="text-[0.65rem] text-green-600 font-medium">{{ $lowongan->tanggal_tutup->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-5">
                                @if($lowongan->status === 'aktif')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Aktif
                                    </span>
                                @elseif($lowongan->status === 'draft')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-amber-100 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-gray-100 text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>Ditutup
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.lowongan.show', $lowongan) }}" class="text-gray-400 hover:text-blue-600 transition-colors p-1.5 rounded" title="Lihat Pelamar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.lowongan.edit', $lowongan) }}" class="text-gray-400 hover:text-amber-600 transition-colors p-1.5 rounded" title="Edit Lowongan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.lowongan.destroy', $lowongan) }}"
                                          onsubmit="return confirm('Hapus lowongan {{ addslashes($lowongan->nama_posisi) }}?')" class="inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1.5 rounded" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706"/></svg>
                                    </div>
                                    <h3 class="text-gray-700 font-semibold text-sm">Belum ada lowongan</h3>
                                    <p class="text-gray-400 text-xs">Buat lowongan pertama dengan menekan tombol "Tambah Lowongan".</p>
                                    <a href="{{ route('admin.lowongan.create') }}" class="mt-1 px-4 py-2 bg-[#8b1515] text-white text-xs font-semibold rounded-lg hover:bg-red-900 transition-colors">
                                        + Tambah Lowongan
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 text-xs text-gray-500">
                Total: <strong>{{ $lowongans->count() }}</strong> lowongan
                &bull; Aktif: <strong>{{ $lowongans->where('status', 'aktif')->count() }}</strong>
                &bull; Draft: <strong>{{ $lowongans->where('status', 'draft')->count() }}</strong>
            </div>
        </div>
    </div>

@endsection
