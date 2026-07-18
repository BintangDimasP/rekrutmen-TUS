@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="{{ auth()->user()->role === 'pelamar' ? 'max-w-lg' : 'max-w-4xl' }} mx-auto min-h-[calc(100vh-8rem)] flex flex-col justify-center py-6"
     x-data="{
        photoModal: false,
        deletePhotoModal: false,
        previewUrl: null,
        fileName: '',
        openFile() { this.$refs.fotoInput.click(); },
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => { this.previewUrl = ev.target.result; };
            reader.readAsDataURL(file);
        },
        resetForm() {
            this.previewUrl = null;
            this.fileName = '';
            this.$refs.fotoInput.value = '';
        }
     }">

    {{-- Flash success --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 mb-6">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─────────────── Combined Card ─────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-8 py-6">
            <div class="flex items-center gap-4">
                
                <div>
                    <h2 class="text-lg font-bold text-white">Pengaturan Akun</h2>
                    
                </div>
            </div>
        </div>

        {{-- Two-column body --}}
        @php $isPelamar = auth()->user()->role === 'pelamar'; @endphp
        <div class="{{ $isPelamar ? 'grid grid-cols-1' : 'grid grid-cols-1 md:grid-cols-2' }}">

            {{-- ── Kiri: Foto Profil (non-pelamar only) ── --}}
            @if(!$isPelamar)
            <div class="p-10 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 bg-gray-50/40">

                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-6">Foto Profil</h3>

                {{-- Avatar dengan pencil --}}
                <div class="relative mb-4">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-[#8b1515] to-[#6e1010] flex items-center justify-center text-white text-5xl font-bold ring-4 ring-white shadow-lg overflow-hidden">
                        @if(auth()->user()->foto_profil_url)
                            <img src="{{ auth()->user()->foto_profil_url }}" alt="Foto Profil" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>

                    <button type="button" @click="photoModal = true"
                            class="absolute bottom-1 right-1 w-10 h-10 bg-[#8b1515] hover:bg-red-900 text-white rounded-full shadow-lg flex items-center justify-center transition-all ring-4 ring-white"
                            title="Ubah Foto Profil">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                </div>

                <p class="text-sm font-bold text-gray-800 mt-1 text-center">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 text-center capitalize">{{ auth()->user()->role }}</p>

                @if(auth()->user()->foto_profil)
                    <button type="button" @click="deletePhotoModal = true"
                            class="mt-5 text-xs font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">
                        Hapus Foto Profil
                    </button>
                @endif
            </div>
            @endif

            {{-- ── Kanan: Form Password ── --}}
            <div class="{{ $isPelamar ? 'p-10' : 'p-10' }}">
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-8">Ubah Password</h3>

                <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Password Lama</label>
                        <input type="password" name="current_password"
                               value="{{ old('current_password') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                                      focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all
                                      @error('current_password') border-red-400 bg-red-50 @enderror"
                               placeholder="Password saat ini">
                        @error('current_password')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Password Baru</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                                      focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all
                                      @error('password') border-red-400 bg-red-50 @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                                      focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all"
                               placeholder="Ulangi password baru">
                    </div>

                    <div class="pt-5">
                        <button type="submit"
                                class="w-full py-2.5 bg-[#8b1515] hover:bg-red-900 text-white font-bold text-sm rounded-xl shadow-md transition-colors">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ─────────────── Modal Upload Foto ─────────────── --}}
    <template x-teleport="body">
        <div x-show="photoModal" x-transition.opacity style="display:none;"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="photoModal = false; resetForm()">
            <div x-show="photoModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

                <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-white">Ubah Foto Profil</h3>
                    <button type="button" @click="photoModal = false; resetForm()"
                            class="w-7 h-7 flex items-center justify-center rounded-lg border border-white/40 text-white hover:bg-white/10 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('settings.foto.update') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf

                    {{-- Preview Area --}}
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-full bg-gray-100 ring-4 ring-gray-100 shadow-inner overflow-hidden flex items-center justify-center">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewUrl">
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#8b1515] to-[#6e1010] text-white text-4xl font-bold">
                                    @if(auth()->user()->foto_profil_url)
                                        <img src="{{ auth()->user()->foto_profil_url }}" alt="Foto Profil" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                            </template>
                        </div>

                        <p class="text-xs text-gray-500 mt-3" x-show="!fileName">Pilih file gambar baru</p>
                        <p class="text-xs text-gray-700 font-medium mt-3 truncate max-w-full" x-show="fileName" x-text="fileName"></p>
                    </div>

                    {{-- Hidden file input --}}
                    <input type="file" name="foto_profil" accept="image/jpeg,image/jpg,image/png,image/webp"
                           x-ref="fotoInput" @change="onFileChange($event)" class="hidden">

                    {{-- Tombol pilih file --}}
                    <button type="button" @click="openFile()"
                            class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Pilih File
                    </button>

                    @error('foto_profil')
                        <p class="text-xs text-red-500 font-medium text-center">{{ $message }}</p>
                    @enderror

                    <p class="text-[0.7rem] text-gray-400 text-center">Format: JPG, JPEG, PNG, WEBP — Maksimal 8 MB</p>

                    {{-- Action --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="photoModal = false; resetForm()"
                                class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="!previewUrl"
                                :class="previewUrl ? 'bg-[#8b1515] hover:bg-red-900 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                                class="flex-1 py-2.5 text-white text-sm font-bold rounded-xl shadow-md transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ─────────────── Modal Konfirmasi Hapus Foto ─────────────── --}}
    <template x-teleport="body">
        <div x-show="deletePhotoModal" x-transition.opacity style="display:none;"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="deletePhotoModal = false">
            <div x-show="deletePhotoModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">

                <div class="mx-auto mb-5 flex justify-center">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                        <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                        <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                    </svg>
                </div>
                <h2 class="text-xl font-extrabold text-gray-800 mb-2">Hapus foto profil?</h2>
                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Foto akan kembali ke<br>tampilan inisial nama.</p>

                <div class="grid grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('settings.foto.delete') }}" class="contents">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 hover:bg-gray-800 hover:text-white rounded-xl transition-all">Iya</button>
                    </form>
                    <button type="button" @click="deletePhotoModal = false"
                            class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 rounded-xl shadow-md transition-all">Tidak</button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
