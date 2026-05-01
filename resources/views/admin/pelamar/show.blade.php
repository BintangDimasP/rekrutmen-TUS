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
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Data Diri
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Nama Lengkap</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->nama ?? '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIK (KTP)</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nik ?? '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">No. Telepon / WA</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_telepon ?? '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Kelamin</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tempat Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?? '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p></div>
                    <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Alamat Lengkap</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat ?? '-' }}</p></div>
                </div>
            </div>

            {{-- 2. RIWAYAT PENDIDIKAN --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l-9-5 9-5 9 5-9 5-9 5 9 5z"/></svg>
                    Riwayat Pendidikan
                </h3>
                <div class="space-y-4">
                    @if($pelamar->jenjang)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-[#8b1515]/40 py-2">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang }}</p></div>
                        <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi ?? '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?? '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?? '-' }}</p></div>
                    </div>
                    @endif
                    @if($pelamar->jenjang_2)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 }}</p></div>
                        <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_2 ?? '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?? '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?? '-' }}</p></div>
                    </div>
                    @endif
                    @if($pelamar->jenjang_3)
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2">
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 }}</p></div>
                        <div class="col-span-2"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->institusi_3 ?? '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?? '-' }}</p></div>
                        <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?? '-' }}</p></div>
                    </div>
                    @endif
                    @if(!$pelamar->jenjang)
                        <p class="text-sm text-gray-400 italic">-</p>
                    @endif
                </div>
            </div>

            {{-- 3. DOKUMEN & SERTIFIKAT --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($docs as $doc)
                        @if($doc['file'])
                        <a href="{{ asset('storage/' . $doc['file']) }}" target="_blank" class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-colors group">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span class="text-xs font-bold text-gray-600 group-hover:text-blue-700 truncate">{{ $doc['label'] }}</span>
                        </a>
                        @endif
                    @endforeach
                </div>
                @else
                    <p class="text-sm text-gray-400 italic">-</p>
                @endif

                @if($pelamar->jenis_tes_bahasa)
                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-3 bg-gray-50 rounded-lg p-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->jenis_tes_bahasa }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                </div>
                @endif
            </div>

            {{-- 4. DATA AKADEMIK (DOSEN) --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Data Akademik (Dosen)
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIDN</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nidn ?? '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Homebase</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->homebase ?? '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jabatan Akademik</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik)) : '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">H-Index</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?? '-' }}</p></div>
                </div>
                <div class="mt-3"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Minat Riset & Keahlian</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?? '-' }}</p></div>
            </div>

            {{-- 5. DOKUMEN PELAMAR BER-HOMEBASE --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
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
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
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
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Hasil Penilaian Seleksi
                </h3>
                @php
                    $jadwals = \App\Models\JadwalSeleksi::where('pelamar_id', $pelamar->id)->with('penilaian')->get();
                    $wawancara = $jadwals->where('tipe_seleksi', 'tahap1')->first();
                    $micro = $jadwals->where('tipe_seleksi', 'tahap2')->first();
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                        ['title' => 'Wawancara', 'jadwal' => $wawancara, 'k1' => 'Kepribadian & Integritas', 'k2' => 'Visi & Profesionalisme', 'k3' => 'Adaptasi & Kolaborasi'],
                        ['title' => 'Micro Teaching', 'jadwal' => $micro, 'k1' => 'Penguasaan Materi', 'k2' => 'Keterampilan Pedagogik', 'k3' => 'Media Pembelajaran']
                    ] as $test)
                        <div class="rounded-xl border border-gray-100 p-5 bg-gray-50/50">
                            <h3 class="text-sm font-black text-[#8b1515] uppercase tracking-widest mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $test['title'] }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center gap-4">
                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">{{ $test['k1'] }}</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $test['jadwal']?->penilaian?->kategori_1 ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-4">
                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">{{ $test['k2'] }}</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $test['jadwal']?->penilaian?->kategori_2 ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-4">
                                    <span class="text-[0.65rem] font-bold text-gray-500 uppercase truncate">{{ $test['k3'] }}</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $test['jadwal']?->penilaian?->kategori_3 ?? '-' }}</span>
                                </div>
                                <div class="pt-3 mt-3 border-t border-gray-200 flex justify-between items-center">
                                    <span class="text-xs font-black text-gray-800 uppercase tracking-widest">Total Nilai Akhir</span>
                                    <span class="text-2xl font-black text-[#8b1515]">{{ $test['jadwal']?->penilaian?->total_nilai ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 7. UBAH STATUS LAMARAN --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Ubah Status Lamaran
                </h3>
                
                @forelse($pelamar->lamarans as $lamaran)
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

                    <form method="POST" action="{{ route('admin.lamaran.update', $lamaran) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Jadwal Wawancara --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Wawancara</label>
                                <input type="date" name="tanggal_wawancara"
                                       value="{{ $lamaran->tanggal_wawancara?->format('Y-m-d') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            </div>
                            <div>
                                <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Link Zoom / Meeting</label>
                                <input type="url" name="link_zoom" placeholder="https://zoom.us/j/..."
                                       value="{{ $lamaran->link_zoom }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            </div>
                        </div>

                        {{-- Status Lamaran --}}
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Ubah Status</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['menunggu' => 'Menunggu', 'seleksi_tahap1' => 'Seleksi Tahap 1', 'seleksi_tahap2' => 'Seleksi Tahap 2', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                                <label class="relative">
                                    <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                                           {{ $lamaran->status === $val ? 'checked' : '' }}>
                                    <span class="cursor-pointer inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold border-2 transition-all
                                        peer-checked:border-[#8b1515] peer-checked:bg-[#8b1515] peer-checked:text-white
                                        border-gray-200 text-gray-600 hover:border-gray-300 bg-white select-none shadow-sm">
                                        {{ $label }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Catatan Admin --}}
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Catatan (Opsional)</label>
                            <textarea name="catatan_admin" rows="2" placeholder="Catatan untuk pelamar atau internal..."
                                      class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-none">{{ $lamaran->catatan_admin }}</textarea>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#8b1515] hover:bg-red-900 text-white text-sm font-bold rounded-lg shadow-md shadow-red-900/20 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
                @empty
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-4 rounded-xl border border-gray-100">Pelamar ini belum mengajukan lamaran ke lowongan manapun.</p>
                @endforelse
            </div>

        </div>
    </div>
</div>

@endsection
