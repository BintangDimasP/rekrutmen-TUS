@extends('layouts.admin')

@section('title', 'Profil Lengkap Pelamar')

@section('content')

<div class="space-y-6" x-data="{
    isEditing: {{ ($errors->any() || empty($pelamar->nik)) ? 'true' : 'false' }},
    showEdu2: {{ old('jenjang_2', $pelamar->jenjang_2) ? 'true' : 'false' }},
    showEdu3: {{ old('jenjang_3', $pelamar->jenjang_3) ? 'true' : 'false' }},
    jenjang1: '{{ old('jenjang', $pelamar->jenjang) }}',
    jenjang2: '{{ old('jenjang_2', $pelamar->jenjang_2) }}',
    jenjang3: '{{ old('jenjang_3', $pelamar->jenjang_3) }}'
}">

    <!-- Single Card -->
    <form id="mainProfilForm" action="{{ route('pelamar.profil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6 md:p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm ring-2 ring-white/30">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($pelamar->nama ?? 'P', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">{{ $pelamar->nama ?? '-' }}</h1>
                        <p class="text-red-200 text-sm mt-0.5">{{ $pelamar->user?->email }}</p>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-red-200 text-xs">Terdaftar: {{ $pelamar->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-show="!isEditing" @click="isEditing = true"
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-all shadow-sm" title="Edit Profil">
                        Edit Profil
                    </button>
                    <button type="button" x-show="isEditing" @click="isEditing = false"
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <!-- CONTENT: Full Profile -->
        <div class="p-6 md:p-8 space-y-8">

            {{-- 1. DATA DIRI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Data Diri
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Nama Lengkap</p>
                        <p x-show="!isEditing" class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->nama ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="nama" value="{{ old('nama',$pelamar->nama) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                        @error('nama')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIK (KTP)</p>
                        <p x-show="!isEditing" class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nik ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="nik" value="{{ old('nik',$pelamar->nik) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                        @error('nik')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Telepon / WA</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_telepon ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="no_telepon" value="{{ old('no_telepon',$pelamar->no_telepon) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                        @error('no_telepon')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tempat Lahir</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="tempat_lahir" value="{{ old('tempat_lahir',$pelamar->tempat_lahir) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                        @error('tempat_lahir')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Lahir</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p>
                        <input x-show="isEditing" x-cloak type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir',$pelamar->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        @error('tanggal_lahir')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Kelamin</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p>
                        <div x-show="isEditing" x-cloak class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $pelamar->jenis_kelamin)=='L'?'checked':'' }} class="text-[#8b1515] focus:ring-[#8b1515]"> Laki-laki</label>
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $pelamar->jenis_kelamin)=='P'?'checked':'' }} class="text-[#8b1515] focus:ring-[#8b1515]"> Perempuan</label>
                        </div>
                        @error('jenis_kelamin')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-span-2 md:col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Kewarganegaraan</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->kewarganegaraan ?: '-' }}</p>
                        <select x-show="isEditing" x-cloak name="kewarganegaraan" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            <option value="">-</option>
                            <option value="WNI" {{ old('kewarganegaraan',$pelamar->kewarganegaraan)=='WNI'?'selected':'' }}>WNI</option>
                            <option value="WNA" {{ old('kewarganegaraan',$pelamar->kewarganegaraan)=='WNA'?'selected':'' }}>WNA</option>
                        </select>
                        @error('kewarganegaraan')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-span-2 md:col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Status Pernikahan</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->status_pernikahan ?: '-' }}</p>
                        <select x-show="isEditing" x-cloak name="status_pernikahan" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            <option value="">-</option>
                            <option value="Belum Kawin" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Belum Kawin'?'selected':'' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Kawin'?'selected':'' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Cerai Hidup'?'selected':'' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Cerai Mati'?'selected':'' }}>Cerai Mati</option>
                        </select>
                        @error('status_pernikahan')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-span-2 md:col-span-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Domisili</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_domisili ?: '-' }}</p>
                        <textarea x-show="isEditing" x-cloak name="alamat_domisili" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">{{ old('alamat_domisili',$pelamar->alamat_domisili) }}</textarea>
                        @error('alamat_domisili')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-span-2 md:col-span-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Sesuai KTP</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_ktp ?: '-' }}</p>
                        <textarea x-show="isEditing" x-cloak name="alamat_ktp" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">{{ old('alamat_ktp',$pelamar->alamat_ktp) }}</textarea>
                        @error('alamat_ktp')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- 2. RIWAYAT PENDIDIKAN --}}
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Riwayat Pendidikan</h3>
                    <button type="button" x-show="isEditing && (!showEdu2 || !showEdu3)" x-cloak @click="if(!showEdu2){showEdu2=true}else{showEdu3=true}"
                        class="text-[0.65rem] font-black text-[#8b1515] px-3 py-1.5 rounded-lg bg-[#8b1515]/5 hover:bg-[#8b1515]/10 uppercase tracking-widest transition-colors">
                        + Tambah Jenjang
                    </button>
                </div>
                
                <div class="space-y-4">
                    {{-- Jenjang 1 --}}
                    <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-[#8b1515]/40 py-2 relative">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="jenjang" x-model="jenjang1" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="S1" :hidden="jenjang2 === 'S1' || jenjang3 === 'S1'" :disabled="jenjang2 === 'S1' || jenjang3 === 'S1'">S1</option>
                                <option value="S2" :hidden="jenjang2 === 'S2' || jenjang3 === 'S2'" :disabled="jenjang2 === 'S2' || jenjang3 === 'S2'">S2</option>
                                <option value="S3" :hidden="jenjang2 === 'S3' || jenjang3 === 'S3'" :disabled="jenjang2 === 'S3' || jenjang3 === 'S3'">S3</option>
                            </select>
                            @error('jenjang')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p>
                            <p x-show="!isEditing" class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="institusi" value="{{ old('institusi',$pelamar->institusi) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            @error('institusi')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan" value="{{ old('prodi_pendidikan',$pelamar->prodi_pendidikan) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="akreditas" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['A','B','C','Unggul','Baik Sekali','Baik','Tidak Terakreditasi'] as $akr)
                                <option value="{{ $akr }}" {{ old('akreditas',$pelamar->akreditas)==$akr?'selected':'' }}>{{ $akr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="no_ijazah" value="{{ old('no_ijazah',$pelamar->no_ijazah) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk" value="{{ old('ipk',$pelamar->ipk) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ijazah)<a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_ijazah" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_ijazah)<a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                            @error('file_ijazah')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_transkrip)<a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_transkrip" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_transkrip)<a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                            @error('file_transkrip')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Jenjang 2 --}}
                    <div x-show="showEdu2 || (!isEditing && {{ $pelamar->jenjang_2 ? 'true' : 'false' }})" class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2 relative" x-cloak>
                        <button type="button" x-show="isEditing" @click="showEdu2=false; jenjang2=''" class="absolute -left-2 -top-1 w-5 h-5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center text-[10px] font-bold" title="Hapus Jenjang">✕</button>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="jenjang_2" x-model="jenjang2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="S1" :hidden="jenjang1 === 'S1' || jenjang3 === 'S1'" :disabled="jenjang1 === 'S1' || jenjang3 === 'S1'">S1</option>
                                <option value="S2" :hidden="jenjang1 === 'S2' || jenjang3 === 'S2'" :disabled="jenjang1 === 'S2' || jenjang3 === 'S2'">S2</option>
                                <option value="S3" :hidden="jenjang1 === 'S3' || jenjang3 === 'S3'" :disabled="jenjang1 === 'S3' || jenjang3 === 'S3'">S3</option>
                            </select>
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p>
                            <p x-show="!isEditing" class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="institusi_2" value="{{ old('institusi_2',$pelamar->institusi_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan_2" value="{{ old('prodi_pendidikan_2',$pelamar->prodi_pendidikan_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_2 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="akreditas_2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['A','B','C','Unggul','Baik Sekali','Baik','Tidak Terakreditasi'] as $akr)
                                <option value="{{ $akr }}" {{ old('akreditas_2',$pelamar->akreditas_2)==$akr?'selected':'' }}>{{ $akr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="no_ijazah_2" value="{{ old('no_ijazah_2',$pelamar->no_ijazah_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk_2" value="{{ old('ipk_2',$pelamar->ipk_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_ijazah_2" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                        <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_transkrip_2" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                    </div>

                    {{-- Jenjang 3 --}}
                    <div x-show="showEdu3 || (!isEditing && {{ $pelamar->jenjang_3 ? 'true' : 'false' }})" class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2 relative" x-cloak>
                        <button type="button" x-show="isEditing" @click="showEdu3=false; jenjang3=''" class="absolute -left-2 -top-1 w-5 h-5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center text-[10px] font-bold" title="Hapus Jenjang">✕</button>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="jenjang_3" x-model="jenjang3" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="S1" :hidden="jenjang1 === 'S1' || jenjang2 === 'S1'" :disabled="jenjang1 === 'S1' || jenjang2 === 'S1'">S1</option>
                                <option value="S2" :hidden="jenjang1 === 'S2' || jenjang2 === 'S2'" :disabled="jenjang1 === 'S2' || jenjang2 === 'S2'">S2</option>
                                <option value="S3" :hidden="jenjang1 === 'S3' || jenjang2 === 'S3'" :disabled="jenjang1 === 'S3' || jenjang2 === 'S3'">S3</option>
                            </select>
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p>
                            <p x-show="!isEditing" class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="institusi_3" value="{{ old('institusi_3',$pelamar->institusi_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan_3" value="{{ old('prodi_pendidikan_3',$pelamar->prodi_pendidikan_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_3 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="akreditas_3" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['A','B','C','Unggul','Baik Sekali','Baik','Tidak Terakreditasi'] as $akr)
                                <option value="{{ $akr }}" {{ old('akreditas_3',$pelamar->akreditas_3)==$akr?'selected':'' }}>{{ $akr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="no_ijazah_3" value="{{ old('no_ijazah_3',$pelamar->no_ijazah_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk_3" value="{{ old('ipk_3',$pelamar->ipk_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_ijazah_3" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                        <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</p>
                            <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_transkrip_3" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                    </div>
                    
                    <p x-show="!isEditing && !{{ $pelamar->jenjang ? 'true' : 'false' }}" class="text-sm text-gray-400 italic">-</p>
                </div>
            </div>

            {{-- 3. DOKUMEN PENDUKUNG --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Dokumen Pendukung
                </h3>
                
                {{-- View Mode --}}
                <div x-show="!isEditing" class="space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-[0.6rem] font-black text-gray-400 uppercase">CV (Resume)</p>
                            @if($pelamar->file_cv)
                                <a href="{{ asset('storage/' . $pelamar->file_cv) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.6rem] font-black text-gray-400 uppercase">Pas Foto Formal</p>
                            @if($pelamar->file_pas_foto)
                                <a href="{{ asset('storage/' . $pelamar->file_pas_foto) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.6rem] font-black text-gray-400 uppercase">Scan KTP</p>
                            @if($pelamar->file_ktp)
                                <a href="{{ asset('storage/' . $pelamar->file_ktp) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.6rem] font-black text-gray-400 uppercase">{{ $pelamar->kategori_sertifikat ?: 'Sertifikat' }}</p>
                            @if($pelamar->file_sertifikat)
                                <a href="{{ asset('storage/' . $pelamar->file_sertifikat) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div x-show="isEditing" x-cloak class="space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4">
                        <div>
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">CV (Resume)</label>
                            <div class="mt-1">
                                <input type="file" name="file_cv" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_cv)<a href="{{ asset('storage/'.$pelamar->file_cv) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                        <div>
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Pas Foto Formal</label>
                            <div class="mt-1">
                                <input type="file" name="file_pas_foto" accept=".jpg,.jpeg" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_pas_foto)<a href="{{ asset('storage/'.$pelamar->file_pas_foto) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                        <div>
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Scan KTP</label>
                            <div class="mt-1">
                                <input type="file" name="file_ktp" accept=".jpg,.jpeg" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_ktp)<a href="{{ asset('storage/'.$pelamar->file_ktp) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[0.6rem] font-black text-gray-400 uppercase">Kategori Sertifikat</p>
                            <select name="kategori_sertifikat" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="kompetensi" {{ old('kategori_sertifikat',$pelamar->kategori_sertifikat)=='kompetensi'?'selected':'' }}>Kompetensi</option>
                                <option value="keahlian_khusus" {{ old('kategori_sertifikat',$pelamar->kategori_sertifikat)=='keahlian_khusus'?'selected':'' }}>Keahlian Khusus</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Profesi</label>
                            <div class="mt-1">
                                <input type="file" name="file_sertifikat" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_sertifikat)<a href="{{ asset('storage/'.$pelamar->file_sertifikat) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. SERTIFIKAT BAHASA INGGRIS --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Sertifikat Bahasa Inggris
                </h3>
                
                {{-- View Mode --}}
                <div x-show="!isEditing">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor Bahasa</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Sertifikat Bahasa</p>
                            @if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>@else<p class="text-sm text-gray-700 mt-0.5">-</p>@endif
                        </div>
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div x-show="isEditing" x-cloak>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p>
                            <select name="jenis_tes_bahasa" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['PBT','TOEFL_ITP','EPrT','CBT','IBT','IELTS','AcEPT'] as $tes)
                                <option value="{{ $tes }}" {{ old('jenis_tes_bahasa',$pelamar->jenis_tes_bahasa)==$tes?'selected':'' }}>{{ $tes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor Bahasa</p>
                            <input type="number" step="0.01" name="skor_bahasa" value="{{ old('skor_bahasa',$pelamar->skor_bahasa) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p>
                            <input type="date" name="tanggal_tes_bahasa" value="{{ old('tanggal_tes_bahasa',$pelamar->tanggal_tes_bahasa?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Bahasa</label>
                            <div class="mt-1"><input type="file" name="file_sertifikat_bahasa" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. DATA AKADEMIK (DOSEN) --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Data Akademik (Dosen)
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIDN</p>
                        <p x-show="!isEditing" class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nidn ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="nidn" value="{{ old('nidn',$pelamar->nidn) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Homebase</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->homebase ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="homebase" value="{{ old('homebase',$pelamar->homebase) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jabatan Akademik</p>
                        <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik)) : '-' }}</p>
                        <select x-show="isEditing" x-cloak name="jabatan_akademik" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            <option value="">-</option>
                            <option value="asisten_ahli" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='asisten_ahli'?'selected':'' }}>Asisten Ahli</option>
                            <option value="lektor" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='lektor'?'selected':'' }}>Lektor</option>
                            <option value="lektor_kepala" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='lektor_kepala'?'selected':'' }}>Lektor Kepala</option>
                            <option value="profesor" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='profesor'?'selected':'' }}>Profesor</option>
                        </select>
                    </div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">H-Index</p>
                        <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="number" name="h_index" value="{{ old('h_index',$pelamar->h_index) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                    </div>
                </div>
                <div class="mt-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Minat Riset & Keahlian</p>
                    <p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?: '-' }}</p>
                    <textarea x-show="isEditing" x-cloak name="minat_riset" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">{{ old('minat_riset',$pelamar->minat_riset) }}</textarea>
                </div>
            </div>

            {{-- 6. DOKUMEN PELAMAR BER-HOMEBASE --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Dokumen Pelamar Ber-Homebase
                </h3>
                
                {{-- View Mode --}}
                <div x-show="!isEditing">
                    @php
                        $homebaseDocs = [
                            ['label' => 'SK Jabatan Akademik (JAD)', 'file' => $pelamar->file_jad],
                            ['label' => 'SK Penetapan Angka Kredit (PAK)', 'file' => $pelamar->file_pak],
                            ['label' => 'Kartu Dosen', 'file' => $pelamar->file_kartu_dosen],
                            ['label' => 'Bukti Registrasi Dosen', 'file' => $pelamar->file_registrasi_dosen],
                            ['label' => 'SK Inpassing', 'file' => $pelamar->file_inpassing],
                            ['label' => 'Sertifikat Pendidik (Serdik)', 'file' => $pelamar->file_serdik],
                            ['label' => 'SKPP Serdos', 'file' => $pelamar->file_skpp_serdos],
                            ['label' => 'Surat Pernyataan Lolos Butuh', 'file' => $pelamar->file_pernyataan_lolos_butuh],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        @foreach($homebaseDocs as $doc)
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">{{ $doc['label'] }}</p>
                            @if($doc['file'])<a href="{{ asset('storage/'.$doc['file']) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>@else<p class="text-sm text-gray-700 mt-0.5">-</p>@endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div x-show="isEditing" x-cloak class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4">
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SK Jabatan Akademik (JAD)</label>
                        <div class="mt-1"><input type="file" name="file_jad" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_jad)<a href="{{ asset('storage/'.$pelamar->file_jad) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SK Angka Kredit (PAK)</label>
                        <div class="mt-1"><input type="file" name="file_pak" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_pak)<a href="{{ asset('storage/'.$pelamar->file_pak) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Kartu Dosen</label>
                        <div class="mt-1"><input type="file" name="file_kartu_dosen" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_kartu_dosen)<a href="{{ asset('storage/'.$pelamar->file_kartu_dosen) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Bukti Registrasi Dosen</label>
                        <div class="mt-1"><input type="file" name="file_registrasi_dosen" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_registrasi_dosen)<a href="{{ asset('storage/'.$pelamar->file_registrasi_dosen) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SK Inpassing</label>
                        <div class="mt-1"><input type="file" name="file_inpassing" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_inpassing)<a href="{{ asset('storage/'.$pelamar->file_inpassing) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Pendidik (Serdik)</label>
                        <div class="mt-1"><input type="file" name="file_serdik" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_serdik)<a href="{{ asset('storage/'.$pelamar->file_serdik) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SKPP Serdos</label>
                        <div class="mt-1"><input type="file" name="file_skpp_serdos" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_skpp_serdos)<a href="{{ asset('storage/'.$pelamar->file_skpp_serdos) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Surat Pernyataan Lolos Butuh</label>
                        <div class="mt-1"><input type="file" name="file_pernyataan_lolos_butuh" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_pernyataan_lolos_butuh)<a href="{{ asset('storage/'.$pelamar->file_pernyataan_lolos_butuh) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                </div>
            </div>

            <div x-show="isEditing" x-cloak class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" @click="isEditing = false" class="px-6 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-lg hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" class="px-8 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-lg shadow-md shadow-[#8b1515]/20 hover:bg-red-900 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
    </form>

   
</div>

@endsection
