@extends('layouts.admin')

@section('title', 'Manajemen Penguji')

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

    @if($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl shadow-black/5 border border-red-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 text-white shadow-inner">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Gagal</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">Harap pilih setidaknya satu dosen.</p>
            </div>
        </div>
    @endif

    {{-- Main Container --}}
    <div x-data="{ openAddModal: false, searchDosen: '', filterProdi: '' }">

        {{-- Inner layout container --}}
        <div class="max-w-6xl mx-auto space-y-6">

        {{-- Filter & Action --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.penguji.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-4 w-full lg:w-auto">
                {{-- Left: Filter Prodi --}}
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-48">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <select name="prodi_id" onchange="this.form.submit()" 
                                class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                            <option value="">Semua Prodi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(request()->filled('prodi_id') || request()->filled('search'))
                        <a href="{{ route('admin.penguji.index') }}" class="text-xs text-red-600 hover:underline">Reset</a>
                    @endif
                </div>

                {{-- Right: Search --}}
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penguji..." 
                           class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
            </form>
            
            <button type="button" @click="openAddModal = true" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors shrink-0 w-full lg:w-auto cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tunjuk Penguji
            </button>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Nama Penguji</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Homebase (Prodi)</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">NIP/NIDN</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Email Akun</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Status Tambahan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pengujis as $penguji)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium">
                                {{ $penguji->nama }}
                                <div class="text-xs font-mono text-gray-500">{{ $penguji->kode }}</div>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium">{{ $penguji->prodi?->nama ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $penguji->nip ?? '-' }} / {{ $penguji->nidn ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-mono">{{ $pengujiEmails[$penguji->id] ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm">
                                @if($penguji->is_kaprodi)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800">Merangkap Kaprodi</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.penguji.show', $penguji) }}"
                                       class="text-gray-400 hover:text-blue-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Detail Penguji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 px-5 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                    </div>
                                    <h3 class="text-gray-800 font-medium text-sm">Belum ada penguji</h3>
                                    <p class="text-gray-400 text-xs mt-1">Gunakan tombol "Tunjuk Penguji" untuk mulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Footer Pagination / Tally --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
                <span>Total: <strong>{{ $pengujis->count() }}</strong> penguji aktif</span>
            </div>

        </div>

        </div>{{-- /inner layout --}}

        {{-- ── Tunjuk Penguji Modal ── --}}
        {{-- Overlay --}}
        <div x-show="openAddModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9990] bg-black/40 backdrop-blur-sm"
             @click="openAddModal = false"
             style="display: none;">
        </div>

        {{-- Modal --}}
        <div x-show="openAddModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none"
             style="display: none;">

            <div class="bg-white rounded-2xl w-full max-w-3xl overflow-hidden flex flex-col pointer-events-auto shadow-2xl"
                 style="max-height: 85vh;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="background: #8b1515;">
                    <div>
                        <h2 class="text-base font-semibold text-white">Tunjuk Penguji</h2>
                        <p class="text-xs text-white/60 mt-0.5">Pilih dosen untuk dijadikan penguji</p>
                    </div>
                    <button type="button" @click="openAddModal = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all"
                        style="border: 1.5px solid rgba(255,255,255,0.3);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Toolbar --}}
                <div class="px-5 pt-4 pb-3 border-b border-gray-100 bg-gray-50/50 flex-shrink-0">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="searchDosen" placeholder="Cari nama dosen..."
                                class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]/20 transition shadow-sm">
                        </div>
                        <select x-model="filterProdi"
                            class="pl-3 pr-8 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]/20 transition min-w-[150px] shadow-sm">
                            <option value="">Semua Prodi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-gray-400 mt-2.5 leading-relaxed">
                        Akun login dibuat otomatis &mdash; kata sandi bawaan
                        <code class="bg-white border border-gray-200 px-1.5 py-0.5 rounded text-gray-500 font-mono">penguji123</code>.
                    </p>
                </div>

                {{-- Body --}}
                <form method="POST" action="{{ route('admin.penguji.store') }}" class="flex-1 overflow-hidden flex flex-col min-h-0">
                    @csrf

                    <div class="flex-1 overflow-y-auto">
                        @if($calonPengujis->isEmpty())
                            <div class="py-16 text-center">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                                <p class="text-sm text-gray-400">Seluruh dosen telah menjadi penguji.</p>
                            </div>
                        @else
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 z-10 bg-white border-b border-gray-100 shadow-sm">
                                    <tr>
                                        <th class="py-3 px-5 w-10"></th>
                                        <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Dosen</th>
                                        <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Prodi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($calonPengujis as $calon)
                                        <tr class="hover:bg-[#8b1515]/[0.03] transition-colors group"
                                            x-show="
                                                (searchDosen === '' || '{{ strtolower($calon->nama) }}'.includes(searchDosen.toLowerCase()))
                                                && (filterProdi === '' || filterProdi === '{{ $calon->prodi_id }}')
                                            ">
                                            <td class="py-3.5 px-5 text-center">
                                                <input type="checkbox" name="dosen_ids[]" value="{{ $calon->id }}"
                                                    class="w-4 h-4 rounded border-gray-300 cursor-pointer focus:ring-2 focus:ring-[#8b1515]/20"
                                                    style="accent-color: #8b1515;">
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="text-sm font-medium text-gray-800 group-hover:text-[#8b1515] transition-colors">{{ $calon->nama }}</div>
                                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $calon->kode }} &middot; {{ $calon->email }}</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-500">
                                                    {{ $calon->prodi?->nama ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-white border-t border-gray-100 flex-shrink-0 flex items-center justify-center gap-3">
                        
                        <button type="submit"
                            class="px-10 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:opacity-90 active:scale-95 shadow-md"
                            style="background: #8b1515;">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>{{-- /modal inner --}}
        </div>{{-- /modal wrapper --}}

    </div>{{-- /x-data --}}

@endsection
