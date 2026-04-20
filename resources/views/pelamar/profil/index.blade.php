@extends('layouts.admin')

@section('title', 'Profil Lengkap Pelamar')

@section('content')

    {{-- Toast Notification --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800">Berhasil</h4>
                <p class="text-[0.75rem] text-gray-500">{{ session('success') }}</p>
            </div>
        </div>
    @endif

<div class="max-w-5xl mx-auto space-y-8" x-data="{ 
    showEdu2: {{ $pelamar->jenjang_2 ? 'true' : 'false' }}, 
    showEdu3: {{ $pelamar->jenjang_3 ? 'true' : 'false' }},
    get usedJenjang() {
        let used = [];
        if ('{{ $pelamar->jenjang }}') used.push('{{ $pelamar->jenjang }}');
        // We'll calculate dynamic dropdowns based on current selections
        return used;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Profil Lengkap</h1>
            <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-medium">Manajemen Data Pribadi & Karir Akademik</p>
        </div>
        <button type="submit" form="mainProfilForm" class="px-8 py-3 bg-[#8b1515] text-white font-bold rounded-xl shadow-lg shadow-[#8b1515]/20 hover:scale-[1.02] transition-all">
            Simpan Semua Perubahan
        </button>
    </div>

    <form id="mainProfilForm" action="{{ route('pelamar.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. DATA DIRI --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Data Diri Dasar
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $pelamar->nama) }}" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] focus:ring-4 focus:ring-[#8b1515]/5 transition-all font-bold text-gray-700">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">NIK (KTP)</label>
                    <input type="text" name="nik" value="{{ old('nik', $pelamar->nik) }}" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm font-mono text-gray-600">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">No. Telepon / WA</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $pelamar->no_telepon) }}" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pelamar->tempat_lahir) }}" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pelamar->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">Jenis Kelamin</label>
                    <div class="flex items-center gap-4 mt-2">
                        <label class="flex items-center gap-2 text-sm cursor-pointer group">
                            <input type="radio" name="jenis_kelamin" value="L" {{ $pelamar->jenis_kelamin == 'L' ? 'checked' : '' }} class="w-4 h-4 text-[#8b1515] focus:ring-[#8b1515]">
                            <span class="text-gray-600 group-hover:text-gray-900 transition-colors">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer group">
                            <input type="radio" name="jenis_kelamin" value="P" {{ $pelamar->jenis_kelamin == 'P' ? 'checked' : '' }} class="w-4 h-4 text-[#8b1515] focus:ring-[#8b1515]">
                            <span class="text-gray-600 group-hover:text-gray-900 transition-colors">Perempuan</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-tighter">Alamat Lengkap</label>
                    <textarea name="alamat" rows="1" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm">{{ old('alamat', $pelamar->alamat) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. RIWAYAT PENDIDIKAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l-9-5 9-5 9 5-9 5-9 5 9 5z"/></svg>
                    Riwayat Pendidikan
                </h2>
                <button type="button" 
                        @click="if(!showEdu2) { showEdu2 = true } else if(!showEdu3) { showEdu3 = true }" 
                        x-show="!showEdu3"
                        class="text-[0.65rem] font-black text-[#8b1515] px-3 py-1.5 rounded-lg bg-[#8b1515]/5 hover:bg-[#8b1515]/10 transition-colors uppercase tracking-widest">
                    + Tambah Jenjang
                </button>
            </div>
            
            <div class="divide-y divide-gray-50">
                {{-- Level 1 --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-4 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-[0.6rem] font-black text-gray-400 tracking-tighter">01</span>
                        <span class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest">Jenjang Utama</span>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</label>
                        <select name="jenjang" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-gray-50 text-xs font-bold text-gray-700">
                            <option value="S1" {{ $pelamar->jenjang == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                            <option value="S2" {{ $pelamar->jenjang == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                            <option value="S3" {{ $pelamar->jenjang == 'S3' ? 'selected' : '' }}>S3 (Doktor)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</label>
                        <input type="text" name="institusi" value="{{ old('institusi', $pelamar->institusi) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-gray-50 text-xs">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</label>
                        <input type="number" step="0.01" name="ipk" value="{{ old('ipk', $pelamar->ipk) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-gray-50 text-xs">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</label>
                        <input type="text" name="prodi_pendidikan" value="{{ old('prodi_pendidikan', $pelamar->prodi_pendidikan) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-gray-50 text-xs">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</label>
                        <input type="file" name="file_ijazah" class="text-[0.65rem] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] hover:file:bg-[#8b1515]/20">
                        @if($pelamar->file_ijazah) <p class="mt-1 text-[0.6rem] text-blue-600 font-bold truncate underline"><a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank">File Tersedia</a></p> @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</label>
                        <input type="file" name="file_transkrip" class="text-[0.65rem] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] hover:file:bg-[#8b1515]/20">
                        @if($pelamar->file_transkrip) <p class="mt-1 text-[0.6rem] text-blue-600 font-bold truncate underline"><a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank">File Tersedia</a></p> @endif
                    </div>
                </div>

                {{-- Level 2 --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50/20" x-show="showEdu2" x-cloak x-transition>
                    <div class="md:col-span-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                             <span class="w-6 h-6 rounded-lg bg-[#8b1515]/10 flex items-center justify-center text-[0.6rem] font-black text-[#8b1515] tracking-tighter">02</span>
                             <span class="text-[0.65rem] font-black text-gray-800 uppercase tracking-widest">Jenjang Tambahan</span>
                        </div>
                        <button type="button" @click="showEdu2 = false" class="text-[0.6rem] text-gray-400 hover:text-red-600 font-black uppercase">✕ Hapus Section</button>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</label>
                        <select name="jenjang_2" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs font-bold text-gray-700">
                             <option value="" selected>— Pilih —</option>
                             <option value="S1" {{ $pelamar->jenjang_2 == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                             <option value="S2" {{ $pelamar->jenjang_2 == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                             <option value="S3" {{ $pelamar->jenjang_2 == 'S3' ? 'selected' : '' }}>S3 (Doktor)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</label>
                        <input type="text" name="institusi_2" value="{{ old('institusi_2', $pelamar->institusi_2) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</label>
                        <input type="number" step="0.01" name="ipk_2" value="{{ old('ipk_2', $pelamar->ipk_2) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</label>
                        <input type="text" name="prodi_pendidikan_2" value="{{ old('prodi_pendidikan_2', $pelamar->prodi_pendidikan_2) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</label>
                        <input type="file" name="file_ijazah_2" class="text-[0.65rem] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-gray-100">
                        @if($pelamar->file_ijazah_2) <p class="mt-1 text-[0.6rem] text-blue-600 font-bold truncate underline"><a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank">File Tersedia</a></p> @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</label>
                        <input type="file" name="file_transkrip_2" class="text-[0.65rem] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-gray-100">
                        @if($pelamar->file_transkrip_2) <p class="mt-1 text-[0.6rem] text-blue-600 font-bold truncate underline"><a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank">File Tersedia</a></p> @endif
                    </div>
                </div>

                {{-- Level 3 --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50/20" x-show="showEdu3" x-cloak x-transition>
                    <div class="md:col-span-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                             <span class="w-6 h-6 rounded-lg bg-[#8b1515]/10 flex items-center justify-center text-[0.6rem] font-black text-[#8b1515] tracking-tighter">03</span>
                             <span class="text-[0.65rem] font-black text-gray-800 uppercase tracking-widest">Jenjang Tambahan</span>
                        </div>
                        <button type="button" @click="showEdu3 = false" class="text-[0.6rem] text-gray-400 hover:text-red-600 font-black uppercase">✕ Hapus Section</button>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</label>
                        <select name="jenjang_3" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs font-bold text-gray-700">
                             <option value="" selected>— Pilih —</option>
                             <option value="S1" {{ $pelamar->jenjang_3 == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                             <option value="S2" {{ $pelamar->jenjang_3 == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                             <option value="S3" {{ $pelamar->jenjang_3 == 'S3' ? 'selected' : '' }}>S3 (Doktor)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</label>
                        <input type="text" name="institusi_3" value="{{ old('institusi_3', $pelamar->institusi_3) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</label>
                        <input type="number" step="0.01" name="ipk_3" value="{{ old('ipk_3', $pelamar->ipk_3) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</label>
                        <input type="text" name="prodi_pendidikan_3" value="{{ old('prodi_pendidikan_3', $pelamar->prodi_pendidikan_3) }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-100 bg-white text-xs">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</label>
                        <input type="file" name="file_ijazah_3" class="text-[0.65rem] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-gray-100">
                        @if($pelamar->file_ijazah_3) <p class="mt-1 text-[0.6rem] text-blue-600 font-bold truncate underline"><a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank">File Tersedia</a></p> @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</label>
                        <input type="file" name="file_transkrip_3" class="text-[0.65rem] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-gray-100">
                        @if($pelamar->file_transkrip_3) <p class="mt-1 text-[0.6rem] text-blue-600 font-bold truncate underline"><a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank">File Tersedia</a></p> @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 3. DOKUMEN PENDUKUNG --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                         <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                         Dokumen & Sertifikat
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5 flex flex-col">
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase">Pas Foto Formal</label>
                            <input type="file" name="file_pas_foto" class="text-[0.65rem]">
                            @if($pelamar->file_pas_foto) <a href="{{ asset('storage/'.$pelamar->file_pas_foto) }}" target="_blank" class="text-[0.6rem] text-blue-600 font-bold mt-1 underline">Lihat Foto</a> @endif
                        </div>
                        <div class="space-y-1.5 flex flex-col">
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase">Scan KTP</label>
                            <input type="file" name="file_ktp" class="text-[0.65rem]">
                            @if($pelamar->file_ktp) <a href="{{ asset('storage/'.$pelamar->file_ktp) }}" target="_blank" class="text-[0.6rem] text-blue-600 font-bold mt-1 underline">Lihat KTP</a> @endif
                        </div>
                    </div>
                    <div class="space-y-1.5 flex flex-col">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">CV (Resume)</label>
                        <input type="file" name="file_cv" class="text-[0.65rem]">
                        @if($pelamar->file_cv) <a href="{{ asset('storage/'.$pelamar->file_cv) }}" target="_blank" class="text-[0.6rem] text-blue-600 font-bold mt-1 underline">Lihat CV Saat Ini</a> @endif
                    </div>
                    <hr class="border-gray-50">
                    <div class="space-y-3">
                        <label class="text-[0.6rem] font-black text-[#8b1515] uppercase tracking-widest">Kemampuan Bahasa</label>
                        <div class="grid grid-cols-2 gap-4 text-xs font-medium">
                             <div class="space-y-1.5">
                                 <label class="text-[0.55rem] text-gray-400 uppercase">Jenis Tes</label>
                                 <select name="jenis_tes_bahasa" class="w-full px-2 py-1.5 rounded bg-gray-50 border-0">
                                     <option value="">—</option>
                                     @foreach(['PBT','TOEFL_ITP','EPrT','CBT','IBT','IELTS','AcEPT'] as $tes)
                                        <option value="{{ $tes }}" {{ $pelamar->jenis_tes_bahasa == $tes ? 'selected' : '' }}>{{ $tes }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="space-y-1.5">
                                 <label class="text-[0.55rem] text-gray-400 uppercase">Skor</label>
                                 <input type="number" step="0.01" name="skor_bahasa" value="{{ old('skor_bahasa', $pelamar->skor_bahasa) }}" class="w-full px-2 py-1.5 rounded bg-gray-50 border-0">
                             </div>
                        </div>
                        <div class="space-y-1.5 flex flex-col">
                            <label class="text-[0.55rem] text-gray-400 uppercase">File Sertifikat Bahasa</label>
                            <input type="file" name="file_sertifikat_bahasa" class="text-[0.6rem]">
                            @if($pelamar->file_sertifikat_bahasa) <a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-[0.55rem] text-blue-600 font-bold underline">Cek Sertifikat</a> @endif
                        </div>
                    </div>
                </div>
                  {{-- 4. RIWAYAT AKADEMIK --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                         <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                         Data Akademik (Dosen)
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase">NIDN (Opsional)</label>
                            <input type="text" name="nidn" value="{{ old('nidn', $pelamar->nidn) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-xs">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase">H-Index</label>
                            <input type="number" name="h_index" value="{{ old('h_index', $pelamar->h_index) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-xs">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase">Homebase Saat Ini</label>
                            <input type="text" name="homebase" value="{{ old('homebase', $pelamar->homebase) }}" placeholder="Contoh: Univ. Telkom" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-xs">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase">Jabatan Akademik</label>
                            <select name="jabatan_akademik" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-xs">
                                <option value="">— Pilih Jabatan —</option>
                                <option value="asisten_ahli" {{ $pelamar->jabatan_akademik == 'asisten_ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                                <option value="lektor" {{ $pelamar->jabatan_akademik == 'lektor' ? 'selected' : '' }}>Lektor</option>
                                <option value="lektor_kepala" {{ $pelamar->jabatan_akademik == 'lektor_kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                                <option value="profesor" {{ $pelamar->jabatan_akademik == 'profesor' ? 'selected' : '' }}>Profesor</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase">Kartu Dosen</label>
                        <input type="file" name="file_kartu_dosen" class="text-xs">
                        @if($pelamar->file_kartu_dosen) <a href="{{ asset('storage/'.$pelamar->file_kartu_dosen) }}" target="_blank" class="text-blue-600 underline">Lihat File</a> @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[0.6rem] font-black text-gray-400 uppercase tracking-tighter">Minat Riset & Keahlian</label>
                        <textarea name="minat_riset" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-xs">{{ old('minat_riset', $pelamar->minat_riset) }}</textarea>
                    </div>

                    {{-- DOKUMEN TAMBAHAN (HOMEBASE) --}}
                    <div class="pt-4 border-t border-gray-50 space-y-4">
                        <h4 class="text-[0.65rem] font-black text-[#8b1515] uppercase tracking-widest bg-[#8b1515]/5 px-3 py-1.5 rounded-lg inline-block italic">Dokumen Pelamar Ber-Homebase</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="space-y-1.5 flex flex-col">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase">SK Jabatan Akademik (JAD)</label>
                                <input type="file" name="file_jad" class="text-[0.6rem]">
                                @if($pelamar->file_jad) <a href="{{ asset('storage/'.$pelamar->file_jad) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                            <div class="space-y-1.5 flex flex-col">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase">SK Penetapan Angka Kredit (PAK)</label>
                                <input type="file" name="file_pak" class="text-[0.6rem]">
                                @if($pelamar->file_pak) <a href="{{ asset('storage/'.$pelamar->file_pak) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                            <div class="space-y-1.5 flex flex-col">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase">Bukti Registrasi Dosen</label>
                                <input type="file" name="file_registrasi_dosen" class="text-[0.6rem]">
                                @if($pelamar->file_registrasi_dosen) <a href="{{ asset('storage/'.$pelamar->file_registrasi_dosen) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                            <div class="space-y-1.5 flex flex-col">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase">SK Inpassing (Pangkat)</label>
                                <input type="file" name="file_inpassing" class="text-[0.6rem]">
                                @if($pelamar->file_inpassing) <a href="{{ asset('storage/'.$pelamar->file_inpassing) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                            <div class="space-y-1.5 flex flex-col">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase">Sertifikat Pendidik (Serdik)</label>
                                <input type="file" name="file_serdik" class="text-[0.6rem]">
                                @if($pelamar->file_serdik) <a href="{{ asset('storage/'.$pelamar->file_serdik) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                            <div class="space-y-1.5 flex flex-col">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase">SKPP Serdos</label>
                                <input type="file" name="file_skpp_serdos" class="text-[0.6rem]">
                                @if($pelamar->file_skpp_serdos) <a href="{{ asset('storage/'.$pelamar->file_skpp_serdos) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                            <div class="space-y-1.5 flex flex-col md:col-span-2">
                                <label class="text-[0.55rem] font-black text-gray-400 uppercase flex items-center gap-2">
                                    Surat Pernyataan Lolos Butuh
                                    <a href="https://bit.ly/Surat-Pernyataan-Lolos-Butuh" target="_blank" class="text-[#8b1515] hover:underline normal-case italic font-medium">(Download Format di Sini)</a>
                                </label>
                                <input type="file" name="file_pernyataan_lolos_butuh" class="text-[0.6rem]">
                                @if($pelamar->file_pernyataan_lolos_butuh) <a href="{{ asset('storage/'.$pelamar->file_pernyataan_lolos_butuh) }}" target="_blank" class="text-blue-600 underline text-[0.55rem]">Lihat Berkas</a> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>            </div>
        </div>

        <div class="flex items-center justify-center pt-6">
            <button type="submit" class="flex items-center gap-3 px-12 py-4 bg-[#8b1515] text-white font-black text-sm rounded-2xl shadow-2xl shadow-[#8b1515]/30 hover:scale-105 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                UPDATE PROFIL SEKARANG
            </button>
        </div>
    </form>
</div>
@endsection
