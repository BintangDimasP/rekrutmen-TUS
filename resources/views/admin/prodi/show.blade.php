@extends('layouts.admin')

@section('title', 'Dosen Prodi — ' . $prodi->nama)

@section('content')



    {{-- Main Container --}}
    <div x-data="{ openAddModal: false {{ $errors->any() && !old('edit_dosen_id') && !$errors->has('file') ? ', openAddModal: true' : '' }}, openImportModal: false {{ $errors->has('file') ? ', openImportModal: true' : '' }} }"
        class="max-w-6xl mx-auto">

        <div class="space-y-6">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.prodi.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Prodi</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-semibold text-gray-800">{{ $prodi->nama }}</span>
        </div>

        {{-- Filter & Action --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full lg:w-auto">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Cari dosen..."
                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
            </div>

            <div class="flex items-center gap-3 w-full lg:w-auto">
                <button type="button"
                    class="flex-1 lg:flex-none px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button type="button" @click="openImportModal = true"
                    class="flex-1 lg:flex-none px-4 py-2.5 bg-green-50 border border-green-200 text-green-700 hover:bg-green-100 text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </button>
                <button type="button" @click="openAddModal = true"
                    class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Dosen
                </button>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[22%]">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[12%]">Kode</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">NIP/NIDN</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[22%]">Email</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-right w-[12%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dosens as $dosen)
                            <tr x-data="{ showDeleteModal: false, openEditModal: false {{ $errors->any() && old('edit_dosen_id') == $dosen->id ? ', openEditModal: true' : '' }} }"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate">{{ $dosen->nama }}</td>
                                <td class="py-3 px-5 text-sm text-gray-600 truncate">{{ $dosen->kode }}</td>
                                <td class="py-3 px-5 text-sm text-gray-600 truncate">{{ $dosen->nip ?? '-' }}/{{ $dosen->nidn ?? '-' }}
                                </td>
                                <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate">
                                    @php
                                        $userEmails = \App\Models\User::where('dosen_id', $dosen->id)->pluck('email', 'role');
                                    @endphp
                                    @if($userEmails->isNotEmpty())
                                        @foreach($userEmails as $role => $email)
                                            <div class="text-xs">{{ $email }}</div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-sm">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if($dosen->is_kaprodi)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800">Kaprodi</span>
                                        @endif
                                        @if($dosen->is_penguji)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800">Penguji</span>
                                        @endif
                                        @if(!$dosen->is_kaprodi && !$dosen->is_penguji)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-sm">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="openEditModal = true"
                                            class="text-gray-400 hover:text-amber-600 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="showDeleteModal = true" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>

                                        {{-- ── Delete Modal ── --}}
                                        <div x-show="showDeleteModal" x-transition.opacity
                                             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                             @click.self="showDeleteModal = false" style="display: none;">
                                            <div x-show="showDeleteModal"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative whitespace-normal">
                                                
                                                {{-- Close Button --}}
                                                <button type="button" @click="showDeleteModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
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
                                                
                                                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus dosen ini?</h2>
                                                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Data yang dihapus tidak dapat dikembalikan!</p>

                                                <div class="flex justify-center gap-3">
                                                    <form method="POST" action="{{ route('admin.dosen.destroy', $dosen) }}" class="flex-1 m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Yes</button>
                                                    </form>
                                                    <button type="button" @click="showDeleteModal = false" class="flex-1 w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all">No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ── Edit Modal ── --}}
                                    <div x-show="openEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                        @click.self="openEditModal = false" style="display: none;">
                                        <div 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">

                                            <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                                                <h2 class="text-xl font-semibold text-white tracking-tight">Edit Dosen</h2>
                                                <button type="button" @click="openEditModal = false"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('admin.dosen.update', $dosen) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="edit_dosen_id" value="{{ $dosen->id }}">

                                                <div class="p-6 space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama
                                                            Lengkap</label>
                                                        <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}"
                                                            required
                                                            class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                        @if($errors->has('nama') && old('edit_dosen_id') == $dosen->id)
                                                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('nama') }}</p>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-800 mb-1.5">Kode
                                                            Dosen</label>
                                                        <input type="text" name="kode" value="{{ old('kode', $dosen->kode) }}"
                                                            required
                                                            class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium uppercase text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                        @if($errors->has('kode') && old('edit_dosen_id') == $dosen->id)
                                                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('kode') }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label
                                                                class="block text-sm font-medium text-gray-800 mb-1.5">NIP</label>
                                                            <input type="text" name="nip" value="{{ old('nip', $dosen->nip) }}"
                                                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                            @if($errors->has('nip') && old('edit_dosen_id') == $dosen->id)
                                                                <p class="text-xs text-red-500 mt-1">{{ $errors->first('nip') }}</p>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-sm font-medium text-gray-800 mb-1.5">NIDN</label>
                                                            <input type="text" name="nidn"
                                                                value="{{ old('nidn', $dosen->nidn) }}"
                                                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                            @if($errors->has('nidn') && old('edit_dosen_id') == $dosen->id)
                                                                <p class="text-xs text-red-500 mt-1">{{ $errors->first('nidn') }}
                                                            </p> @endif
                                                        </div>
                                                    </div>
                                

                                                    {{-- Roles --}}
                                                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50 space-y-3">
                                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jabatan / Peran</p>
                                                        <label class="flex items-center gap-3 cursor-pointer group">
                                                            <input type="checkbox" name="is_kaprodi" value="1" {{ old('edit_dosen_id') == $dosen->id ? (old('is_kaprodi') ? 'checked' : '') : ($dosen->is_kaprodi ? 'checked' : '') }}
                                                                class="w-4 h-4 rounded border-gray-300 text-[#8b1515] focus:ring-[#8b1515] cursor-pointer">
                                                            <div>
                                                                <span class="text-sm font-medium text-gray-800">Kaprodi</span>
                                                                <p class="text-xs text-gray-400">Hanya 1 per prodi — menggantikan kaprodi sebelumnya</p>
                                                            </div>
                                                        </label>
                                                    </div>

                                                </div>

                                                <div
                                                    class="px-6 py-4 bg-gray-50 flex justify-center border-t border-gray-100">
                                                    <button type="submit"
                                                        class="px-8 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors w-full sm:w-auto">Simpan</button>
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
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-800 font-medium text-sm">Tidak ada dosen</h3>
                                        <p class="text-gray-400 text-xs mt-1">Belum ada dosen terdaftar di program studi ini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination / Tally --}}
            @include('components.pagination', ['paginator' => $dosens])

        </div>
        </div>

        {{-- ── Add Modal ── --}}
        <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
            @click.self="openAddModal = false" style="display: none;">
            <div 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" 
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">

                <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white tracking-tight">Tambah Dosen Baru</h2>
                    <button type="button" @click="openAddModal = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.dosen.store', $prodi) }}">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ !old('edit_dosen_id') ? old('nama') : '' }}" required
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('nama') && !old('edit_dosen_id'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('nama') }}</p> @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Kode Dosen</label>
                            <input type="text" name="kode" value="{{ !old('edit_dosen_id') ? old('kode') : '' }}" required
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium uppercase text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('kode') && !old('edit_dosen_id'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('kode') }}</p> @endif
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1.5">NIP</label>
                                <input type="text" name="nip" value="{{ !old('edit_dosen_id') ? old('nip') : '' }}"
                                    class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1.5">NIDN</label>
                                <input type="text" name="nidn" value="{{ !old('edit_dosen_id') ? old('nidn') : '' }}"
                                    class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            </div>
                        </div>

                        {{-- Info: email otomatis --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-blue-600 font-medium">Email akan otomatis dibuat saat dosen ditunjuk sebagai Penguji atau Kaprodi.</p>
                        </div>

                        {{-- Roles --}}
                        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50 space-y-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jabatan / Peran</p>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_kaprodi" value="1" {{ old('is_kaprodi') ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-gray-300 text-[#8b1515] focus:ring-[#8b1515] cursor-pointer">
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Kaprodi</span>
                                    <p class="text-xs text-gray-400">Hanya 1 per prodi — menggantikan kaprodi sebelumnya</p>
                                </div>
                            </label>
                        </div>


                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex justify-center border-t border-gray-100">
                        <button type="submit"
                            class="px-8 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors w-full sm:w-auto">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Import Modal ── --}}
        <div x-show="openImportModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9990] bg-black/40 backdrop-blur-sm"
            @click="openImportModal = false"
            style="display: none;">
        </div>

        <div x-show="openImportModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none"
            style="display: none;">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden pointer-events-auto">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4" style="background: #8b1515;">
                    <div>
                        <h2 class="text-base font-semibold text-white">Import Dosen</h2>
                        <p class="text-xs text-white/60 mt-0.5">Upload file Excel ke program studi ini</p>
                    </div>
                    <button type="button" @click="openImportModal = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all"
                        style="border: 1.5px solid rgba(255,255,255,0.3);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <form method="POST" action="{{ route('admin.dosen.import', $prodi) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5">
                        <p class="text-sm text-gray-500 leading-relaxed mb-4">
                            Upload file <span class="font-medium text-gray-700">.xlsx</span> atau
                            <span class="font-medium text-gray-700">.csv</span> — header baris pertama:
                            <code class="font-medium text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">nama, kode, nip, nidn</code>.
                        </p>

                        <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 rounded-xl px-4 py-7 cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-[#8b1515]/30 transition-colors">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-500">Klik untuk pilih file</span>
                            <span class="text-xs text-gray-400">.xlsx &nbsp;/&nbsp; .xls &nbsp;/&nbsp; .csv</span>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="hidden" />
                        </label>
                        @if($errors->has('file'))
                            <p class="text-xs text-red-500 mt-2">{!! $errors->first('file') !!}</p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-center gap-3">
                        <button type="button" @click="openImportModal = false"
                            class="px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-10 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:opacity-90 active:scale-95 shadow-md"
                            style="background: #8b1515;">
                            Import Data
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>{{-- /x-data --}}

@endsection