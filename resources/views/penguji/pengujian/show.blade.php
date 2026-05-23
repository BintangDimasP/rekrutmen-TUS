@extends('layouts.admin')

@section('title', 'Detail Pelamar')

@section('content')
@php
    $pelamarLive = $jadwal->pelamar;
    $lamaran = \App\Models\Lamaran::where('pelamar_id', $pelamarLive->id)
        ->where('lowongan_id', $jadwal->lowongan_id)
        ->first();
    $pelamar = $lamaran ? $lamaran->effective_pelamar : $pelamarLive;
    $lowongan = $jadwal->lowongan;
    $penilaian = $jadwal->penilaian;
    $isWawancara = $jadwal->tipe_seleksi == 'wawancara';
    $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
    $today = \Carbon\Carbon::today();
    $canTest = $today->greaterThanOrEqualTo(\Carbon\Carbon::parse($jadwal->tanggal));

    // Get all schedules for this pelamar to check Micro Teaching evaluation status
    $jadwals_all = \App\Models\JadwalSeleksi::where('pelamar_id', $pelamar->id)->with('penilaian')->get();
    $microAll    = $jadwals_all->where('tipe_seleksi', 'micro_teaching');
    $isMicro     = $jadwal->tipe_seleksi == 'micro_teaching';

    // Wawancara boleh dilakukan jika SEMUA penguji micro sudah menilai
    $microSelesai = $microAll->isNotEmpty() && $microAll->every(fn($j) => $j->penilaian !== null);
    $microProgress = $microAll->count() > 0
        ? $microAll->filter(fn($j) => $j->penilaian !== null)->count() . '/' . $microAll->count()
        : '0/0';

    $canEvaluate = $isMicro || $microSelesai;
@endphp

