@extends('layouts.admin')

@section('title', 'Dosen Prodi — ' . $prodi->nama)

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

    {{-- Main Container --}}
    <div x-data="{ openAddModal: false {{ $errors->any() && !old('edit_dosen_id') ? ', openAddModal: true' : '' }} }" class="max-w-6xl mx-auto">
        
        {{-- Header Data --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-800">Dosen Prodi</h1>
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">
                        {{ $prodi->kode }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Data master dosen untuk prodi {{ $prodi->nama }}</p>
            </div>
            
            <a href="{{ route('admin.prodi.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors shadow-sm self-start inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
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
                        <input type="text" placeholder="Cari dosen..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                    </button>
                    <button type="button" @click="openAddModal = true" class="px-4 py-2 bg-[#8b1515] text-white hover:bg-red-900 text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Dosen
                    </button>
                </div>

            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Kode</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">NIP/NIDN</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Email</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($prodi->dosens as $dosen)
                        <tr x-data="{ openEditModal: false {{ $errors->any() && old('edit_dosen_id') == $dosen->id ? ', openEditModal: true' : '' }} }" class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium">{{ $dosen->nama }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $dosen->kode }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $dosen->nip ?? '-' }}/{{ $dosen->nidn ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $dosen->email }}</td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($dosen->is_kaprodi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800">Kaprodi</span>
                                    @endif
                                    @if($dosen->is_penguji)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800">Penguji</span>
                                    @endif
                                    @if(!$dosen->is_kaprodi && !$dosen->is_penguji)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="openEditModal = true" class="text-gray-400 hover:text-amber-600 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.dosen.destroy', $dosen) }}" onsubmit="return confirm('Yakin ingin menghapus dosen {{ addslashes($dosen->nama) }}?')" class="inline-block m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- ── Edit Modal ── --}}
                                <div x-show="openEditModal" x-transition.opacity
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                    @click.self="openEditModal = false" style="display: none;">
                                <div x-show="openEditModal"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">
                                    
                                    <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                                        <h2 class="text-xl font-semibold text-white tracking-tight">Edit Dosen</h2>
                                        <button type="button" @click="openEditModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.dosen.update', $dosen) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="edit_dosen_id" value="{{ $dosen->id }}">
                                        
                                        <div class="p-6 space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Lengkap</label>
                                                <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                @if($errors->has('nama') && old('edit_dosen_id') == $dosen->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('nama') }}</p> @endif
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-800 mb-1.5">Kode Dosen</label>
                                                <input type="text" name="kode" value="{{ old('kode', $dosen->kode) }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-mono uppercase text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                @if($errors->has('kode') && old('edit_dosen_id') == $dosen->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('kode') }}</p> @endif
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">NIP</label>
                                                    <input type="text" name="nip" value="{{ old('nip', $dosen->nip) }}" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                    @if($errors->has('nip') && old('edit_dosen_id') == $dosen->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('nip') }}</p> @endif
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">NIDN</label>
                                                    <input type="text" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                    @if($errors->has('nidn') && old('edit_dosen_id') == $dosen->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('nidn') }}</p> @endif
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-800 mb-1.5">Email</label>
                                                <input type="email" name="email" value="{{ old('email', $dosen->email) }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                @if($errors->has('email') && old('edit_dosen_id') == $dosen->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('email') }}</p> @endif
                                            </div>
                                            

                                        </div>

                                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                                            <button type="button" @click="openEditModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Simpan</button>
                                        </div>
                                    </form>
                                </div>
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
                                    <h3 class="text-gray-800 font-medium text-sm">Tidak ada dosen</h3>
                                    <p class="text-gray-400 text-xs mt-1">Belum ada dosen terdaftar di program studi ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Footer Pagination / Tally --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
                <span>Total: <strong>{{ $prodi->dosens->count() }}</strong> dosen</span>
            </div>

        </div>

        {{-- ── Add Modal ── --}}
        <div x-show="openAddModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="openAddModal = false" style="display: none;">
            <div x-show="openAddModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">
                
                <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white tracking-tight">Tambah Dosen Baru</h2>
                    <button type="button" @click="openAddModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.dosen.store', $prodi) }}">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ !old('edit_dosen_id') ? old('nama') : '' }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('nama') && !old('edit_dosen_id')) <p class="text-xs text-red-500 mt-1">{{ $errors->first('nama') }}</p> @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Kode Dosen</label>
                            <input type="text" name="kode" value="{{ !old('edit_dosen_id') ? old('kode') : '' }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-mono uppercase text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('kode') && !old('edit_dosen_id')) <p class="text-xs text-red-500 mt-1">{{ $errors->first('kode') }}</p> @endif
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1.5">NIP</label>
                                <input type="text" name="nip" value="{{ !old('edit_dosen_id') ? old('nip') : '' }}" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1.5">NIDN</label>
                                <input type="text" name="nidn" value="{{ !old('edit_dosen_id') ? old('nidn') : '' }}" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ !old('edit_dosen_id') ? old('email') : '' }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('email') && !old('edit_dosen_id')) <p class="text-xs text-red-500 mt-1">{{ $errors->first('email') }}</p> @endif
                        </div>
                        

                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                        <button type="button" @click="openAddModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
