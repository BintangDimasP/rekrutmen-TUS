@extends('layouts.admin')

@section('title', 'Detail Pelamar — ' . $pelamar->nama)

@section('content')

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
        <a href="{{ route('admin.pelamar.index') }}" class="hover:text-[#8b1515] transition-colors">Pelamar</a>
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
                            <span class="text-red-200 text-xs">Terdaftar: {{ $pelamar->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
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
                    $allJadwals = \App\Models\JadwalSeleksi::where('pelamar_id', $pelamar->id)->with(['penilaian', 'lowongan'])->get();
                    $hasAnyPenilaian = false;
                @endphp

                <div class="space-y-6">
                    @foreach($pelamar->lamarans as $lamaran)
                        @php
                            $wawancara = $allJadwals->where('lowongan_id', $lamaran->lowongan_id)->where('tipe_seleksi', 'tahap1')->first();
                            $micro = $allJadwals->where('lowongan_id', $lamaran->lowongan_id)->where('tipe_seleksi', 'tahap2')->first();
                            
                            $hasWawancaraScore = $wawancara && $wawancara->penilaian;
                            $hasMicroScore = $micro && $micro->penilaian;
                        @endphp

                        @if($hasWawancaraScore || $hasMicroScore)
                            @php $hasAnyPenilaian = true; @endphp
                            <div>
                                <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">
                                    Lamaran: <span class="text-[#8b1515]">{{ $lamaran->lowongan?->nama_posisi ?? '—' }}</span>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($hasWawancaraScore)
                                        <div class="rounded-xl border border-gray-100 p-5 bg-gray-50/50">
                                            <h3 class="text-sm font-black text-[#8b1515] uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                                                Wawancara
                                            </h3>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center gap-4">
                                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">Kepribadian & Integritas</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $wawancara->penilaian->kategori_1 }}</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-4">
                                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">Visi & Profesionalisme</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $wawancara->penilaian->kategori_2 }}</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-4">
                                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">Adaptasi & Kolaborasi</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $wawancara->penilaian->kategori_3 }}</span>
                                                </div>
                                                <div class="pt-3 mt-3 border-t border-gray-200 flex justify-between items-center">
                                                    <span class="text-xs font-black text-gray-800 uppercase tracking-widest">Total Nilai Akhir</span>
                                                    <span class="text-2xl font-black text-[#8b1515]">{{ $wawancara->penilaian->total_nilai }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($hasMicroScore)
                                        <div class="rounded-xl border border-gray-100 p-5 bg-gray-50/50">
                                            <h3 class="text-sm font-black text-[#8b1515] uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">
                                                Micro Teaching
                                            </h3>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center gap-4">
                                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">Penguasaan Materi</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $micro->penilaian->kategori_1 }}</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-4">
                                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">Keterampilan Pedagogik</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $micro->penilaian->kategori_2 }}</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-4">
                                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">Media Pembelajaran</span>
                                                    <span class="text-sm font-bold text-gray-800">{{ $micro->penilaian->kategori_3 }}</span>
                                                </div>
                                                <div class="pt-3 mt-3 border-t border-gray-200 flex justify-between items-center">
                                                    <span class="text-xs font-black text-gray-800 uppercase tracking-widest">Total Nilai Akhir</span>
                                                    <span class="text-2xl font-black text-[#8b1515]">{{ $micro->penilaian->total_nilai }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasAnyPenilaian)
                        <p class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded-xl border border-gray-100">Belum ada hasil penilaian seleksi yang masuk untuk pelamar ini.</p>
                    @endif
                </div>
            </div>

            {{-- 7. UBAH STATUS LAMARAN --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Ubah Status Lamaran
                </h3>
                
                @forelse($pelamar->lamarans as $lamaran)
                @php
                    $isFinished = in_array($lamaran->status, ['diterima', 'ditolak']);
                    $wawancara = $allJadwals->where('lowongan_id', $lamaran->lowongan_id)->where('tipe_seleksi', 'tahap1')->first();
                    $micro = $allJadwals->where('lowongan_id', $lamaran->lowongan_id)->where('tipe_seleksi', 'tahap2')->first();
                    
                    $hasJadwal = $allJadwals->where('lowongan_id', $lamaran->lowongan_id)->isNotEmpty();
                    $hasBothScores = ($wawancara && $wawancara->penilaian) && ($micro && $micro->penilaian);
                    
                    $statusOrder = ['menunggu' => 1, 'seleksi_tahap1' => 2, 'seleksi_tahap2' => 3, 'diterima' => 4, 'ditolak' => 4];
                    $currentOrder = $statusOrder[$lamaran->status] ?? 1;
                @endphp
                
                <div class="bg-gray-50 rounded-xl p-5 mb-4 border border-gray-100">
                    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-gray-200 pb-3">
                        <div>
                            <h4 class="text-sm font-bold text-[#8b1515] uppercase tracking-wider">Lamaran: {{ $lamaran->lowongan?->nama_posisi ?? '—' }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $lamaran->lowongan?->prodi?->nama ?? '-' }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'menunggu'       => 'bg-gray-100 text-gray-600 border-gray-200',
                                'seleksi_tahap1' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'seleksi_tahap2' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'diterima'       => 'bg-green-50 text-green-700 border-green-200',
                                'ditolak'        => 'bg-red-50 text-red-700 border-red-200',
                            ];
                        @endphp
                        <span class="inline-flex px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusColors[$lamaran->status] ?? $statusColors['menunggu'] }}">
                            Status Saat Ini: {{ $lamaran->status_label }}
                        </span>
                    </div>

                    @if($isFinished)
                        <div class="flex items-center gap-4 p-4 bg-green-50/50 border border-green-100 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-green-800">Alur Seleksi Selesai</h5>
                                <p class="text-xs text-green-600/80 mt-0.5">Status akhir pelamar ini telah ditetapkan ({{ $lamaran->status_label }}). Form ubah status telah dinonaktifkan.</p>
                            </div>
                        </div>
                    @else
                    <form method="POST" action="{{ route('admin.lamaran.update', $lamaran) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Status Lamaran --}}
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Ubah Status</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['menunggu' => 'Menunggu', 'seleksi_tahap1' => 'Seleksi Tahap 1', 'seleksi_tahap2' => 'Seleksi Tahap 2', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                                @php
                                    $isDisabled = false;
                                    $targetOrder = $statusOrder[$val];
                                    
                                    // Tidak bisa kembali ke status sebelumnya
                                    if ($targetOrder < $currentOrder) {
                                        $isDisabled = true;
                                    }
                                    
                                    // Seleksi Tahap 2 butuh jadwal minimal 1
                                    if ($val === 'seleksi_tahap2' && !$hasJadwal && $currentOrder < 3) {
                                        $isDisabled = true;
                                    }
                                    
                                    // Diterima/Ditolak butuh kedua nilai
                                    if (($val === 'diterima' || $val === 'ditolak') && !$hasBothScores) {
                                        $isDisabled = true;
                                    }
                                @endphp
                                <label class="relative {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $isDisabled ? 'title="Syarat belum terpenuhi atau tidak bisa kembali ke status sebelumnya"' : '' }}>
                                    <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                                           {{ $lamaran->status === $val ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                    <span class="cursor-pointer inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold border-2 transition-all
                                        peer-checked:border-[#8b1515] peer-checked:bg-[#8b1515] peer-checked:text-white
                                        border-gray-200 text-gray-600 peer-not-disabled:hover:border-gray-300 bg-white select-none shadow-sm">
                                        {{ $label }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            
                            @if(!$hasJadwal && $currentOrder < 3)
                                <p class="text-[0.65rem] font-bold text-amber-600 mt-2 flex items-center gap-1.5 uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Tahap 2 butuh penjadwalan
                                </p>
                            @endif
                            @if(!$hasBothScores && $currentOrder < 4)
                                <p class="text-[0.65rem] font-bold text-amber-600 mt-1 flex items-center gap-1.5 uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Keputusan akhir butuh ke-2 nilai seleksi
                                </p>
                            @endif
                        </div>

                        {{-- Catatan Admin --}}
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Catatan (Opsional)</label>
                            <textarea name="catatan_admin" rows="2" placeholder="Catatan untuk pelamar atau internal..."
                                      class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-none">{{ $lamaran->catatan_admin }}</textarea>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#8b1515] hover:bg-red-900 text-white text-sm font-bold rounded-lg shadow-md shadow-red-900/20 transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
                @empty
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded-xl border border-gray-100">Pelamar ini belum mengajukan lamaran ke lowongan manapun.</p>
                @endforelse
            </div>

        </div>
    </div>
</div>

@endsection
