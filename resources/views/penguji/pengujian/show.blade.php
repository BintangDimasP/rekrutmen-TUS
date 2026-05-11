@extends('layouts.admin')

@section('title', 'Detail Pelamar')

@section('content')
@php
    $pelamar = $jadwal->pelamar;
    $lowongan = $jadwal->lowongan;
    $penilaian = $jadwal->penilaian;
    $isWawancara = $jadwal->tipe_seleksi == 'tahap1';
    $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
    $today = \Carbon\Carbon::today();
    $canTest = $today->greaterThanOrEqualTo(\Carbon\Carbon::parse($jadwal->tanggal));

    // Get all schedules for this pelamar to check Wawancara evaluation status
    $jadwals_all = \App\Models\JadwalSeleksi::where('pelamar_id', $pelamar->id)->with('penilaian')->get();
    $wawancara = $jadwals_all->where('tipe_seleksi', 'tahap1')->first();
    $wawancaraSudahDinilai = $wawancara && $wawancara->penilaian;
    
    // Can evaluate if it's Wawancara OR (it's Micro and Wawancara is already evaluated)
    $canEvaluate = $isWawancara || $wawancaraSudahDinilai;
@endphp

<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('penguji.pengujian.index') }}" class="hover:text-[#8b1515] transition-colors">Pengujian</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-800">Detail Pelamar</span>
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
                        <p class="text-red-200 text-sm mt-0.5">{{ $pelamar->user?->email }}</p>
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
                        <a href="{{ $jadwal->link_meeting }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl text-sm font-bold text-white transition-colors whitespace-nowrap backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Buka Zoom
                        </a>
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
                            <span class="text-[0.65rem] text-red-200 mt-1.5 font-medium">Selesaikan Wawancara dahulu</span>
                        </div>
                    @endif
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
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Nama Lengkap</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->nama ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIK (KTP)</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nik ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Telepon / WA</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_telepon ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Kelamin</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tempat Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p></div>
                    <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Lengkap</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat ?: '-' }}</p></div>
                </div>
            </div>

            {{-- 2. RIWAYAT PENDIDIKAN --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Riwayat Pendidikan
                </h3>
                <div class="space-y-4">
                    @if($pelamar->jenjang)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-[#8b1515]/40 py-2">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang }}</p></div>
                        <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?: '-' }}</p></div>
                    </div>
                    @endif
                    @if($pelamar->jenjang_2)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 }}</p></div>
                        <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?: '-' }}</p></div>
                    </div>
                    @endif
                    @if($pelamar->jenjang_3)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 }}</p></div>
                        <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?: '-' }}</p></div>
                    </div>
                    @endif
                    @if(!$pelamar->jenjang)
                        <p class="text-sm text-gray-400 italic">-</p>
                    @endif
                </div>
            </div>

            {{-- 3. DOKUMEN & SERTIFIKAT --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Dokumen & Sertifikat
                </h3>
                @php
                    $docs = [
                        ['label' => 'CV (Resume)', 'file' => $pelamar->file_cv],
                        ['label' => 'Pas Foto', 'file' => $pelamar->file_pas_foto],
                        ['label' => 'KTP', 'file' => $pelamar->file_ktp],
                        ['label' => 'Ijazah (1)', 'file' => $pelamar->file_ijazah],
                        ['label' => 'Transkrip (1)', 'file' => $pelamar->file_transkrip],
                        ['label' => 'Ijazah (2)', 'file' => $pelamar->file_ijazah_2],
                        ['label' => 'Transkrip (2)', 'file' => $pelamar->file_transkrip_2],
                        ['label' => 'Ijazah (3)', 'file' => $pelamar->file_ijazah_3],
                        ['label' => 'Transkrip (3)', 'file' => $pelamar->file_transkrip_3],
                        ['label' => 'Sertifikat Profesi', 'file' => $pelamar->file_sertifikat],
                        ['label' => 'Sertifikat Bahasa', 'file' => $pelamar->file_sertifikat_bahasa],
                    ];
                    $hasDocs = collect($docs)->contains(fn($d) => $d['file']);
                @endphp
                @if($hasDocs)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    @foreach($docs as $doc)
                        @if($doc['file'])
                        <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-colors group">
                            <span class="text-xs font-bold text-gray-600 group-hover:text-blue-700 truncate">{{ $doc['label'] }}</span>
                        </a>
                        @endif
                    @endforeach
                </div>
                @else
                    <p class="text-sm text-gray-400 italic mb-4">-</p>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Kategori Sertifikat</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->kategori_sertifikat ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor Bahasa</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                </div>
            </div>

            {{-- 4. DATA AKADEMIK (DOSEN) --}}
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

            {{-- 5. DOKUMEN PELAMAR BER-HOMEBASE --}}
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

            {{-- 6. HASIL PENILAIAN SELEKSI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Hasil Penilaian Seleksi
                </h3>
                @php
                    $micro = $jadwals_all->where('tipe_seleksi', 'tahap2')->first();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                        ['title' => 'Wawancara', 'jadwal' => $wawancara, 'k1' => 'Kepribadian & Integritas', 'k2' => 'Visi & Profesionalisme', 'k3' => 'Adaptasi & Kolaborasi'],
                        ['title' => 'Micro Teaching', 'jadwal' => $micro, 'k1' => 'Penguasaan Materi', 'k2' => 'Keterampilan Pedagogik', 'k3' => 'Media Pembelajaran']
                    ] as $test)
                        @if($test['jadwal'] && $test['jadwal']->penilaian)
                            <div class="rounded-xl border border-gray-100 p-5 bg-gray-50/50">
                                <h3 class="text-sm font-black text-[#8b1515] uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                                    {{ $test['title'] }}
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center gap-4">
                                        <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">{{ $test['k1'] }}</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $test['jadwal']->penilaian->kategori_1 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-4">
                                        <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">{{ $test['k2'] }}</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $test['jadwal']->penilaian->kategori_2 }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-4">
                                        <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">{{ $test['k3'] }}</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $test['jadwal']->penilaian->kategori_3 }}</span>
                                    </div>
                                    <div class="pt-3 mt-3 border-t border-gray-200 flex justify-between items-center">
                                        <span class="text-xs font-black text-gray-800 uppercase tracking-widest">Total Nilai Akhir</span>
                                        <span class="text-2xl font-black text-[#8b1515]">{{ $test['jadwal']->penilaian->total_nilai }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl border border-gray-100 p-5 bg-gray-50/50">
                                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                                    {{ $test['title'] }}
                                </h3>
                                <p class="text-sm text-gray-400 italic">Belum ada penilaian.</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
