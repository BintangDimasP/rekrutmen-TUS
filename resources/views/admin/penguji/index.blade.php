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
    <div x-data="{ openAddModal: false, searchDosen: '' }" class="max-w-6xl mx-auto">
        
        {{-- Header Data --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Penguji</h1>
                <p class="text-sm text-gray-500 mt-1">Daftar dosen yang telah ditugaskan sebagai penguji pada sistem.</p>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            {{-- Toolbar --}}
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                {{-- Filter & Actions --}}
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" placeholder="Cari nama penguji..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.jadwal.index') }}"
                       class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Lihat Jadwal
                    </a>
                    <a href="{{ route('admin.jadwal.create') }}"
                       class="px-4 py-2 bg-indigo-700 text-white hover:bg-indigo-800 text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Jadwalkan Seleksi
                    </a>
                    <button type="button" @click="openAddModal = true" class="px-4 py-2 bg-[#8b1515] text-white hover:bg-red-900 text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tunjuk Penguji
                    </button>
                </div>

            </div>

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
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-right">Aksi</th>
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
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $penguji->email }}</td>
                            <td class="py-3 px-5 text-sm">
                                @if($penguji->is_kaprodi)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800">Merangkap Kaprodi</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.penguji.show', $penguji) }}"
                                       class="text-gray-400 hover:text-blue-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Detail Penguji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.penguji.destroy', $penguji) }}" onsubmit="return confirm('Cabut status penguji dari dosen {{ addslashes($penguji->nama) }}? Akses loginnya akan dihapus.')" class="inline-block m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Cabut Status">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
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

        {{-- ── Tunjuk Penguji Modal ── --}}
        <div x-show="openAddModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="openAddModal = false" style="display: none;">
            <div x-show="openAddModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden text-left flex flex-col max-h-[85vh]">
                
                <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <h2 class="text-xl font-semibold text-white tracking-tight">Tunjuk Dosen Menjadi Penguji</h2>
                    <button type="button" @click="openAddModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 border-b border-gray-100 bg-gray-50 flex-shrink-0">
                    <p class="text-sm text-gray-600 mb-3 leading-relaxed">
                        Pilih satu atau lebih dosen dari daftar di bawah ini. Akun login akan otomatis dibuatkan untuk mereka dengan kata sandi bawaan <code class="bg-gray-200 px-1 py-0.5 rounded text-gray-800">penguji123</code>.
                    </p>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="searchDosen" placeholder="Cari berdasarkan nama atau prodi..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] hover:border-gray-300 transition shadow-sm">
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.penguji.store') }}" class="flex-1 overflow-hidden flex flex-col min-h-0">
                    @csrf
                    
                    <div class="flex-1 overflow-y-auto bg-white p-0">
                        @if($calonPengujis->isEmpty())
                            <div class="p-12 text-center">
                                <span class="text-gray-400 text-sm">Seluruh dosen tersinkronisasi sebagai penguji, atau tidak ada dosen tersedia.</span>
                            </div>
                        @else
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 sticky top-0 z-10 border-b border-gray-100 shadow-sm">
                                    <tr>
                                        <th class="py-3 px-5 w-10 text-center">
                                            {{-- Checkbox select all optional --}}
                                        </th>
                                        <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                        <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Prodi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($calonPengujis as $calon)
                                        <tr class="hover:bg-gray-50 transition-colors"
                                            x-show="searchDosen === '' || '{{ strtolower($calon->nama) }}'.includes(searchDosen.toLowerCase()) || '{{ strtolower($calon->prodi?->nama ?? '') }}'.includes(searchDosen.toLowerCase())">
                                            <td class="py-3 px-5 text-center">
                                                <input type="checkbox" name="dosen_ids[]" value="{{ $calon->id }}" class="w-4 h-4 text-[#8b1515] bg-gray-100 border-gray-300 rounded focus:ring-[#8b1515] focus:ring-2 cursor-pointer">
                                            </td>
                                            <td class="py-3 px-5">
                                                <div class="text-sm font-medium text-gray-800">{{ $calon->nama }}</div>
                                                <div class="text-xs text-gray-400 font-mono">{{ $calon->kode }} &bull; {{ $calon->email }}</div>
                                            </td>
                                            <td class="py-3 px-5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-gray-100 text-gray-600 uppercase">{{ $calon->prodi?->kode ?? '-' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="px-6 py-4 bg-white flex justify-end gap-3 border-t border-gray-100 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)] flex-shrink-0">
                        <button type="button" @click="openAddModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Tunjuk Penguji Terpilih</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection
