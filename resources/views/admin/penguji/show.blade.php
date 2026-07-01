@extends('layouts.admin')

@section('title', 'Detail Penguji — ' . $penguji->nama)

@section('content')



<div class="max-w-3xl mx-auto space-y-6 py-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.penguji.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Penguji</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $penguji->nama }}</span>
    </div>

    {{-- Single Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-6 py-8 flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
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

        <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
            {{-- NIP --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-2">NIP</p>
                <p class="text-sm font-semibold text-gray-800">{{ $penguji->nip ?? '—' }}</p>
            </div>

            {{-- NIDN --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-2">NIDN</p>
                <p class="text-sm font-semibold text-gray-800">{{ $penguji->nidn ?? '—' }}</p>
            </div>

            {{-- Prodi --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-2">Program Studi</p>
                <p class="text-sm font-semibold text-gray-800">{{ $penguji->prodi?->nama ?? '—' }}</p>
            </div>

            {{-- Kode --}}
            <div>
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-2">Kode Dosen</p>
                <p class="text-sm font-semibold text-gray-800">{{ $penguji->kode }}</p>
            </div>

            <div class="sm:col-span-2">
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-2">Email</p>
                <p class="text-sm font-semibold text-gray-800">{{ $pengujiEmail ?? '—' }}</p>
            </div>
        </div>

        {{-- Danger Zone inside the same card --}}
        <div class="border-t border-gray-100" x-data="{ showCabutModal: false }">
            <div class="px-8 py-7 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Ubah menjadi Non-Penguji</p>
                    <p class="text-xs text-gray-400 mt-0.5">Dosen tetap terdaftar di sistem, namun tidak lagi berstatus penguji.</p>
                </div>
                <button type="button" @click="showCabutModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                    Cabut Status Penguji
                </button>
            </div>

            {{-- Cabut Status Modal --}}
            <div x-show="showCabutModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="showCabutModal = false">
                <div x-show="showCabutModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">
                    
                    {{-- Close Button --}}
                    <button type="button" @click="showCabutModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Warning Icon --}}
                    <div class="mx-auto mb-5 flex justify-center">
                        <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                            <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                            <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Cabut status penguji?</h2>
                    <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Akses login penguji akan dihapus.<br>Dosen tetap terdaftar di sistem.</p>

                    <div class="grid grid-cols-2 gap-3">
                        <form method="POST" action="{{ route('admin.penguji.destroy', $penguji) }}" class="contents">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Yes</button>
                        </form>
                        <button type="button" @click="showCabutModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
