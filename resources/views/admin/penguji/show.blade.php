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

    {{-- Single Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-6 py-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $penguji->nama }}</h2>
                <p class="text-white/70 text-sm mt-0.5">{{ $penguji->kode }} &bull; {{ $penguji->prodi?->nama ?? 'Prodi tidak diketahui' }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                @if($penguji->is_kaprodi)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-400/90 text-amber-900 shadow-sm">
                        Kaprodi
                    </span>
                @endif
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-white/20 text-white border border-white/30 shadow-sm">
                    Penguji
                </span>
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

        {{-- Danger Zone inside the same card --}}
        <div class="border-t border-gray-100">
            
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
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                        
                        Cabut Status Penguji
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
