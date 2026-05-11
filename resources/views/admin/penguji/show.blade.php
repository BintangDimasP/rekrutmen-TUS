@extends('layouts.admin')

@section('title', 'Detail Penguji — ' . $penguji->nama)

@section('content')



<div class="max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.penguji.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Penguji</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $penguji->nama }}</span>
    </div>

    {{-- Card Profil Penguji --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-[#8b1515] px-6 py-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $penguji->nama }}</h2>
                <p class="text-white/70 text-sm mt-0.5">{{ $penguji->kode }} &bull; {{ $penguji->prodi?->nama ?? 'Prodi tidak diketahui' }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white border border-white/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-300"></span>PENGUJI AKTIF
                </span>
                @if($penguji->is_kaprodi)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-400/90 text-amber-900">
                        Merangkap Kaprodi
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- NIP --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">NIP</p>
                <p class="text-sm font-semibold text-gray-800 font-medium">{{ $penguji->nip ?? '—' }}</p>
            </div>

            {{-- NIDN --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">NIDN</p>
                <p class="text-sm font-semibold text-gray-800 font-medium">{{ $penguji->nidn ?? '—' }}</p>
            </div>

            {{-- Email --}}
            <div class="sm:col-span-2">
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Email</p>
                <p class="text-sm font-semibold text-gray-800 font-medium">{{ $pengujiEmail ?? '—' }}</p>
            </div>

            {{-- Prodi --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Homebase Prodi</p>
                <p class="text-sm font-semibold text-gray-800">{{ $penguji->prodi?->nama ?? '—' }}</p>
            </div>

            {{-- Kode --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Kode Dosen</p>
                <p class="text-sm font-semibold text-gray-800 font-medium">{{ $penguji->kode }}</p>
            </div>
        </div>
    </div>

    {{-- Danger Zone: Cabut Status --}}
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-50 bg-red-50/50">
            <h3 class="text-base font-bold text-red-800">Kelola Status Penguji</h3>
            <p class="text-xs text-red-600 mt-0.5">Mencabut status penguji akan menghapus akses login penguji ini (kecuali ia juga Kaprodi).</p>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-700">Ubah menjadi Non-Penguji</p>
                <p class="text-xs text-gray-400 mt-0.5">Dosen tetap terdaftar di sistem, namun tidak lagi berstatus penguji.</p>
            </div>
            <form method="POST" action="{{ route('admin.penguji.destroy', $penguji) }}"
                  onsubmit="return confirm('Cabut status penguji dari {{ addslashes($penguji->nama) }}? Akses loginnya akan dihapus.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Cabut Status Penguji
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
