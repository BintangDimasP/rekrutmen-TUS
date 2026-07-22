@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="{{ auth()->user()->role === 'pelamar' ? 'max-w-lg' : 'max-w-4xl' }} mx-auto min-h-[calc(100vh-8rem)] flex flex-col justify-center py-6"
     x-data="{
        photoModal: false,
        deletePhotoModal: false,
        previewUrl: null,
        fileName: '',
        reqs: { length: false, lower: false, upper: false, number: false },
        checkPassword(val) {
            this.reqs.length = val.length >= 8;
            this.reqs.lower = /[a-z]+/.test(val);
            this.reqs.upper = /[A-Z]+/.test(val);
            this.reqs.number = /[0-9]+/.test(val);
        },
        openFile() { (this.$refs.fotoInput || document.getElementById('fotoInputSettings')).click(); },
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => { 
                this.previewUrl = ev.target.result; 
                this.photoModal = true;
            };
            reader.readAsDataURL(file);
        },
        resetForm() {
            this.previewUrl = null;
            this.fileName = '';
            if (this.$refs.fotoInput) this.$refs.fotoInput.value = '';
            const input = document.getElementById('fotoInputSettings');
            if (input) input.value = '';
        }
     }">

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
            <form method="POST" action="{{ route('settings.foto.update') }}" enctype="multipart/form-data" class="p-10 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 bg-gray-50/40">
                @csrf
                <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-6">Foto Profil</h3>

                {{-- Avatar dengan pencil --}}
                <div class="relative mb-4">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-[#8b1515] to-[#6e1010] flex items-center justify-center text-white text-5xl font-bold ring-4 ring-white shadow-lg overflow-hidden">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Foto Profil" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="w-full h-full flex items-center justify-center">
                                @if(auth()->user()->foto_profil_url)
                                    <img src="{{ auth()->user()->foto_profil_url }}" alt="Foto Profil" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                        </template>
                    </div>

                    {{-- Hidden File Input --}}
                    <input type="file" name="foto_profil" id="fotoInputSettings" accept="image/jpeg,image/jpg,image/png,image/webp"
                           x-ref="fotoInput" @change="onFileChange($event)" class="hidden">

                    <button type="button" @click="openFile()"
                            class="absolute bottom-1 right-1 w-10 h-10 bg-[#8b1515] hover:bg-red-900 text-white rounded-full shadow-lg flex items-center justify-center transition-all ring-4 ring-white"
                            title="Ubah Foto Profil">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                </div>

                <p class="text-sm font-bold text-gray-800 mt-1 text-center">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 text-center capitalize">{{ auth()->user()->role }}</p>

                {{-- Action Area dengan tinggi tetap (h-10) agar posisi vertical centering selalu presisi & diam --}}
                <div class="h-10 flex flex-col items-center justify-center mt-3">
                    <template x-if="previewUrl">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-4 py-2 bg-[#8b1515] hover:bg-red-900 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5 cursor-pointer">
                                Simpan
                            </button>
                            <button type="button" @click="resetForm()" class="px-3 py-2 border border-gray-300 hover:bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl transition-colors">
                                Batal
                            </button>
                        </div>
                    </template>
                    @if(auth()->user()->foto_profil)
                        <template x-if="!previewUrl">
                            <button type="button" @click="deletePhotoModal = true"
                                    class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">
                                Hapus Foto Profil
                            </button>
                        </template>
                    @endif
                </div>

                @error('foto_profil')
                    <p class="text-xs text-red-500 font-medium text-center mt-1">{{ $message }}</p>
                @enderror
            </form>
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
                        <input type="password" name="password" @input="checkPassword($event.target.value)"
                               class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                                      focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all
                                      @error('password') border-red-400 bg-red-50 @enderror"
                               placeholder="********">
                        @error('password')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror

                        {{-- Password Requirements Checklist --}}
                        <div class="mt-2 bg-white border border-gray-100 rounded-lg p-3 shadow-sm">
                            <ul class="grid grid-cols-1 gap-2 text-[0.7rem]">
                                <li class="flex items-center gap-2 transition-colors" :class="reqs.length ? 'text-green-600 font-medium' : 'text-gray-400'">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>Minimal 8 karakter</span>
                                </li>
                                <li class="flex items-center gap-2 transition-colors" :class="reqs.lower ? 'text-green-600 font-medium' : 'text-gray-400'">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>Huruf kecil (a-z)</span>
                                </li>
                                <li class="flex items-center gap-2 transition-colors" :class="reqs.upper ? 'text-green-600 font-medium' : 'text-gray-400'">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>Huruf besar (A-Z)</span>
                                </li>
                                <li class="flex items-center gap-2 transition-colors" :class="reqs.number ? 'text-green-600 font-medium' : 'text-gray-400'">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    <span>Angka (0-9)</span>
                                </li>
                            </ul>
                        </div>
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
