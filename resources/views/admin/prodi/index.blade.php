@extends('layouts.admin')

@section('title', 'Manajemen Program Studi')

@section('content')

    {{-- Prodi Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

        {{-- Existing Prodi Cards --}}
        @foreach($prodis as $prodi)
            <div x-data="{ openEditModal: {{ old('edit_prodi_id') == $prodi->id ? 'true' : 'false' }}, showDeleteModal: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow duration-200 overflow-hidden relative">
                
                {{-- Card Body --}}
                <div class="p-6 flex flex-col items-center flex-1">
                    {{-- Logo --}}
                    @if($prodi->logo)
                        <img src="{{ asset('storage/' . $prodi->logo) }}" alt="Logo {{ $prodi->nama }}" class="w-20 h-20 rounded-full object-cover border-4 border-gray-50 shadow-sm mb-4 shrink-0" style="width: 80px; height: 80px; min-width: 80px; min-height: 80px; max-width: 80px; max-height: 80px; object-fit: cover;">
                    @else
                        <div class="w-20 h-20 rounded-full bg-white border-4 border-gray-50 shadow-sm flex items-center justify-center p-2 mb-4 shrink-0" style="width: 80px; height: 80px; min-width: 80px; min-height: 80px;">
                            <img src="{{ asset('images/logo-icon.png') }}" alt="Telkom University" class="w-full h-full object-contain">
                        </div>
                    @endif

                    <h3 class="font-bold text-gray-800 text-[1.1rem] text-center leading-snug">{{ $prodi->nama }}</h3>
                    <p class="text-[0.8rem] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $prodi->kode }}</p>
                </div>

                {{-- Separator --}}
                <div class="w-full h-px bg-gray-100"></div>

                {{-- Action Footer --}}
                <div class="px-6 py-4 flex items-center justify-center gap-3 bg-gray-50/50 mt-auto">
                    {{-- Eye: Lihat Dosen --}}
                    <a href="{{ route('admin.prodi.show', $prodi) }}" title="Lihat Data Dosen"
                       class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm border border-blue-100 hover:shadow-md hover:shadow-blue-600/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>

                    {{-- Edit Modal Button --}}
                    <button type="button" @click="openEditModal = true" title="Edit Prodi"
                            class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm border border-amber-100 hover:shadow-md hover:shadow-amber-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                    </button>

                    {{-- Hapus --}}
                    <button type="button" @click="showDeleteModal = true" title="Hapus Prodi" 
                            class="w-10 h-10 rounded-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm border border-red-100 hover:shadow-md hover:shadow-red-600/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                    </button>
                </div>

                {{-- ── Edit Modal ── --}}
                <div x-show="openEditModal" x-transition.opacity
                     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                     @click.self="openEditModal = false"
                     @if(old('edit_prodi_id') != $prodi->id) style="display: none;" @endif>
                    <div x-show="openEditModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="bg-white rounded-2xl shadow-2xl w-full max-w-[480px] overflow-hidden">

                        {{-- Header Merah --}}
                        <div class="bg-[#8b1515] px-7 py-5 flex items-center justify-between">
                            <h2 class="text-2xl font-semibold text-white tracking-tight">Edit Prodi</h2>
                            <button type="button" @click="openEditModal = false"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('admin.prodi.update', $prodi) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="edit_prodi_id" value="{{ $prodi->id }}">

                            {{-- Body --}}
                            <div class="px-8 py-7 flex items-start gap-7 text-left">
                                {{-- Logo Upload --}}
                                <div class="flex-shrink-0">
                                    <label for="edit_logo_{{ $prodi->id }}" class="relative cursor-pointer group block">
                                        <div class="w-[108px] h-[108px] rounded-full bg-gray-200 overflow-hidden flex items-center justify-center ring-2 ring-gray-200 group-hover:ring-[#8b1515]/40 transition-all duration-200">
                                            <img id="edit_preview_{{ $prodi->id }}"
                                                 src="{{ $prodi->logo ? asset('storage/' . $prodi->logo) : '' }}"
                                                 alt="{{ $prodi->nama }}"
                                                 class="w-full h-full object-cover {{ $prodi->logo ? '' : 'hidden' }}">
                                            <svg id="edit_placeholder_{{ $prodi->id }}" class="w-12 h-12 text-gray-400 {{ $prodi->logo ? 'hidden' : '' }}" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                            </svg>
                                        </div>
                                        <div class="absolute bottom-0.5 right-0.5 w-8 h-8 rounded-full bg-white shadow-md border border-gray-200 flex items-center justify-center group-hover:bg-[#8b1515] group-hover:border-[#8b1515] transition-all duration-200">
                                            <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                        </div>
                                        <input type="file" id="edit_logo_{{ $prodi->id }}" name="logo" accept="image/*" class="sr-only"
                                               onchange="
                                                   const file = this.files[0];
                                                   if (file) {
                                                       const reader = new FileReader();
                                                       reader.onload = e => {
                                                           document.getElementById('edit_preview_{{ $prodi->id }}').src = e.target.result;
                                                           document.getElementById('edit_preview_{{ $prodi->id }}').classList.remove('hidden');
                                                           document.getElementById('edit_placeholder_{{ $prodi->id }}').classList.add('hidden');
                                                       };
                                                       reader.readAsDataURL(file);
                                                   }
                                               ">
                                    </label>
                                    @error('logo') <p class="text-xs text-red-500 mt-1.5 font-medium max-w-[108px] leading-tight text-center">{{ $message }}</p> @enderror
                                </div>

                                {{-- Fields --}}
                                <div class="flex-1 space-y-5">
                                    <div>
                                        <label class="block text-base font-medium text-gray-800 mb-2">Nama Prodi</label>
                                        <input type="text" name="nama" value="{{ old('nama', $prodi->nama) }}" required
                                               class="w-full px-4 py-2.5 rounded-xl bg-gray-100 border border-transparent text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-base font-medium text-gray-800 mb-2">Kode</label>
                                        <input type="text" name="kode" value="{{ old('kode', $prodi->kode) }}" required maxlength="20"
                                               class="w-full px-4 py-2.5 rounded-xl bg-gray-100 border border-transparent text-sm font-medium uppercase text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-gray-200 mx-0"></div>

                            {{-- Footer --}}
                            <div class="px-8 py-5 flex justify-center">
                                <button type="submit"
                                        class="px-12 py-2.5 bg-[#8b1515] hover:bg-red-900 active:scale-95 text-white text-base font-semibold rounded-xl shadow-md shadow-[#8b1515]/20 transition-all duration-150">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Delete Modal ── --}}
                <div x-show="showDeleteModal" x-transition.opacity
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                     @click.self="showDeleteModal = false" style="display: none;">
                    <div x-show="showDeleteModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">
                        
                        {{-- Warning Icon --}}
                        <div class="mx-auto mb-5 flex justify-center">
                            <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                                <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                                <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                            </svg>
                        </div>
                        
                        <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus prodi ini?</h2>
                        <p class="text-[0.85rem] font-medium text-gray-500 mb-8"><strong class="text-gray-800 block mb-1">{{ $prodi->nama }}</strong>Data yang dihapus tidak dapat dikembalikan!</p>

                        <div class="grid grid-cols-2 gap-3">
                            <form method="POST" action="{{ route('admin.prodi.destroy', $prodi) }}" class="contents">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Iya</button>
                            </form>
                            <button type="button" @click="showDeleteModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all">Tidak</button>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach

        {{-- Tambah Prodi Card --}}
        <div x-data="{ openModal: {{ $errors->any() && !old('edit_prodi_id') ? 'true' : 'false' }} }" @open-prodi-modal.window="openModal = true">
            <button @click="openModal = true"
                    class="w-full h-full min-h-[148px] bg-gray-100 hover:bg-gray-200 border-2 border-dashed border-gray-300 hover:border-gray-400 rounded-xl flex flex-col items-center justify-center gap-2 transition-all duration-200 cursor-pointer group">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span class="text-sm font-semibold text-gray-500 group-hover:text-gray-700 transition-colors">Tambah Prodi</span>
            </button>

            {{-- Modal Tambah Prodi --}}
            <div x-show="openModal" x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                 @click.self="openModal = false"
                 @if(!($errors->any() && !old('edit_prodi_id'))) style="display: none;" @endif>
                <div x-show="openModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="bg-white rounded-2xl shadow-2xl w-full max-w-[480px] overflow-hidden">

                    {{-- ── Header Merah ── --}}
                    <div class="bg-[#8b1515] px-7 py-5 flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-white tracking-tight">Tambah Prodi</h2>
                        <button type="button" @click="openModal = false"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.prodi.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- ── Body ── --}}
                        <div class="px-8 py-7 flex items-start gap-7">

                            {{-- Logo Upload (Circular) --}}
                            <div class="flex-shrink-0">
                                <label for="logo_upload" class="relative cursor-pointer group block">
                                    {{-- Circle area --}}
                                    <div class="w-[108px] h-[108px] rounded-full bg-gray-200 overflow-hidden flex items-center justify-center ring-2 ring-gray-200 group-hover:ring-[#8b1515]/40 transition-all duration-200">
                                        <img id="logo_preview" src="" alt="" class="w-full h-full object-cover hidden">
                                        <svg id="logo_placeholder" class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                        </svg>
                                    </div>
                                    {{-- Pencil badge --}}
                                    <div class="absolute bottom-0.5 right-0.5 w-8 h-8 rounded-full bg-white shadow-md border border-gray-200 flex items-center justify-center group-hover:bg-[#8b1515] group-hover:border-[#8b1515] transition-all duration-200">
                                        <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </div>
                                    <input type="file" id="logo_upload" name="logo" accept="image/*" class="sr-only"
                                           onchange="
                                               const file = this.files[0];
                                               if (file) {
                                                   const reader = new FileReader();
                                                   reader.onload = e => {
                                                       document.getElementById('logo_preview').src = e.target.result;
                                                       document.getElementById('logo_preview').classList.remove('hidden');
                                                       document.getElementById('logo_placeholder').classList.add('hidden');
                                                   };
                                                   reader.readAsDataURL(file);
                                               }
                                           ">
                                </label>
                                @error('logo') <p class="text-xs text-red-500 mt-1.5 font-medium max-w-[108px] leading-tight text-center">{{ $message }}</p> @enderror
                            </div>

                            {{-- Fields --}}
                            <div class="flex-1 space-y-5">
                                <div>
                                    <label class="block text-base font-medium text-gray-800 mb-2">Nama Prodi</label>
                                    <input type="text" name="nama" value="{{ old('nama') }}" required
                                           class="w-full px-4 py-2.5 rounded-xl bg-gray-100 border border-transparent text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all"
                                           placeholder="Teknik Informatika">
                                    @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-base font-medium text-gray-800 mb-2">Kode</label>
                                    <input type="text" name="kode" value="{{ old('kode') }}" required maxlength="20"
                                           class="w-full px-4 py-2.5 rounded-xl bg-gray-100 border border-transparent text-sm font-medium uppercase text-gray-800 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all"
                                           placeholder="TI">
                                    @error('kode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ── Separator ── --}}
                        <div class="h-px bg-gray-200 mx-0"></div>

                        {{-- ── Footer ── --}}
                        <div class="px-8 py-5 flex justify-center">
                            <button type="submit"
                                    class="px-12 py-2.5 bg-[#8b1515] hover:bg-red-900 active:scale-95 text-white text-base font-semibold rounded-xl shadow-md shadow-[#8b1515]/20 transition-all duration-150">
                                Buat Prodi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- Reopen modal on validation error --}}
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // trigger Alpine modal open on validation error
                window.dispatchEvent(new CustomEvent('open-prodi-modal'));
            });
        </script>
    @endif

@endsection
