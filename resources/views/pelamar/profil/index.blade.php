@extends('layouts.admin')
@section('title', 'Profil Lengkap Pelamar')
@section('content')

@if(session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
     x-transition:enter-end="opacity-100 translate-x-0"
     class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px]">
    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div class="flex-1"><h4 class="text-sm font-bold text-gray-800">Berhasil</h4><p class="text-xs text-gray-500">{{ session('success') }}</p></div>
</div>
@endif

<div class="max-w-4xl mx-auto" x-data="{
    isEditing: {{ ($errors->any() || empty($pelamar->nik)) ? 'true' : 'false' }},
    showEdu2: {{ $pelamar->jenjang_2 ? 'true' : 'false' }},
    showEdu3: {{ $pelamar->jenjang_3 ? 'true' : 'false' }}
}">
<form id="mainProfilForm" action="{{ route('pelamar.profil.update') }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

{{-- RED HEADER --}}
<div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6 md:p-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 ring-2 ring-white/30">
                <span class="text-2xl font-bold text-white">{{ strtoupper(substr($pelamar->nama ?? 'P', 0, 1)) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $pelamar->nama ?? '-' }}</h2>
                <p class="text-red-200 text-sm mt-0.5">{{ $pelamar->user?->email }}</p>
                <p class="text-red-300 text-xs mt-1">Bergabung: {{ $pelamar->created_at->format('d M Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" x-show="!isEditing" @click="isEditing = true"
                class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition-all" title="Edit Profil">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button type="button" x-show="isEditing" @click="isEditing = false"
                class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-all">Batal</button>
        </div>
    </div>
</div>

{{-- CARD BODY --}}
<div class="p-6 md:p-8 space-y-8">
<div>
<div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
<h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
<svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
Data Diri</h3></div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
<div class="md:col-span-2"><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Nama Lengkap</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold text-gray-800">{{ $pelamar->nama ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="nama" value="{{ old('nama',$pelamar->nama) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">NIK</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-mono">{{ $pelamar->nik ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="nik" value="{{ old('nik',$pelamar->nik) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">No. Telepon / WA</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 ">{{ $pelamar->no_telepon ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="no_telepon" value="{{ old('no_telepon',$pelamar->no_telepon) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Tempat Lahir</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 ">{{ $pelamar->tempat_lahir ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="tempat_lahir" value="{{ old('tempat_lahir',$pelamar->tempat_lahir) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Tanggal Lahir</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p>
<input x-show="isEditing" x-cloak type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir',$pelamar->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Jenis Kelamin</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin=='L'?'Laki-laki':($pelamar->jenis_kelamin=='P'?'Perempuan':'-') }}</p>
<div x-show="isEditing" x-cloak class="flex gap-4 mt-2">
<label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="L" {{ $pelamar->jenis_kelamin=='L'?'checked':'' }}> Laki-laki</label>
<label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="P" {{ $pelamar->jenis_kelamin=='P'?'checked':'' }}> Perempuan</label>
</div></div><div class="md:col-span-2"><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Alamat Lengkap</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat ?? '-' }}</p>
<textarea x-show="isEditing" x-cloak name="alamat" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">{{ old('alamat',$pelamar->alamat) }}</textarea></div></div></div><div>
<div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
<h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
<svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l-9-5 9-5 9 5-9 5-9 5 9 5z"/></svg>
Riwayat Pendidikan</h3><button type="button" x-show="isEditing && !showEdu3" x-cloak @click="if(!showEdu2){showEdu2=true}else{showEdu3=true}"
class="text-[0.65rem] font-black text-[#8b1515] px-3 py-1.5 rounded-lg bg-[#8b1515]/5 hover:bg-[#8b1515]/10 uppercase tracking-widest">+ Tambah Jenjang</button></div><div class='space-y-5'><div  class="pl-4 border-l-[3px] border-[#8b1515]/40 py-2">
<div class="flex items-center justify-between mb-3"><p class="text-[0.6rem] font-black text-[#8b1515] uppercase tracking-widest">Jenjang Utama</p></div>
<div class="grid grid-cols-2 md:grid-cols-5 gap-x-4 gap-y-3">
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Jenjang</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold text-[#8b1515]">{{ $pelamar->jenjang ?? '-' }}</p>
<select x-show="isEditing" x-cloak name="jenjang" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">
<option value="">-</option>
<option value="S1" {{ $pelamar->jenjang=='S1'?'selected':'' }}>S1</option>
<option value="S2" {{ $pelamar->jenjang=='S2'?'selected':'' }}>S2</option>
<option value="S3" {{ $pelamar->jenjang=='S3'?'selected':'' }}>S3</option>
</select></div>
<div class="col-span-2"><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Institusi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->institusi ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="institusi" value="{{ old('institusi',$pelamar->institusi) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Prodi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan" value="{{ old('prodi_pendidikan',$pelamar->prodi_pendidikan) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">IPK</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold">{{ $pelamar->ipk ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk" value="{{ old('ipk',$pelamar->ipk) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Ijazah</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ijazah)<a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_ijazah" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_ijazah)<a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank" class="text-blue-600 underline text-xs">Ada</a>@endif</div></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Transkrip</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_transkrip)<a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_transkrip" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_transkrip)<a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank" class="text-blue-600 underline text-xs">Ada</a>@endif</div></div>
</div></div><div x-show="showEdu2" x-cloak class="pl-4 border-l-[3px] border-gray-300 py-2">
<div class="flex items-center justify-between mb-3"><p class="text-[0.6rem] font-black text-[#8b1515] uppercase tracking-widest">Jenjang Ke-2</p><button type="button" @click="showEdu2=false" class="text-[0.6rem] text-gray-400 hover:text-red-600 font-black">✕</button></div>
<div class="grid grid-cols-2 md:grid-cols-5 gap-x-4 gap-y-3">
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Jenjang</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold text-[#8b1515]">{{ $pelamar->jenjang_2 ?? '-' }}</p>
<select x-show="isEditing" x-cloak name="jenjang_2" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">
<option value="">-</option>
<option value="S1" {{ $pelamar->jenjang_2=='S1'?'selected':'' }}>S1</option>
<option value="S2" {{ $pelamar->jenjang_2=='S2'?'selected':'' }}>S2</option>
<option value="S3" {{ $pelamar->jenjang_2=='S3'?'selected':'' }}>S3</option>
</select></div>
<div class="col-span-2"><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Institusi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->institusi_2 ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="institusi_2" value="{{ old('institusi_2',$pelamar->institusi_2) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Prodi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan_2" value="{{ old('prodi_pendidikan_2',$pelamar->prodi_pendidikan_2) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">IPK</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold">{{ $pelamar->ipk_2 ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk_2" value="{{ old('ipk_2',$pelamar->ipk_2) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Ijazah</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_ijazah_2" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank" class="text-blue-600 underline text-xs">Ada</a>@endif</div></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Transkrip</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_transkrip_2" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank" class="text-blue-600 underline text-xs">Ada</a>@endif</div></div>
</div></div><div x-show="showEdu3" x-cloak class="pl-4 border-l-[3px] border-gray-300 py-2">
<div class="flex items-center justify-between mb-3"><p class="text-[0.6rem] font-black text-[#8b1515] uppercase tracking-widest">Jenjang Ke-3</p><button type="button" @click="showEdu3=false" class="text-[0.6rem] text-gray-400 hover:text-red-600 font-black">✕</button></div>
<div class="grid grid-cols-2 md:grid-cols-5 gap-x-4 gap-y-3">
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Jenjang</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold text-[#8b1515]">{{ $pelamar->jenjang_3 ?? '-' }}</p>
<select x-show="isEditing" x-cloak name="jenjang_3" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">
<option value="">-</option>
<option value="S1" {{ $pelamar->jenjang_3=='S1'?'selected':'' }}>S1</option>
<option value="S2" {{ $pelamar->jenjang_3=='S2'?'selected':'' }}>S2</option>
<option value="S3" {{ $pelamar->jenjang_3=='S3'?'selected':'' }}>S3</option>
</select></div>
<div class="col-span-2"><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Institusi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->institusi_3 ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="institusi_3" value="{{ old('institusi_3',$pelamar->institusi_3) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Prodi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan_3" value="{{ old('prodi_pendidikan_3',$pelamar->prodi_pendidikan_3) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">IPK</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold">{{ $pelamar->ipk_3 ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk_3" value="{{ old('ipk_3',$pelamar->ipk_3) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Ijazah</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_ijazah_3" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank" class="text-blue-600 underline text-xs">Ada</a>@endif</div></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Transkrip</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_transkrip_3" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank" class="text-blue-600 underline text-xs">Ada</a>@endif</div></div>
</div></div></div></div><div>
<div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
<h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
<svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
Dokumen & Sertifikat</h3></div><div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4"><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">CV (Resume)</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_cv)<a href="{{ asset('storage/'.$pelamar->file_cv) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_cv" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_cv)<a href="{{ asset('storage/'.$pelamar->file_cv) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Pas Foto Formal</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_pas_foto)<a href="{{ asset('storage/'.$pelamar->file_pas_foto) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_pas_foto" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_pas_foto)<a href="{{ asset('storage/'.$pelamar->file_pas_foto) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Scan KTP</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_ktp)<a href="{{ asset('storage/'.$pelamar->file_ktp) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_ktp" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_ktp)<a href="{{ asset('storage/'.$pelamar->file_ktp) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Profesi</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_sertifikat)<a href="{{ asset('storage/'.$pelamar->file_sertifikat) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_sertifikat" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_sertifikat)<a href="{{ asset('storage/'.$pelamar->file_sertifikat) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div></div><div class="mt-6 pt-4 border-t border-gray-50">
<p class="text-[0.65rem] font-black text-[#8b1515] uppercase tracking-widest mb-3">Kemampuan Bahasa</p>
<div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Jenis Tes Bahasa</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?? '-' }}</p>
<select x-show="isEditing" x-cloak name="jenis_tes_bahasa" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">
<option value="">-</option>
@foreach(['PBT','TOEFL_ITP','EPrT','CBT','IBT','IELTS','AcEPT'] as $tes)
<option value="{{ $tes }}" {{ $pelamar->jenis_tes_bahasa==$tes?'selected':'' }}>{{ $tes }}</option>
@endforeach</select></div>
<div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Skor</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-bold">{{ $pelamar->skor_bahasa ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="number" step="0.01" name="skor_bahasa" value="{{ old('skor_bahasa',$pelamar->skor_bahasa) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Bahasa</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_sertifikat_bahasa" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Tanggal Tes</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p>
<input x-show="isEditing" x-cloak type="date" name="tanggal_tes_bahasa" value="{{ old('tanggal_tes_bahasa',$pelamar->tanggal_tes_bahasa?->format('Y-m-d')) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></div></div></div></div><div>
<div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
<h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
<svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
Data Akademik (Dosen)</h3></div><div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4"><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">NIDN</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 font-mono">{{ $pelamar->nidn ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="nidn" value="{{ old('nidn',$pelamar->nidn) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Homebase Saat Ini</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 ">{{ $pelamar->homebase ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="text" name="homebase" value="{{ old('homebase',$pelamar->homebase) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Jabatan Akademik</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ucwords(str_replace('_',' ',$pelamar->jabatan_akademik)) : '-' }}</p>
<select x-show="isEditing" x-cloak name="jabatan_akademik" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">
<option value="">-</option>
<option value="asisten_ahli" {{ $pelamar->jabatan_akademik=='asisten_ahli'?'selected':'' }}>Asisten Ahli</option>
<option value="lektor" {{ $pelamar->jabatan_akademik=='lektor'?'selected':'' }}>Lektor</option>
<option value="lektor_kepala" {{ $pelamar->jabatan_akademik=='lektor_kepala'?'selected':'' }}>Lektor Kepala</option>
<option value="profesor" {{ $pelamar->jabatan_akademik=='profesor'?'selected':'' }}>Profesor</option>
</select></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">H-Index Scopus</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 ">{{ $pelamar->h_index ?? '-' }}</p>
<input x-show="isEditing" x-cloak type="number" name="h_index" value="{{ old('h_index',$pelamar->h_index) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all" ></div><div class="md:col-span-4"><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Minat Riset & Keahlian</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?? '-' }}</p>
<textarea x-show="isEditing" x-cloak name="minat_riset" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all">{{ old('minat_riset',$pelamar->minat_riset) }}</textarea></div></div></div><div>
<div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
<h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
<svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
Dokumen Pelamar Ber-Homebase</h3></div><div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4"><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SK Jabatan Akademik (JAD)</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_jad)<a href="{{ asset('storage/'.$pelamar->file_jad) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_jad" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_jad)<a href="{{ asset('storage/'.$pelamar->file_jad) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SK Angka Kredit (PAK)</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_pak)<a href="{{ asset('storage/'.$pelamar->file_pak) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_pak" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_pak)<a href="{{ asset('storage/'.$pelamar->file_pak) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Kartu Dosen</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_kartu_dosen)<a href="{{ asset('storage/'.$pelamar->file_kartu_dosen) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_kartu_dosen" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_kartu_dosen)<a href="{{ asset('storage/'.$pelamar->file_kartu_dosen) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Bukti Registrasi Dosen</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_registrasi_dosen)<a href="{{ asset('storage/'.$pelamar->file_registrasi_dosen) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_registrasi_dosen" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_registrasi_dosen)<a href="{{ asset('storage/'.$pelamar->file_registrasi_dosen) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SK Inpassing</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_inpassing)<a href="{{ asset('storage/'.$pelamar->file_inpassing) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_inpassing" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_inpassing)<a href="{{ asset('storage/'.$pelamar->file_inpassing) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Pendidik (Serdik)</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_serdik)<a href="{{ asset('storage/'.$pelamar->file_serdik) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_serdik" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_serdik)<a href="{{ asset('storage/'.$pelamar->file_serdik) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">SKPP Serdos</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_skpp_serdos)<a href="{{ asset('storage/'.$pelamar->file_skpp_serdos) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_skpp_serdos" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_skpp_serdos)<a href="{{ asset('storage/'.$pelamar->file_skpp_serdos) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div><div><label class="text-[0.6rem] font-black text-gray-400 uppercase mb-0.5 block">Surat Pernyataan Lolos Butuh</label>
<p x-show="!isEditing" class="text-sm text-gray-700 mt-0.5">@if($pelamar->file_pernyataan_lolos_butuh)<a href="{{ asset('storage/'.$pelamar->file_pernyataan_lolos_butuh) }}" target="_blank" class="text-blue-600 underline text-xs font-bold">Lihat File</a>@else-@endif</p>
<div x-show="isEditing" x-cloak><input type="file" name="file_pernyataan_lolos_butuh" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515]">@if($pelamar->file_pernyataan_lolos_butuh)<a href="{{ asset('storage/'.$pelamar->file_pernyataan_lolos_butuh) }}" target="_blank" class="text-blue-600 underline text-xs ml-1">Ada</a>@endif</div></div></div></div>@php
    $jadwals = \App\Models\JadwalSeleksi::where('pelamar_id', $pelamar->id)->with('penilaian')->get();
    $wawancara = $jadwals->where('tipe_seleksi', 'tahap1')->first();
    $micro = $jadwals->where('tipe_seleksi', 'tahap2')->first();
@endphp
@if($wawancara || $micro)
<div>
<h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
<svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
Hasil Penilaian Seleksi</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
@foreach([
    ['title'=>'Wawancara','jadwal'=>$wawancara,'k1'=>'Kepribadian & Integritas','k2'=>'Visi & Profesionalisme','k3'=>'Adaptasi & Kolaborasi'],
    ['title'=>'Micro Teaching','jadwal'=>$micro,'k1'=>'Penguasaan Materi','k2'=>'Keterampilan Pedagogik','k3'=>'Media Pembelajaran']
] as $test)
<div class="rounded-xl border border-gray-100 p-5 bg-gray-50/50">
    <p class="text-xs font-black text-[#8b1515] uppercase tracking-widest mb-3 pb-2 border-b border-gray-200">{{ $test['title'] }}</p>
    <div class="space-y-2.5">
        <div class="flex justify-between"><span class="text-[0.65rem] font-bold text-gray-500 uppercase">{{ $test['k1'] }}</span><span class="text-sm font-bold text-gray-800">{{ $test['jadwal']?->penilaian?->kategori_1 ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-[0.65rem] font-bold text-gray-500 uppercase">{{ $test['k2'] }}</span><span class="text-sm font-bold text-gray-800">{{ $test['jadwal']?->penilaian?->kategori_2 ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-[0.65rem] font-bold text-gray-500 uppercase">{{ $test['k3'] }}</span><span class="text-sm font-bold text-gray-800">{{ $test['jadwal']?->penilaian?->kategori_3 ?? '-' }}</span></div>
        <div class="pt-2 border-t border-gray-200 flex justify-between items-center">
            <span class="text-xs font-black text-gray-800 uppercase">Total Nilai Akhir</span>
            <span class="text-2xl font-black text-[#8b1515]">{{ $test['jadwal']?->penilaian?->total_nilai ?? '-' }}</span>
        </div>
    </div>
</div>
@endforeach
</div></div>
@endif
<div x-show="isEditing" x-cloak class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
<button type="button" @click="isEditing = false" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all">Batal</button>
<button type="submit" class="px-8 py-3 bg-[#8b1515] text-white font-bold rounded-xl shadow-lg shadow-[#8b1515]/20 hover:scale-[1.02] transition-all flex items-center gap-2">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
Simpan Perubahan</button>
</div>
        </div>
    </div>
</form>
</div>
@endsection
