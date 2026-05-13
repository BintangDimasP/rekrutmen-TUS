@extends('layouts.admin')

@section('title', 'Detail Lamaran — ' . $lamaran->pelamar->nama)

@section('content')
@php
    $pelamar = $lamaran->pelamar;
@endphp



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
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative">
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
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Kewarganegaraan</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->kewarganegaraan ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Status Pernikahan</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->status_pernikahan ?: '-' }}</p></div>
                    <div class="col-span-2 md:col-span-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Domisili</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_domisili ?: '-' }}</p></div>
                    <div class="col-span-2 md:col-span-4"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Sesuai KTP</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_ktp ?: '-' }}</p></div>
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


                </div>
            </div>

                        {{-- 3. JADWAL & SUMMARY PENILAIAN SELEKSI --}}
            @php
                $microDinilai     = $micro->filter(fn($j) => $j->penilaian !== null);
                $wawancaraDinilai = $wawancara->filter(fn($j) => $j->penilaian !== null);

                $microKategoriLabels = [
                    1 => 'Perencanaan Pembelajaran',
                    2 => 'Penguasaan Materi',
                    3 => 'Sistematika',
                    4 => 'Pengelolaan Kelas & Interaksi',
                    5 => 'Sikap & Etika',
                ];
                // Wawancara: 8 indikator flat
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
                    ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2)
                    : null;
                $nilaiAkhirWawancara = $wawancaraDinilai->count() > 0
                    ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2)
                    : null;
            @endphp

            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Jadwal &amp; Penilaian Seleksi
                </h3>

                @if(($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0))
                <div class="space-y-8">

                    {{-- ── MICRO TEACHING ── --}}
                    @if($micro && $micro->count() > 0)
                    <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-white font-black text-sm uppercase tracking-widest">Micro Teaching</span>
                            </div>
                            <div class="flex items-center gap-3 text-red-200 text-xs flex-wrap">
                                <span>{{ $micro[0]->tanggal->format('d M Y') }}</span>
                                <span>&bull;</span>
                                <span>{{ $micro[0]->session_label }}</span>
                                @if($micro[0]->link_meeting)
                                <a href="{{ $micro[0]->link_meeting }}" target="_blank" class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg transition-colors border border-white/20">Link Zoom</a>
                                @endif
                            </div>
                        </div>

                        @if($microDinilai->count() === 0)
                        <div class="p-6 bg-white">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">Menunggu Penilaian</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Penguji: {{ $micro->pluck('penguji.nama')->filter()->implode(', ') }} &bull; 0/{{ $micro->count() }} sudah menilai</p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="divide-y divide-gray-100 bg-white">
                            @foreach($microDinilai->values() as $idx => $jadwalMicro)
                            @php $p = $jadwalMicro->penilaian; @endphp
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-[#8b1515]/10 text-[#8b1515] flex items-center justify-center text-xs font-black flex-shrink-0">{{ $idx + 1 }}</div>
                                        <span class="text-sm font-black text-gray-800">{{ $jadwalMicro->penguji->nama ?? '-' }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penguji {{ $idx + 1 }}</span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-4">
                                    @foreach($microKategoriLabels as $kNum => $kLabel)
                                    @php $kVal = $p->{'kategori_'.$kNum}; @endphp
                                    @if($kVal !== null)
                                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-center">
                                        <p class="text-[0.6rem] font-bold text-gray-400 uppercase leading-tight mb-1">{{ $kLabel }}</p>
                                        <p class="text-lg font-black text-[#8b1515]">{{ $kVal }}</p>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>

                                <div class="flex flex-wrap items-start gap-3">
                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-gradient-to-r from-[#7a1111] to-[#8b1515] rounded-xl text-white">
                                        <span class="text-xs font-bold uppercase tracking-wider">Rata-rata</span>
                                        <span class="text-xl font-black">{{ $p->total_nilai }}</span>
                                    </div>
                                    @if($p->rekomendasi)
                                    @php
                                        $rekLabels = ['direkomendasikan' => ['label' => 'Direkomendasikan', 'color' => 'bg-green-50 text-green-700 border-green-200'], 'tidak_direkomendasikan' => ['label' => 'Tidak Direkomendasikan', 'color' => 'bg-red-50 text-red-700 border-red-200'], 'perlu_dipertimbangkan' => ['label' => 'Perlu Dipertimbangkan', 'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200']];
                                        $rek = $rekLabels[$p->rekomendasi] ?? ['label' => $p->rekomendasi, 'color' => 'bg-gray-50 text-gray-700 border-gray-200'];
                                    @endphp
                                    <span class="px-3 py-2 rounded-xl text-xs font-bold border {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                    @endif
                                    @if($p->prodi_tujuan)
                                    <div class="text-xs py-2"><span class="text-gray-400 font-bold uppercase">Prodi Tujuan:</span> <span class="font-bold text-gray-700">{{ $p->prodi_tujuan }}</span></div>
                                    @endif
                                    @if($p->kelompok_keahlian)
                                    @php $kkLabels = ['scout' => 'SCoT', 'ethes' => 'ETHES', 'riib' => 'RIIB']; @endphp
                                    <div class="text-xs py-2"><span class="text-gray-400 font-bold uppercase">Kelompok:</span> <span class="font-bold text-gray-700">{{ $kkLabels[$p->kelompok_keahlian] ?? $p->kelompok_keahlian }}</span></div>
                                    @endif
                                    @if($p->bidang_keahlian)
                                    <div class="text-xs py-2"><span class="text-gray-400 font-bold uppercase">Bidang Keahlian:</span> <span class="font-bold text-gray-700">{{ $p->bidang_keahlian }}</span></div>
                                    @endif
                                </div>
                                @if($p->catatan)
                                <div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-[0.65rem] font-bold text-gray-400 uppercase mb-1">Catatan</p>
                                    <p class="text-sm text-gray-700">{{ $p->catatan }}</p>
                                </div>
                                @endif
                            </div>
                            @endforeach

                            @foreach($micro->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                            <div class="px-6 py-4 flex items-center gap-3 bg-yellow-50/50">
                                <svg class="w-4 h-4 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-semibold text-gray-600">{{ $jadwalBelum->penguji->nama ?? '-' }}</span>
                                <span class="text-xs text-yellow-600 font-bold">Belum menilai</span>
                            </div>
                            @endforeach

                            <div class="px-6 py-5 bg-gradient-to-r from-[#7a1111] to-[#8b1515] flex items-center justify-between">
                                <div>
                                    <p class="text-red-200 text-xs font-bold uppercase tracking-widest">Nilai Akhir Micro Teaching</p>
                                    <p class="text-red-200 text-[0.65rem] mt-0.5">Rata-rata dari {{ $microDinilai->count() }} penguji</p>
                                </div>
                                <span class="text-4xl font-black text-white">{{ $nilaiAkhirMicro }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- ── WAWANCARA ── --}}
                    @if($wawancara && $wawancara->count() > 0)
                    <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                                <span class="text-white font-black text-sm uppercase tracking-widest">Wawancara</span>
                            </div>
                            <div class="flex items-center gap-3 text-red-200 text-xs flex-wrap">
                                <span>{{ $wawancara[0]->tanggal->format('d M Y') }}</span>
                                <span>&bull;</span>
                                <span>{{ $wawancara[0]->session_label }}</span>
                                @if($wawancara[0]->link_meeting)
                                <a href="{{ $wawancara[0]->link_meeting }}" target="_blank" class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg transition-colors border border-white/20">Link Zoom</a>
                                @endif
                            </div>
                        </div>

                        @if($wawancaraDinilai->count() === 0)
                        <div class="p-6 bg-white">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">Menunggu Penilaian</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Penguji: {{ $wawancara->pluck('penguji.nama')->filter()->implode(', ') }} &bull; 0/{{ $wawancara->count() }} sudah menilai</p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="divide-y divide-gray-100 bg-white">
                            @foreach($wawancaraDinilai->values() as $idx => $jadwalWaw)
                            @php $p = $jadwalWaw->penilaian; @endphp
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-[#8b1515]/10 text-[#8b1515] flex items-center justify-center text-xs font-black flex-shrink-0">{{ $idx + 1 }}</div>
                                        <span class="text-sm font-black text-gray-800">{{ $jadwalWaw->penguji->nama ?? '-' }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penguji {{ $idx + 1 }}</span>
                                </div>

                                <div class="grid grid-cols-4 gap-2 mb-4">
                                        @php $detail = $p->detail_nilai ?? []; @endphp
                                        @foreach($wawancaraIndikatorLabels as $iNum => $iLabel)
                                        @php $iVal = $detail['k1_item_'.$iNum] ?? null; @endphp
                                        @if($iVal !== null)
                                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 text-center">
                                            <p class="text-[0.6rem] font-bold text-gray-400 uppercase leading-tight mb-1">{{ $iLabel }}</p>
                                            <p class="text-lg font-black text-[#8b1515]">{{ $iVal }}</p>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>

                                <div class="flex items-center gap-3 px-4 py-2.5 bg-gradient-to-r from-[#7a1111] to-[#8b1515] rounded-xl text-white w-fit">
                                    <span class="text-xs font-bold uppercase tracking-wider">Rata-rata</span>
                                    <span class="text-xl font-black">{{ $p->total_nilai }}</span>
                                </div>
                                                                @if($p->rekomendasi)
                                @php
                                    $rekLabels = ['direkomendasikan' => ['label' => 'Direkomendasikan', 'color' => 'bg-green-50 text-green-700 border-green-200'], 'tidak_direkomendasikan' => ['label' => 'Tidak Direkomendasikan', 'color' => 'bg-red-50 text-red-700 border-red-200'], 'perlu_dipertimbangkan' => ['label' => 'Perlu Dipertimbangkan', 'color' => 'bg-yellow-50 text-yellow-700 border-yellow-200']];
                                    $rek = $rekLabels[$p->rekomendasi] ?? ['label' => $p->rekomendasi, 'color' => 'bg-gray-50 text-gray-700 border-gray-200'];
                                @endphp
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold border {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                    @if($p->prodi_tujuan)
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold border bg-gray-50 text-gray-700 border-gray-200">Prodi: {{ $p->prodi_tujuan }}</span>
                                    @endif
                                </div>
                                @endif
                                @if($p->catatan)
                                <div class="mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-[0.65rem] font-bold text-gray-400 uppercase mb-1">Catatan</p>
                                    <p class="text-sm text-gray-700">{{ $p->catatan }}</p>
                                </div>
                                @endif
                            </div>
                            @endforeach

                            @foreach($wawancara->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                            <div class="px-6 py-4 flex items-center gap-3 bg-yellow-50/50">
                                <svg class="w-4 h-4 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-semibold text-gray-600">{{ $jadwalBelum->penguji->nama ?? '-' }}</span>
                                <span class="text-xs text-yellow-600 font-bold">Belum menilai</span>
                            </div>
                            @endforeach

                            <div class="px-6 py-5 bg-gradient-to-r from-[#7a1111] to-[#8b1515] flex items-center justify-between">
                                <div>
                                    <p class="text-red-200 text-xs font-bold uppercase tracking-widest">Nilai Akhir Wawancara</p>
                                    <p class="text-red-200 text-[0.65rem] mt-0.5">Rata-rata dari {{ $wawancaraDinilai->count() }} penguji</p>
                                </div>
                                <span class="text-4xl font-black text-white">{{ $nilaiAkhirWawancara }}</span>
                            </div>
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
                $hasJadwal = ($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0);
                $hasBothScores = ($wawancara && $wawancara->count() > 0 && $wawancara->every(fn($j) => $j->penilaian !== null)) && ($micro && $micro->count() > 0 && $micro->every(fn($j) => $j->penilaian !== null));
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
                                @foreach(['menunggu' => 'Menunggu', 'seleksi_tahap1' => 'Seleksi Tahap 1 (Administrasi)', 'seleksi_tahap2' => 'Seleksi Tahap 2 (Micro Teaching & Wawancara)', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
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
                                <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-4">
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang }}</p></div>
                                    <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah ?: '-' }}</p></div>
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
                                <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-4">
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 }}</p></div>
                                    <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_2 ?: '-' }}</p></div>
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
                                <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-4">
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 }}</p></div>
                                    <div class="col-span-2 md:col-span-1"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_3 ?: '-' }}</p></div>
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
