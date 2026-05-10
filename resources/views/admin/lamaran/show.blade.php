@extends('layouts.admin')

@section('title', 'Detail Lamaran — ' . $lamaran->pelamar->nama)

@section('content')
@php
    $pelamar = $lamaran->pelamar;
@endphp

    {{-- Toast --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.lowongan.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Lowongan</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.lowongan.show', $lamaran->lowongan_id) }}" class="hover:text-[#8b1515] transition-colors font-medium">{{ $lamaran->lowongan->nama_posisi }}</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $pelamar->nama }}</span>
    </div>

    <!-- Single Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6 md:p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm ring-2 ring-white/30">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($pelamar->nama, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">{{ $pelamar->nama }}</h1>
                        <p class="text-red-200 text-sm mt-0.5">Melamar Posisi: <strong class="text-white">{{ $lamaran->lowongan->nama_posisi }}</strong></p>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-red-200 text-xs">Dilamar pada: {{ $lamaran->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    @php
                        $statusColors = [
                            'menunggu'       => 'bg-white/20 text-white border-white/30',
                            'seleksi_tahap1' => 'bg-white text-blue-700 border-white',
                            'seleksi_tahap2' => 'bg-white text-indigo-700 border-white',
                            'diterima'       => 'bg-white text-green-700 border-white',
                            'ditolak'        => 'bg-white text-red-700 border-white',
                        ];
                        $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                    @endphp
                    <span class="inline-flex px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest border backdrop-blur-sm shadow-sm {{ $colorClass }}">
                        {{ $lamaran->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- CONTENT: Full Profile & Status -->
        <div class="p-6 md:p-8 space-y-8">

            {{-- 1. DATA DIRI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Data Diri
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Nama Lengkap</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->nama ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIK (KTP)</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nik ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Telepon / WA</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_telepon ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Kelamin</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tempat Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p></div>
                    <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Lengkap</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat ?: '-' }}</p></div>
                </div>
            </div>

            {{-- 2. DOKUMEN PENDUKUNG LAMARAN (SURAT LAMARAN DLL) --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Dokumen Pendukung Lamaran
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($lamaran->file_surat_lamaran)
                    <a href="{{ Storage::url($lamaran->file_surat_lamaran) }}" target="_blank" class="flex items-center justify-between p-5 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all bg-gray-50/50 group">
                        <div>
                            <p class="text-sm font-bold text-gray-800 transition-colors">Surat Lamaran</p>
                            <p class="text-[0.7rem] text-gray-500 mt-1 font-medium">Dokumen PDF/Word</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 hover:bg-[#8b1515] hover:text-white hover:border-[#8b1515] transition-colors shadow-sm">Preview</span>
                    </a>
                    @endif

                    @if($lamaran->file_berkas_pendukung)
                    <a href="{{ Storage::url($lamaran->file_berkas_pendukung) }}" target="_blank" class="flex items-center justify-between p-5 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all bg-gray-50/50 group">
                        <div>
                            <p class="text-sm font-bold text-gray-800 transition-colors">Berkas Pendukung</p>
                            <p class="text-[0.7rem] text-gray-500 mt-1 font-medium">Dokumen Lampiran</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 hover:bg-[#8b1515] hover:text-white hover:border-[#8b1515] transition-colors shadow-sm">Preview</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- 3. JADWAL & HASIL PENILAIAN SELEKSI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    {{ ($wawancara && $wawancara->penilaian) || ($micro && $micro->penilaian) ? 'Nilai Hasil Seleksi' : 'Jadwal Seleksi' }}
                </h3>
                
                @if($wawancara || $micro)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($wawancara)
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm relative overflow-hidden group hover:border-gray-300 hover:shadow-md transition-all">
                            <div class="absolute top-0 left-0 w-full h-1 bg-[#8b1515]"></div>
                            
                            @if($wawancara->penilaian)
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-4">Wawancara</h4>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100/50">
                                    <span class="text-xs font-semibold text-gray-700">Kompetensi Kepribadian</span>
                                    <span class="text-sm font-black text-[#8b1515]">{{ $wawancara->penilaian->kategori_1 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100/50">
                                    <span class="text-xs font-semibold text-gray-700">Visi Tri Dharma</span>
                                    <span class="text-sm font-black text-[#8b1515]">{{ $wawancara->penilaian->kategori_2 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100/50">
                                    <span class="text-xs font-semibold text-gray-700">Kemampuan Adaptasi</span>
                                    <span class="text-sm font-black text-[#8b1515]">{{ $wawancara->penilaian->kategori_3 }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#7a1111] to-[#8b1515] rounded-xl shadow-sm mb-4 text-white">
                                <p class="text-xs font-bold uppercase tracking-wider">Total Nilai</p>
                                <p class="text-2xl font-black">{{ $wawancara->penilaian->total_nilai }}</p>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <p class="text-sm font-bold text-gray-800">{{ $wawancara->penguji->nama ?? '-' }}</p>
                                </div>
                            </div>
                            @else
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-5">Wawancara</h4>
                            
                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara->tanggal->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Waktu</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara->session_label }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara->penguji->nama ?? '-' }}</p>
                                </div>
                                
                                @if($wawancara->link_meeting)
                                <div class="pt-3">
                                    <a href="{{ $wawancara->link_meeting }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gray-50 hover:bg-[#8b1515] text-[#8b1515] hover:text-white border border-gray-200 hover:border-[#8b1515] text-[0.75rem] font-bold rounded-xl transition-all shadow-sm">
                                        Link Zoom
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($micro)
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm relative overflow-hidden group hover:border-gray-300 hover:shadow-md transition-all">
                            <div class="absolute top-0 left-0 w-full h-1 bg-[#8b1515]"></div>
                            
                            @if($micro->penilaian)
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-4">Micro Teaching</h4>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100/50">
                                    <span class="text-xs font-semibold text-gray-700">Penguasaan Materi</span>
                                    <span class="text-sm font-black text-[#8b1515]">{{ $micro->penilaian->kategori_1 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100/50">
                                    <span class="text-xs font-semibold text-gray-700">Keterampilan Pedagogik</span>
                                    <span class="text-sm font-black text-[#8b1515]">{{ $micro->penilaian->kategori_2 }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100/50">
                                    <span class="text-xs font-semibold text-gray-700">Pemanfaatan Media</span>
                                    <span class="text-sm font-black text-[#8b1515]">{{ $micro->penilaian->kategori_3 }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#7a1111] to-[#8b1515] rounded-xl shadow-sm mb-4 text-white">
                                <p class="text-xs font-bold uppercase tracking-wider">Total Nilai</p>
                                <p class="text-2xl font-black">{{ $micro->penilaian->total_nilai }}</p>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <p class="text-sm font-bold text-gray-800">{{ $micro->penguji->nama ?? '-' }}</p>
                                </div>
                            </div>
                            @else
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-5">Micro Teaching</h4>
                            
                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="font-bold text-gray-800">{{ $micro->tanggal->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Waktu</p>
                                    <p class="font-bold text-gray-800">{{ $micro->session_label }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <p class="font-bold text-gray-800">{{ $micro->penguji->nama ?? '-' }}</p>
                                </div>
                                
                                @if($micro->link_meeting)
                                <div class="pt-3">
                                    <a href="{{ $micro->link_meeting }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gray-50 hover:bg-[#8b1515] text-[#8b1515] hover:text-white border border-gray-200 hover:border-[#8b1515] text-[0.75rem] font-bold rounded-xl transition-all shadow-sm">
                                        Link Zoom
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                @else
                    <div class="p-8 rounded-2xl border border-gray-200 bg-gray-50 text-center">
                        <p class="text-sm font-bold text-gray-600">Belum Ada Jadwal</p>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Jadwal seleksi belum ditentukan. Silakan buat jadwal seleksi di menu Jadwal Seleksi jika diperlukan.</p>
                    </div>
                @endif
            </div>

            {{-- 4. UBAH STATUS LAMARAN --}}
            @php
                $isFinished = in_array($lamaran->status, ['diterima', 'ditolak']);
                $hasJadwal = collect([$wawancara, $micro])->filter()->isNotEmpty();
                $hasBothScores = ($wawancara && $wawancara->penilaian) && ($micro && $micro->penilaian);
                $statusOrder = ['menunggu' => 1, 'seleksi_tahap1' => 2, 'seleksi_tahap2' => 3, 'diterima' => 4, 'ditolak' => 4];
                $currentOrder = $statusOrder[$lamaran->status] ?? 1;
                
                $statusColors = [
                    'menunggu'       => 'bg-gray-100 text-gray-600 border-gray-200',
                    'seleksi_tahap1' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'seleksi_tahap2' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'diterima'       => 'bg-green-100 text-green-700 border-green-200',
                    'ditolak'        => 'bg-red-50 text-red-700 border-red-200',
                ];
            @endphp
            
            <div x-data="{ editing: false }">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                        Status Lamaran
                    </h3>
                    @if(!$isFinished)
                    <button @click="editing = !editing" class="text-gray-400 hover:text-[#8b1515] transition-all p-1.5 hover:bg-red-50 rounded-lg" :class="editing ? 'text-[#8b1515] bg-red-50' : ''" title="Ubah Status">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                    @endif
                </div>

                <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100">
                    {{-- READ ONLY VIEW --}}
                    <div x-show="!editing" class="flex flex-col items-center">
                        <span class="inline-flex px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest border-2 shadow-sm {{ $statusColors[$lamaran->status] ?? $statusColors['menunggu'] }}">
                            {{ $lamaran->status_label }}
                        </span>
                        @if($isFinished)
                            <p class="text-[0.65rem] text-gray-400 mt-4 font-bold uppercase tracking-wider">Alur seleksi telah selesai</p>
                        @endif
                    </div>

                    {{-- EDITABLE VIEW --}}
                    @if(!$isFinished)
                    <form x-show="editing" method="POST" action="{{ route('admin.lamaran.update', $lamaran) }}" class="space-y-6" x-cloak>
                        @csrf
                        @method('PUT')

                        <div>
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach(['menunggu' => 'Menunggu', 'seleksi_tahap1' => 'Seleksi Tahap 1', 'seleksi_tahap2' => 'Seleksi Tahap 2', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                                @php
                                    $isDisabled = false;
                                    $targetOrder = $statusOrder[$val];
                                    if ($targetOrder < $currentOrder) $isDisabled = true;
                                    if ($val === 'seleksi_tahap2' && !$hasJadwal && $currentOrder < 3) $isDisabled = true;
                                    if (($val === 'diterima' || $val === 'ditolak') && !$hasBothScores) $isDisabled = true;
                                @endphp
                                <label class="relative {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $isDisabled ? 'title="Syarat belum terpenuhi atau tidak bisa kembali"' : '' }}>
                                    <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                                           {{ $lamaran->status === $val ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                    <span class="cursor-pointer inline-flex items-center px-4 py-2 rounded-xl text-[0.65rem] font-black uppercase tracking-wider border-2 transition-all
                                        peer-checked:border-[#8b1515] peer-checked:bg-[#8b1515] peer-checked:text-white
                                        border-gray-200 text-gray-500 hover:border-gray-300 bg-white select-none shadow-sm">
                                        {{ $label }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col items-center gap-4 pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-12 py-3 bg-[#8b1515] hover:bg-red-900 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-red-900/20 transition-all">
                                Simpan Perubahan
                            </button>
                            <button type="button" @click="editing = false" class="text-[0.65rem] font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">Batal</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>



            {{-- 5. DETAIL PELAMAR LAINNYA (RIWAYAT PENDIDIKAN, DOKUMEN, DLL) --}}
            <div x-data="{ expanded: false }" class="pt-6 border-t border-gray-100">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                    <span class="text-sm font-bold text-gray-800">Lihat Detail Profil Pelamar Lainnya</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="expanded" x-collapse class="mt-6 space-y-8">
                    
                    {{-- RIWAYAT PENDIDIKAN --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Riwayat Pendidikan
                        </h3>
                        <div class="space-y-8">
                            @if($pelamar->jenjang)
                            <div class="pl-4 border-l-[3px] border-[#8b1515]/40 py-1">
                                <div class="grid grid-cols-2 md:grid-cols-6 gap-x-6 gap-y-4">
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang }}</p></div>
                                    <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?: '-' }}</p></div>
                                    
                                    <div>
                                        <p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah {{ $pelamar->jenjang }}</p>
                                        @if($pelamar->file_ijazah)
                                            <a href="{{ asset('storage/' . $pelamar->file_ijazah) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">-</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip {{ $pelamar->jenjang }}</p>
                                        @if($pelamar->file_transkrip)
                                            <a href="{{ asset('storage/' . $pelamar->file_transkrip) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($pelamar->jenjang_2)
                            <div class="pl-4 border-l-[3px] border-gray-200 py-1">
                                <div class="grid grid-cols-2 md:grid-cols-6 gap-x-6 gap-y-4">
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 }}</p></div>
                                    <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?: '-' }}</p></div>
                                    
                                    <div>
                                        <p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah {{ $pelamar->jenjang_2 }}</p>
                                        @if($pelamar->file_ijazah_2)
                                            <a href="{{ asset('storage/' . $pelamar->file_ijazah_2) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">-</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip {{ $pelamar->jenjang_2 }}</p>
                                        @if($pelamar->file_transkrip_2)
                                            <a href="{{ asset('storage/' . $pelamar->file_transkrip_2) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($pelamar->jenjang_3)
                            <div class="pl-4 border-l-[3px] border-gray-200 py-1">
                                <div class="grid grid-cols-2 md:grid-cols-6 gap-x-6 gap-y-4">
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 }}</p></div>
                                    <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?: '-' }}</p></div>
                                    
                                    <div>
                                        <p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah {{ $pelamar->jenjang_3 }}</p>
                                        @if($pelamar->file_ijazah_3)
                                            <a href="{{ asset('storage/' . $pelamar->file_ijazah_3) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">-</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip {{ $pelamar->jenjang_3 }}</p>
                                        @if($pelamar->file_transkrip_3)
                                            <a href="{{ asset('storage/' . $pelamar->file_transkrip_3) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(!$pelamar->jenjang)
                                <p class="text-sm text-gray-400 italic">-</p>
                            @endif
                        </div>
                    </div>

                    {{-- DOKUMEN PENDUKUNG --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Dokumen Pendukung
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4 mb-8">
                            <div>
                                <p class="text-[0.6rem] font-black text-gray-400 uppercase">CV (Resume)</p>
                                @if($pelamar->file_cv)
                                    <a href="{{ asset('storage/' . $pelamar->file_cv) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.6rem] font-black text-gray-400 uppercase">Pas Foto</p>
                                @if($pelamar->file_pas_foto)
                                    <a href="{{ asset('storage/' . $pelamar->file_pas_foto) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.6rem] font-black text-gray-400 uppercase">KTP</p>
                                @if($pelamar->file_ktp)
                                    <a href="{{ asset('storage/' . $pelamar->file_ktp) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.6rem] font-black text-gray-400 uppercase">{{ $pelamar->kategori_sertifikat ?: 'Sertifikat' }}</p>
                                @if($pelamar->file_sertifikat)
                                    <a href="{{ asset('storage/' . $pelamar->file_sertifikat) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">-</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SERTIFIKAT BAHASA INGGRIS --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Sertifikat Bahasa Inggris
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                            <div>
                                <p class="text-[0.6rem] font-black text-gray-400 uppercase">Sertifikat Bahasa</p>
                                @if($pelamar->file_sertifikat_bahasa)
                                    <a href="{{ asset('storage/' . $pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">-</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- DATA AKADEMIK (DOSEN) --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Data Akademik (Dosen)
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIDN</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nidn ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Homebase</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->homebase ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jabatan Akademik</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik)) : '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">H-Index</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?: '-' }}</p></div>
                        </div>
                        <div class="mt-3"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Minat Riset & Keahlian</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?: '-' }}</p></div>
                    </div>

                    {{-- DOKUMEN PELAMAR BER-HOMEBASE --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Dokumen Pelamar Ber-Homebase
                        </h3>
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
                            $hasHomebaseDocs = collect($homebaseDocs)->contains(fn($d) => $d['file']);
                        @endphp
                        @if($hasHomebaseDocs)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($homebaseDocs as $doc)
                                @if($doc['file'])
                                <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-colors group">
                                    <span class="text-xs font-bold text-gray-600 group-hover:text-blue-700 truncate">{{ $doc['label'] }}</span>
                                </a>
                                @endif
                            @endforeach
                        </div>
                        @else
                            <p class="text-sm text-gray-400 italic">-</p>
                        @endif
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