<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('penguji.pengujian.index') }}" class="hover:text-[#8b1515] transition-colors">Pengujian</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $pelamar->nama }}</span>
    </div>

    <!-- Single Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm ring-2 ring-white/30">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($pelamar->nama, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">{{ $pelamar->nama }}</h1>
                        <p class="text-red-200 text-sm mt-0.5">{{ $pelamarLive->user?->email }}</p>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            @if($isWawancara)
                                <span class="inline-flex px-2 py-0.5 bg-white/20 text-white text-xs font-bold rounded">Wawancara</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 bg-white/20 text-white text-xs font-bold rounded">Micro Teaching</span>
                            @endif
                            <span class="text-red-200 text-xs">{{ $jadwal->tanggal->format('d M Y') }} · Sesi {{ $jadwal->sesi }} ({{ $sesiInfo['start'] ?? '-' }} - {{ $sesiInfo['end'] ?? '-' }})</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($jadwal->link_meeting)
                        @if($penilaian)
                            {{-- Zoom disabled after testing --}}
                            <button disabled class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 cursor-not-allowed rounded-xl text-sm font-bold text-white/50 whitespace-nowrap backdrop-blur-sm opacity-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Buka Zoom
                            </button>
                        @else
                            <a href="{{ $jadwal->link_meeting }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-sm font-bold text-white transition-colors whitespace-nowrap backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Buka Zoom
                            </a>
                        @endif
                    @endif
                    @if($penilaian)
                        {{-- Sudah dinilai: tombol disabled --}}
                        <div class="flex flex-col items-end">
                            <button disabled class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/50 cursor-not-allowed rounded-xl text-sm font-bold text-green-700/70 whitespace-nowrap shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Sudah Dinilai
                            </button>
                            
                        </div>
                    @elseif($canEvaluate)
                        <a href="{{ route('penguji.pengujian.uji', $jadwal->id) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-100 rounded-xl text-sm font-bold text-[#8b1515] transition-colors whitespace-nowrap shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Mulai Uji
                        </a>
                    @else
                        <div class="flex flex-col items-end">
                            <button disabled class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/50 cursor-not-allowed rounded-xl text-sm font-bold text-[#8b1515]/50 whitespace-nowrap shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Mulai Uji
                            </button>
                            <span class="text-[0.65rem] text-red-200 mt-1.5 font-medium">Selesaikan Micro Teaching dahulu ({{ $microProgress }} penguji menilai)</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- CONTENT: Full Profile -->
        <div class="p-6 md:p-8 space-y-8">

            {{-- 1. DATA DIRI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Data Diri</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Nama Lengkap</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->nama ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIK (KTP)</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nik ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Telepon / WA</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_telepon ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Kelamin</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tempat Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Kewarganegaraan</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->kewarganegaraan ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Status Pernikahan</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->status_pernikahan ?: '-' }}</p></div>
                    <div class="col-span-2 md:col-span-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Domisili</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_domisili ?: '-' }}</p></div>
                    <div class="col-span-2 md:col-span-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Sesuai KTP</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_ktp ?: '-' }}</p></div>
                </div>
            </div>

            {{-- 2. RIWAYAT PENDIDIKAN --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Riwayat Pendidikan</h3>
                <div class="space-y-8">
                    @if($pelamar->jenjang)
                    <div class="pl-4 border-l-[3px] border-[#8b1515]/40 py-1">
                        <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-4">
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</p>@if($pelamar->file_ijazah)<a href="{{ asset('storage/' . $pelamar->file_ijazah) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</p>@if($pelamar->file_transkrip)<a href="{{ asset('storage/' . $pelamar->file_transkrip) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                        </div>
                    </div>
                    @endif
                    @if($pelamar->jenjang_2)
                    <div class="pl-4 border-l-[3px] border-gray-200 py-1">
                        <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-4">
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_2 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_2 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</p>@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/' . $pelamar->file_ijazah_2) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</p>@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/' . $pelamar->file_transkrip_2) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                        </div>
                    </div>
                    @endif
                    @if($pelamar->jenjang_3)
                    <div class="pl-4 border-l-[3px] border-gray-200 py-1">
                        <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-4">
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_3 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_3 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?: '-' }}</p></div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Ijazah</p>@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/' . $pelamar->file_ijazah_3) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                            <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Transkrip</p>@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/' . $pelamar->file_transkrip_3) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                        </div>
                    </div>
                    @endif
                    @if(!$pelamar->jenjang)
                        <p class="text-sm text-gray-400 italic">-</p>
                    @endif
                </div>
            </div>

            {{-- 3. DOKUMEN PENDUKUNG --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Dokumen Pendukung</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">CV (Resume)</p>@if($pelamar->file_cv)<a href="{{ asset('storage/' . $pelamar->file_cv) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Pas Foto</p>@if($pelamar->file_pas_foto)<a href="{{ asset('storage/' . $pelamar->file_pas_foto) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">KTP</p>@if($pelamar->file_ktp)<a href="{{ asset('storage/' . $pelamar->file_ktp) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">{{ $pelamar->kategori_sertifikat ?: 'Sertifikat' }}</p>@if($pelamar->file_sertifikat)<a href="{{ asset('storage/' . $pelamar->file_sertifikat) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                </div>
            </div>

            {{-- 4. SERTIFIKAT BAHASA INGGRIS --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Sertifikat Bahasa Inggris</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Sertifikat Bahasa</p>@if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/' . $pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                </div>
            </div>

            {{-- 5. DATA AKADEMIK (DOSEN) --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Data Akademik (Dosen)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIDN</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nidn ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Homebase</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->homebase ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jabatan Akademik</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik)) : '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">H-Index</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?: '-' }}</p></div>
                </div>
                <div class="mt-3"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Minat Riset & Keahlian</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?: '-' }}</p></div>
            </div>

            {{-- 6. DOKUMEN PELAMAR BER-HOMEBASE --}}
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
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Dokumen Pelamar Ber-Homebase</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($homebaseDocs as $doc)
                        @if($doc['file'])
                        <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-colors group">
                            <span class="text-xs font-bold text-gray-600 group-hover:text-blue-700 truncate">{{ $doc['label'] }}</span>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Hasil Penilaian Seleksi
                </h3>
                @php
                    $wawancaraAll = $jadwals_all->where('tipe_seleksi', 'wawancara');
                    $microAll     = $jadwals_all->where('tipe_seleksi', 'micro_teaching');

                    $microDinilai     = $microAll->filter(fn($j) => $j->penilaian !== null);
                    $wawancaraDinilai = $wawancaraAll->filter(fn($j) => $j->penilaian !== null);

                    $microKategoriLabels = [
                        1 => 'Perencanaan Pembelajaran',
                        2 => 'Penguasaan Materi',
                        3 => 'Sistematika',
                        4 => 'Pengelolaan Kelas & Interaksi',
                        5 => 'Sikap & Etika',
                    ];
                    // Wawancara: 8 indikator flat (k1_item_1 s/d k1_item_8)
                    $wawancaraIndikatorLabels = [
                        1 => 'Motivasi',
                        2 => 'Kemampuan Mengajar',
                        3 => 'Kemampuan Mengembangkan Kurikulum',
                        4 => 'Kemampuan Penelitian & Publikasi',
                        5 => 'Kemampuan Abdimas',
                        6 => 'Kemampuan Bekerjasama dengan Tim',
                        7 => 'Keahlian Lainnya',
                        8 => 'Komitmen Waktu',
                    ];
                    $wawancaraKategoriLabels = []; // tidak dipakai untuk wawancara

                    $nilaiAkhirMicro = $microDinilai->count() > 0
                        ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;
                    $nilaiAkhirWawancara = $wawancaraDinilai->count() > 0
                        ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;
                @endphp

                <div class="space-y-6">
                    @foreach([
                        ['label' => 'Micro Teaching', 'jadwals' => $microAll,     'dinilai' => $microDinilai,     'kategoriLabels' => $microKategoriLabels,     'nilaiAkhir' => $nilaiAkhirMicro,     'isMicro' => true],
                        ['label' => 'Wawancara',      'jadwals' => $wawancaraAll, 'dinilai' => $wawancaraDinilai, 'kategoriLabels' => $wawancaraKategoriLabels, 'nilaiAkhir' => $nilaiAkhirWawancara, 'isMicro' => false, 'isWawancara' => true, 'indikatorLabels' => $wawancaraIndikatorLabels],
                    ] as $section)
                    @if($section['jadwals']->count() > 0)
                    <div class="rounded-xl border border-gray-100 overflow-hidden">
                        <div class="bg-[#8b1515]/5 border-b border-gray-100 px-5 py-3 flex items-center justify-between">
                            <span class="text-xs font-black text-[#8b1515] uppercase tracking-widest">{{ $section['label'] }}</span>
                            <span class="text-xs text-gray-500 font-medium">{{ $section['dinilai']->count() }}/{{ $section['jadwals']->count() }} penguji menilai</span>
                        </div>

                        @if($section['dinilai']->count() === 0)
                        <div class="p-5">
                            <p class="text-sm text-gray-400 italic">Belum ada penilaian.</p>
                        </div>
                        @else
                        <div class="divide-y divide-gray-100">
                            @foreach($section['dinilai']->values() as $idx => $jadwalItem)
                            @php $p = $jadwalItem->penilaian; @endphp
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-6 h-6 rounded-full bg-[#8b1515]/10 text-[#8b1515] flex items-center justify-center text-xs font-black flex-shrink-0">{{ $idx + 1 }}</div>
                                    <span class="text-sm font-black text-gray-800">{{ $jadwalItem->penguji->nama ?? '-' }}</span>
                                </div>
                                <div class="space-y-1.5 mb-3">
                                    @if(!empty($section['isWawancara']))
                                    {{-- Wawancara: grid 4 kolom --}}
                                    @php $detail = $p->detail_nilai ?? []; @endphp
                                    <div class="grid grid-cols-4 gap-2 mb-3">
                                        @foreach($section['indikatorLabels'] as $iNum => $iLabel)
                                        @php $iVal = $detail['k1_item_'.$iNum] ?? null; @endphp
                                        @if($iVal !== null)
                                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-center">
                                            <p class="text-[0.55rem] font-bold text-gray-400 uppercase leading-tight mb-1">{{ $iLabel }}</p>
                                            <p class="text-base font-black text-[#8b1515]">{{ $iVal }}</p>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    @else
                                    {{-- Micro Teaching: grid 5 kolom --}}
                                    <div class="grid grid-cols-5 gap-2 mb-3">
                                        @foreach($section['kategoriLabels'] as $kNum => $kLabel)
                                        @php $kVal = $p->{'kategori_'.$kNum}; @endphp
                                        @if($kVal !== null)
                                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-center">
                                            <p class="text-[0.55rem] font-bold text-gray-400 uppercase leading-tight mb-1">{{ $kLabel }}</p>
                                            <p class="text-base font-black text-[#8b1515]">{{ $kVal }}</p>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                    <span class="text-xs font-black text-gray-600 uppercase tracking-wider">Rata-rata</span>
                                    <span class="text-lg font-black text-[#8b1515]">{{ $p->total_nilai }}</span>
                                </div>
                                @php
                                    $rekLabels = [
                                        'direkomendasikan'       => ['label' => 'Direkomendasikan',           'color' => 'bg-green-50 text-green-700 border-green-200'],
                                        'tidak_direkomendasikan' => ['label' => 'Tidak Direkomendasikan',     'color' => 'bg-red-50 text-red-700 border-red-200'],
                                        'perlu_dipertimbangkan'  => ['label' => 'Perlu Dipertimbangkan',      'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200'],
                                    ];
                                    $kkLabels = ['scout' => 'SCoT', 'ethes' => 'ETHES', 'riib' => 'RIIB'];
                                @endphp
                                @if($p->rekomendasi || $p->prodi_tujuan || $p->kelompok_keahlian || $p->bidang_keahlian || $p->catatan)
                                <div class="mt-3 space-y-2">
                                    {{-- Rekomendasi + Prodi Tujuan --}}
                                    @if($p->rekomendasi || $p->prodi_tujuan)
                                    <div class="flex flex-wrap gap-2">
                                        @if($p->rekomendasi)
                                        @php $rek = $rekLabels[$p->rekomendasi] ?? ['label' => $p->rekomendasi, 'color' => 'bg-gray-50 text-gray-700 border-gray-200']; @endphp
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                        @endif
                                        @if($p->prodi_tujuan)
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold border bg-blue-50 text-blue-700 border-blue-200">Prodi: {{ $p->prodi_tujuan }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    {{-- Kelompok Keahlian + Bidang Keahlian (micro only) --}}
                                    @if($p->kelompok_keahlian || $p->bidang_keahlian)
                                    <div class="flex flex-wrap gap-2">
                                        @if($p->kelompok_keahlian)
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold border bg-purple-50 text-purple-700 border-purple-200">{{ $kkLabels[$p->kelompok_keahlian] ?? $p->kelompok_keahlian }}</span>
                                        @endif
                                        @if($p->bidang_keahlian)
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold border bg-gray-50 text-gray-600 border-gray-200">{{ $p->bidang_keahlian }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    {{-- Catatan --}}
                                    @if($p->catatan)
                                    <p class="text-xs text-gray-500 italic border-t border-gray-100 pt-2">{{ $p->catatan }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endforeach

                            <div class="px-5 py-4 bg-gradient-to-r from-[#7a1111] to-[#8b1515] flex items-center justify-between">
                                <div>
                                    <p class="text-red-200 text-[0.65rem] font-bold uppercase tracking-widest">Nilai Akhir {{ $section['label'] }}</p>
                                    <p class="text-red-200 text-[0.6rem] mt-0.5">Rata-rata dari {{ $section['dinilai']->count() }} penguji</p>
                                </div>
                                <span class="text-3xl font-black text-white">{{ $section['nilaiAkhir'] }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
