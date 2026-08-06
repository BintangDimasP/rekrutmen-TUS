@extends('layouts.admin')

@section('title', 'Detail Lamaran ' )

@section('content')
@php
    // Gunakan snapshot data jika tersedia, fallback ke data live
    $pelamar = $lamaran->effective_pelamar;
    $hasSnapshot = !empty($lamaran->snapshot_data);
@endphp



<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.lowongan.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Lowongan</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.lamaran.index', $lamaran->lowongan_id) }}" class="hover:text-[#8b1515] transition-colors font-medium">{{ $lamaran->lowongan->nama_posisi }}</a>
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
                            @if($hasSnapshot)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/10 border border-white/20 text-white/70 text-[0.6rem] font-semibold">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Data saat melamar
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col items-end gap-2">

                    {{-- Status Badge --}}
                    @php
                        $statusColors = [
                            'seleksi_tahap1' => 'bg-white text-blue-700 border-white shadow-sm',
                            'seleksi_tahap2' => 'bg-white text-indigo-700 border-white shadow-sm',
                            'diterima'       => 'bg-white text-green-700 border-white shadow-sm',
                            'ditolak'        => 'bg-white text-red-700 border-white shadow-sm',
                            'mengundurkan_diri' => 'bg-white text-gray-700 border-white shadow-sm',
                        ];
                        
                        $label = $lamaran->status_label;
                        $colorClass = $statusColors[$lamaran->status] ?? 'bg-white/20 text-white border-white/30';

                        if ($lamaran->status === 'menunggu') {
                            if ($lamaran->is_direkomendasikan_kaprodi === true) {
                                $label = 'Direkomendasikan';
                                $colorClass = 'bg-white text-green-700 border-white shadow-sm';
                            } elseif ($lamaran->is_direkomendasikan_kaprodi === false) {
                                $label = 'Tidak Direkomendasi';
                                $colorClass = 'bg-white text-red-700 border-white shadow-sm';
                            } else {
                                $label = 'Menunggu Review';
                                $colorClass = 'bg-white/20 text-white border-white/30';
                            }
                        }
                    @endphp
                    <span class="inline-flex px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest border backdrop-blur-sm {{ $colorClass }}">
                        {{ $label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- CONTENT: Full Profile & Status -->
        <div class="p-6 md:p-8 space-y-8">

                        {{-- 1. DETAIL PELAMAR LAINNYA (RIWAYAT PENDIDIKAN, DOKUMEN, DLL) --}}
            <div class="pt-6 space-y-8">

            {{-- 1. DATA DIRI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Data Diri
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-5">

                    {{-- Baris 1: Nama | NIK | Jenis Kelamin --}}
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Nama Lengkap</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->nama ?: '-' }}</p></div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">NIK (KTP)</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->nik ?: '-' }}</p></div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenis Kelamin</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p></div>

                    {{-- Baris 2: Tempat Lahir | Tanggal Lahir | Kewarganegaraan --}}
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tempat Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?: '-' }}</p></div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tanggal Lahir</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p></div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Kewarganegaraan</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->kewarganegaraan ?: '-' }}</p></div>

                    {{-- Baris 3: Status Pernikahan | No. Telepon | Email --}}
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Status Pernikahan</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->status_pernikahan ?: '-' }}</p></div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Telepon / WA</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_telepon ?: '-' }}</p></div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Email</p><p class="text-sm text-gray-700 mt-0.5">{{ $lamaran->pelamar->user?->email ?: '-' }}</p></div>

                    {{-- Baris 4: Alamat Domisili & KTP --}}
                    <div class="col-span-1 md:col-span-2"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Domisili</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_domisili ?: '-' }}</p></div>
                    <div class="col-span-1 md:col-span-2"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Sesuai KTP</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_ktp ?: '-' }}</p></div>

                </div>
            </div>

          
                    {{-- RIWAYAT PENDIDIKAN --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Riwayat Pendidikan
                        </h3>
                        <div class="space-y-8">
                            @if($pelamar->jenjang)
                            <div class="pl-4 border-l-[3px] border-[#8b1515]/40 py-1">
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-x-3 gap-y-4">
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?: '-' }}</p></div>
                                    
                                    <div>
                                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Ijazah {{ $pelamar->jenjang }}</p>
                                        @if($pelamar->file_ijazah)
                                            <a href="{{ file_url($pelamar->file_ijazah) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-sm text-gray-700 mt-0.5">-</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Transkrip {{ $pelamar->jenjang }}</p>
                                        @if($pelamar->file_transkrip)
                                            <a href="{{ file_url($pelamar->file_transkrip) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-sm text-gray-700 mt-0.5">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($pelamar->jenjang_2)
                            <div class="pl-4 border-l-[3px] border-gray-200 py-1">
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-x-3 gap-y-4">
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_2 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?: '-' }}</p></div>
                                    
                                    <div>
                                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Ijazah</p>
                                        @if($pelamar->file_ijazah_2)
                                            <a href="{{ file_url($pelamar->file_ijazah_2) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-sm text-gray-700 mt-0.5">-</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Transkrip</p>
                                        @if($pelamar->file_transkrip_2)
                                            <a href="{{ file_url($pelamar->file_transkrip_2) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-sm text-gray-700 mt-0.5">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($pelamar->jenjang_3)
                            <div class="pl-4 border-l-[3px] border-gray-200 py-1">
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-x-3 gap-y-4">
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenjang</p><p class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Institusi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Prodi</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Akreditas</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->akreditas_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Ijazah</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_3 ?: '-' }}</p></div>
                                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">IPK</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?: '-' }}</p></div>
                                    
                                    <div>
                                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Ijazah</p>
                                        @if($pelamar->file_ijazah_3)
                                            <a href="{{ file_url($pelamar->file_ijazah_3) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-sm text-gray-700 mt-0.5">-</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Transkrip</p>
                                        @if($pelamar->file_transkrip_3)
                                            <a href="{{ file_url($pelamar->file_transkrip_3) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                        @else
                                            <p class="text-sm text-gray-700 mt-0.5">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(!$pelamar->jenjang && !$pelamar->jenjang_2 && !$pelamar->jenjang_3)
                                <p class="text-sm text-gray-400 italic">-</p>
                            @endif
                        </div>
                    </div>

                    {{-- DOKUMEN PENDUKUNG --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Dokumen Pendukung
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-4 mb-8">
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">CV (Resume)</p>
                                @if($pelamar->file_cv)
                                    <a href="{{ file_url($pelamar->file_cv) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">Pas Foto Formal</p>
                                @if($pelamar->file_pas_foto)
                                    <a href="{{ file_url($pelamar->file_pas_foto) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">Scan KTP</p>
                                @if($pelamar->file_ktp)
                                    <a href="{{ file_url($pelamar->file_ktp) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                            
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">{{ $pelamar->kategori_sertifikat ?: 'Sertifikat' }}</p>
                                @if($pelamar->file_sertifikat)
                                    <a href="{{ file_url($pelamar->file_sertifikat) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">Surat Lamaran</p>
                                @if($lamaran->file_surat_lamaran)
                                    <a href="{{ file_url($lamaran->file_surat_lamaran) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">SK Penyetaraan <span class="normal-case font-medium text-gray-300">(Lulusan LN)</span></p>
                                @if($lamaran->file_sk_penyetaraan)
                                    <a href="{{ file_url($lamaran->file_sk_penyetaraan) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">Surat Pemberhentian <span class="normal-case font-medium text-gray-300">(Instansi Lain)</span></p>
                                @if($lamaran->file_surat_pemberhentian)
                                    <a href="{{ file_url($lamaran->file_surat_pemberhentian) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SERTIFIKAT BAHASA INGGRIS --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Sertifikat Bahasa Inggris
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-4">
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Skor Bahasa</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                            <div>
                                <p class="text-[0.55rem] font-black text-gray-400 uppercase">Sertifikat Bahasa</p>
                                @if($pelamar->file_sertifikat_bahasa)
                                    <a href="{{ $pelamar->fileUrl('file_sertifikat_bahasa') }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
                                @else
                                    <p class="text-sm text-gray-700 mt-0.5">-</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- DATA AKADEMIK (DOSEN) --}}
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Data Akademik (Dosen)
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-4">
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">NIDN</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->nidn ?: '-' }}</p></div>
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Homebase</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->homebase ?: '-' }}</p></div>
                            @php
                                $jfaLabels = [
                                    'guru_besar' => 'Guru Besar (GB)',
                                    'lektor_kepala' => 'Lektor Kepala (LK)',
                                    'lektor' => 'Lektor (L)',
                                    'asisten_ahli' => 'Asisten Ahli (AA)',
                                    'non_jabatan' => 'Non Jabatan (NJAD)',
                                ];
                            @endphp
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jabatan Fungsional Akademik</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ($jfaLabels[$pelamar->jabatan_akademik] ?? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik))) : '-' }}</p></div>
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">H-Index</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?: '-' }}</p></div>
                        </div>
                        <div class="mt-3"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Minat Riset & Keahlian</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?: '-' }}</p></div>
                    </div>

                    {{-- DOKUMEN PELAMAR BER-HOMEBASE --}}
                    @if($pelamar->nidn || $pelamar->homebase)
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
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                            @foreach($homebaseDocs as $doc)
                            <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">{{ $doc['label'] }}</p>
                                @if($doc['file'])<a href="{{ $pelamar->fileUrl($doc['file']) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>@else<p class="text-sm text-gray-700 mt-0.5">-</p>@endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

            {{-- 2. JADWAL & SUMMARY PENILAIAN SELEKSI --}}
            @php
                $microDinilai     = $micro->filter(fn($j) => $j->penilaian !== null);
                $wawancaraDinilai = $wawancara->filter(fn($j) => $j->penilaian !== null);

                $microKategoriLabels = [
                    1 => 'PP',
                    2 => 'PMP',
                    3 => 'Sis',
                    4 => 'PKI',
                    5 => 'SE',
                    6 => 'MWP',
                ];
                $microKategoriTooltips = [
                    1 => 'Perencanaan Pembelajaran',
                    2 => 'Penggunaan Media Pembelajaran',
                    3 => 'Sistematika',
                    4 => 'Pengelolaan Kelas & Interaksi',
                    5 => 'Sikap & Etika',
                    6 => 'Manajemen Waktu Pembelajaran',
                ];
                // Wawancara: 5 indikator flat
                $wawancaraIndikatorLabels = [
                    1 => 'Mot',
                    2 => 'PotKon',
                    3 => 'KPP',
                    4 => 'KKom',
                    5 => 'KonRel',
                ];
                $wawancaraIndikatorTooltips = [
                    1 => 'motivasi',
                    2 => 'Potensi Kontribusi terhadap Program Studi dan Institusi',
                    3 => 'Kemampuan Penelitian & Publikasi',
                    4 => 'Kemampuan Komunikasi, Terutama Menjawab Pertanyaan Dengan Cepat dan Tepat',
                    5 => 'Kontribusi yang Pernah Dilakukan / Memiliki Link Relasi Dengan Pihak Lain',
                ];
                $wawancaraKategoriLabels = []; // tidak dipakai untuk wawancara

                $nilaiAkhirMicro = $microDinilai->count() > 0
                    ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2)
                    : null;
                $nilaiAkhirWawancara = $wawancaraDinilai->count() > 0
                    ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2)
                    : null;
            @endphp

            <div class="pt-6 border-t border-gray-100">
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Jadwal &amp; Penilaian Seleksi
                        </h3>

                @if(($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0))
                <div class="space-y-6">

                    {{-- -- MICRO TEACHING -- --}}
                    @if($micro && $micro->count() > 0)
                    <div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">
                            Micro Teaching
                            <span class="text-gray-500 font-normal">{{ $micro[0]->tanggal->format('d M Y') }} – {{ $micro[0]->session_label }}</span>
                            
                        </h4>
                        <div class="border border-gray-200 rounded-xl overflow-hidden">

                        @if($microDinilai->count() === 0)
                        <div class="px-4 py-3 bg-yellow-50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-yellow-700">Menunggu penilaian &mdash; Penguji: {{ $micro->pluck('penguji.nama')->filter()->implode(', ') }} (0/{{ $micro->count() }} sudah menilai)</p>
                        </div>
                        @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border border-gray-200 border-collapse" style="min-width:600px">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
                                        <th class="px-4 py-2 text-left font-semibold border border-gray-200">Penguji</th>
                                        @foreach($microKategoriLabels as $kNum => $kShort)
                                        <th class="px-3 py-2 text-center font-semibold border border-gray-200" title="{{ $microKategoriTooltips[$kNum] }}">{{ $kShort }}</th>
                                        @endforeach
                                        <th class="px-3 py-2 text-center font-semibold border border-gray-200">Avg</th>
                                        <th class="px-3 py-2 text-center font-semibold border border-gray-200">Status</th>
                                        <th class="px-3 py-2 text-left font-semibold border border-gray-200">Prodi</th>
                                        <th class="px-3 py-2 text-left font-semibold border border-gray-200">Kelompok</th>
                                        <th class="px-3 py-2 text-left font-semibold border border-gray-200">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($microDinilai->values() as $idx => $jadwalMicro)
                                    @php
                                        $p = $jadwalMicro->penilaian;
                                        $rekLabels = ['direkomendasikan' => ['label' => 'Direkomendasikan', 'color' => 'bg-green-50 text-green-700'], 'tidak_direkomendasikan' => ['label' => 'Tidak Direkomendasikan', 'color' => 'bg-red-50 text-red-700'], 'perlu_dipertimbangkan' => ['label' => 'Perlu Dipertimbangkan', 'color' => 'bg-yellow-50 text-yellow-700']];
                                        $rek = $p->rekomendasi ? ($rekLabels[$p->rekomendasi] ?? ['label' => $p->rekomendasi, 'color' => 'bg-gray-50 text-gray-700']) : null;
                                        $kkLabels = ['scout' => 'SCoT', 'ethes' => 'ETHES', 'riib' => 'RIIB'];
                                    @endphp
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-2.5 text-xs text-gray-600 whitespace-nowrap border border-gray-200" title="{{ $jadwalMicro->penguji->nama ?? '' }}">{{ $jadwalMicro->penguji->kode ?? '-' }}</td>
                                        @foreach($microKategoriLabels as $kNum => $kShort)
                                        <td class="px-3 py-2.5 text-center font-bold text-gray-800 border border-gray-200">{{ $p->{'kategori_'.$kNum} ?? '-' }}</td>
                                        @endforeach
                                        <td class="px-3 py-2.5 text-center border border-gray-200">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-xs font-bold rounded">{{ $p->total_nilai }}</span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center border border-gray-200">
                                            @if($rek)
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->prodi_tujuan ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->kelompok_keahlian ? ($kkLabels[$p->kelompok_keahlian] ?? $p->kelompok_keahlian) : '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-600 max-w-xs border border-gray-200">{{ $p->catatan ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @foreach($micro->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                                    <tr class="bg-yellow-50/40">
                                        <td class="px-4 py-2.5 text-xs text-gray-500 border border-gray-200" title="{{ $jadwalBelum->penguji->nama ?? '' }}">{{ $jadwalBelum->penguji->kode ?? '-' }}</td>
                                        <td colspan="{{ count($microKategoriLabels) + 7 }}" class="px-3 py-2.5 text-xs text-yellow-600 font-semibold border border-gray-200">Belum menilai</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($nilaiAkhirMicro !== null)
                                <tfoot>
                                    <tr class="bg-gray-100 border-t border-gray-200">
                                        <td class="px-4 py-2.5 text-xs text-center font-bold text-gray-600 uppercase " colspan="{{ count($microKategoriLabels) + 1 }}">Nilai Akhir </td>
                                        <td class="px-3 py-2.5 text-center border border-gray-200">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-sm font-black rounded">{{ $nilaiAkhirMicro }}</span>
                                        </td>
                                        <td colspan="6"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- -- WAWANCARA -- --}}
                    @if($wawancara && $wawancara->count() > 0)
                    <div class="mt-4">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">
                            Wawancara
                            <span class="text-gray-500 font-normal">{{ $wawancara[0]->tanggal->format('d M Y') }} – {{ $wawancara[0]->session_label }}</span>
                            
                        </h4>
                        <div class="border border-gray-200 rounded-xl overflow-hidden">

                        @if($wawancaraDinilai->count() === 0)
                        <div class="px-4 py-3 bg-yellow-50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-yellow-700">Menunggu penilaian &mdash; Penguji: {{ $wawancara->pluck('penguji.nama')->filter()->implode(', ') }} (0/{{ $wawancara->count() }} sudah menilai)</p>
                        </div>
                        @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border border-gray-200 border-collapse" style="min-width:600px">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
                                        <th class="px-4 py-2 text-left font-semibold border border-gray-200">Penguji</th>
                                        @foreach($wawancaraIndikatorLabels as $iNum => $iShort)
                                        <th class="px-3 py-2 text-center font-semibold border border-gray-200" title="{{ $wawancaraIndikatorTooltips[$iNum] }}">{{ $iShort }}</th>
                                        @endforeach
                                        <th class="px-3 py-2 text-center font-semibold border border-gray-200">Avg</th>
                                        <th class="px-3 py-2 text-center font-semibold border border-gray-200">Status</th>
                                        <th class="px-3 py-2 text-left font-semibold border border-gray-200">Prodi</th>
                                        <th class="px-3 py-2 text-left font-semibold border border-gray-200">Bidang</th>
                                        <th class="px-3 py-2 text-left font-semibold border border-gray-200">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach($wawancaraDinilai->values() as $idx => $jadwalWaw)
                                    @php
                                        $p = $jadwalWaw->penilaian;
                                        $detail = $p->detail_nilai ?? [];
                                        $rekLabels = ['direkomendasikan' => ['label' => 'Direkomendasikan', 'color' => 'bg-green-50 text-green-700'], 'tidak_direkomendasikan' => ['label' => 'Tidak Direkomendasikan', 'color' => 'bg-red-50 text-red-700'], 'perlu_dipertimbangkan' => ['label' => 'Perlu Dipertimbangkan', 'color' => 'bg-yellow-50 text-yellow-700']];
                                        $rek = $p->rekomendasi ? ($rekLabels[$p->rekomendasi] ?? ['label' => $p->rekomendasi, 'color' => 'bg-gray-50 text-gray-700']) : null;
                                    @endphp
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-2.5 text-xs text-gray-600 whitespace-nowrap border border-gray-200" title="{{ $jadwalWaw->penguji->nama ?? '' }}">{{ $jadwalWaw->penguji->kode ?? '-' }}</td>
                                        @foreach($wawancaraIndikatorLabels as $iNum => $iShort)
                                        <td class="px-3 py-2.5 text-center font-bold text-gray-800 border border-gray-200">{{ $detail['k1_item_'.$iNum] ?? '-' }}</td>
                                        @endforeach
                                        <td class="px-3 py-2.5 text-center border border-gray-200">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-xs font-bold rounded">{{ $p->total_nilai }}</span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center border border-gray-200">
                                            @if($rek)
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->prodi_tujuan ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->bidang_keahlian ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-600 max-w-xs border border-gray-200">{{ $p->catatan ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @foreach($wawancara->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                                    <tr class="bg-yellow-50/40">
                                        <td class="px-4 py-2.5 text-xs text-gray-500 border border-gray-200" title="{{ $jadwalBelum->penguji->nama ?? '' }}">{{ $jadwalBelum->penguji->kode ?? '-' }}</td>
                                        <td colspan="{{ count($wawancaraIndikatorLabels) + 4 }}" class="px-3 py-2.5 text-xs text-yellow-600 font-semibold border border-gray-200">Belum menilai</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($nilaiAkhirWawancara !== null)
                                <tfoot>
                                    <tr class="bg-gray-100 border-t border-gray-200">
                                        <td class="px-4 py-2.5 text-xs text-center font-bold text-gray-600 uppercase border border-gray-200" colspan="{{ count($wawancaraIndikatorLabels) + 1 }}">Nilai Akhir</td>
                                        <td class="px-3 py-2.5 text-center border border-gray-200">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-sm font-black rounded">{{ $nilaiAkhirWawancara }}</span>
                                        </td>
                                        <td colspan="4" class="border border-gray-200"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                        @endif
                        </div>
                    </div>
                    @endif

                </div>
                    @else
                    <div class="p-8 rounded-2xl border border-gray-200 bg-gray-50 text-center">
                        <p class="text-sm font-bold text-gray-600">Belum Ada Jadwal</p>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Jadwal seleksi belum ditentukan. Silakan buat jadwal seleksi di menu Jadwal Seleksi jika diperlukan.</p>
                    </div>
                    @endif

                    {{-- -- KUALIFIKASI DOSEN -- --}}
                    @php
                        // Ambil status rekrutmen dari penilaian wawancara (penguji pertama yang menilai)
                        $statusRekrutmenNilai = $wawancaraDinilai->first()?->penilaian?->status_rekrutmen ?? null;

                        // Skor SPT (Jalur Lamaran / Pendidikan)
                        $jenjangTertinggi = collect([$pelamar->jenjang_3, $pelamar->jenjang_2, $pelamar->jenjang])
                            ->filter()
                            ->map(fn($j) => strtolower(trim($j)))
                            ->first();
                        $isS3 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's3') || str_contains($jenjangTertinggi, 'doktor'));
                        $isS2 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's2') || str_contains($jenjangTertinggi, 'magister') || str_contains($jenjangTertinggi, 'master'));

                        $sptSkor = 0;
                        $sptLabel = '-';
                        $sptPending = false; // true bila S2/S3 tapi status rekrutmen belum diisi penguji
                        if ($isS3) {
                            if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 5; $sptLabel = 'S3 Prof Full Time'; }
                            elseif ($statusRekrutmenNilai === 'praktisi_part_time')  { $sptSkor = 4; $sptLabel = 'S3 Praktisi Part Time'; }
                            elseif ($statusRekrutmenNilai === 'on_going')            { $sptSkor = 3; $sptLabel = 'S3 On Going'; }
                            else { $sptPending = true; $sptLabel = 'S3 (belum dinilai)'; }
                        } elseif ($isS2) {
                            if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 2; $sptLabel = 'S2 Prof Full Time'; }
                            elseif ($statusRekrutmenNilai === 'praktisi_part_time')  { $sptSkor = 1; $sptLabel = 'S2 Praktisi Part Time'; }
                            else { $sptPending = true; $sptLabel = 'S2 (belum dinilai)'; }
                        } else {
                            // S1 / D3 / lainnya: tidak mendapat skor SPT, namun tidak menunggu penilaian
                            $sptLabel = $jenjangTertinggi ? strtoupper($jenjangTertinggi) : '-';
                        }

                        // Skor JFA
                        $jfaSkorMap = ['guru_besar' => 5, 'lektor_kepala' => 4, 'lektor' => 3, 'asisten_ahli' => 2, 'non_jabatan' => 1];
                        $jfaLabelMap = ['guru_besar' => 'Guru Besar (GB)', 'lektor_kepala' => 'Lektor Kepala (LK)', 'lektor' => 'Lektor (L)', 'asisten_ahli' => 'Asisten Ahli (AA)', 'non_jabatan' => 'Non Jabatan (NJAD)'];
                        $jfaKey = $pelamar->jabatan_akademik ?? 'non_jabatan';
                        $jfaSkor = $jfaSkorMap[$jfaKey] ?? 1;
                        $jfaLabel = $jfaLabelMap[$jfaKey] ?? 'Non Jabatan (NJAD)';

                        // Skor H-Index
                        $hIndex = (int) ($pelamar->h_index ?? 0);
                        if ($hIndex > 10)      { $hSkor = 5; }
                        elseif ($hIndex >= 5)  { $hSkor = 4; }
                        elseif ($hIndex >= 2)  { $hSkor = 3; }
                        elseif ($hIndex >= 1)  { $hSkor = 2; }
                        else                   { $hSkor = 1; }

                        // AVG Kualifikasi: dihitung kecuali sedang menunggu status rekrutmen S2/S3
                        $avgKualifikasi = $sptPending
                            ? null
                            : round(($sptSkor + $jfaSkor + $hSkor) / 3, 2);

                        // Hasil Akhir
                        $hasilAkhir = null;
                        if ($nilaiAkhirMicro !== null && $nilaiAkhirWawancara !== null && $avgKualifikasi !== null) {
                            $hasilAkhir = round(
                                ($nilaiAkhirMicro * 0.20) + ($nilaiAkhirWawancara * 0.40) + ($avgKualifikasi * 0.40),
                                2
                            );
                        }
                    @endphp

                    @if($nilaiAkhirWawancara !== null || $avgKualifikasi !== null)
                    <div class="mt-8">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Kualifikasi & Hasil Akhir</h4>
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full text-sm border-collapse" style="min-width:650px">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500">
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">SPT</th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">JFA</th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">H-Index</th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">Avg Kualifikasi</th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">AVG Micro </th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">AVG WWC </th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">AVG Kual</th>
                                        <th class="px-4 py-2 text-center font-semibold border border-gray-200">Hasil Akhir</th>
                                    </tr>
                                    
                                </thead>
                                <tbody class="bg-white">
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-center font-black text-gray-800 text-base border border-gray-200">
                                            @if($sptPending) <span class="text-gray-300">-</span> @else {{ $sptSkor }} @endif
                                        </td>
                                        <td class="px-4 py-3 text-center font-black text-gray-800 text-base border border-gray-200">{{ $jfaSkor }}</td>
                                        <td class="px-4 py-3 text-center font-black text-gray-800 text-base border border-gray-200">{{ $hSkor }}</td>
                                        <td class="px-4 py-3 text-center border border-gray-200">
                                            @if($avgKualifikasi !== null)
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-sm font-black rounded">{{ $avgKualifikasi }}</span>
                                            @else <span class="text-gray-300">-</span> @endif
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700 border border-gray-200">{{ $nilaiAkhirMicro ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700 border border-gray-200">{{ $nilaiAkhirWawancara ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700 border border-gray-200">{{ $avgKualifikasi ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center border border-gray-200 bg-gray-800">
                                            @if($hasilAkhir !== null)
                                            <span class="text-xl font-black text-white">{{ $hasilAkhir }}</span>
                                            @else <span class="text-gray-400 text-xs">Belum lengkap</span> @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
                                <p class="text-[0.65rem] text-gray-400">(Micro–20%) + (WWC–40%) + (Kualifikasi–40%)</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- STATUS LAMARAN --}}
                    <div id="status-lamaran" class="pt-6 border-t border-gray-100">
                        @php
                            $isFinished = in_array($lamaran->status, ['diterima', 'ditolak']);
                            $hasJadwal = ($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0);
                            $hasBothScores = ($wawancara && $wawancara->count() > 0 && $wawancara->every(fn($j) => $j->penilaian !== null)) && ($micro && $micro->count() > 0 && $micro->every(fn($j) => $j->penilaian !== null));
                            $statusOrder = ['menunggu' => 1, 'seleksi_tahap1' => 2, 'seleksi_tahap2' => 3, 'diterima' => 4, 'ditolak' => 4];
                            $currentOrder = $statusOrder[$lamaran->status] ?? 1;

                            $steps = [
                                ['key' => 'menunggu',       'label' => 'Menunggu',   'sub' => 'Administrasi'],
                                ['key' => 'seleksi_tahap1', 'label' => 'Tahap 1',    'sub' => 'Seleksi Administrasi'],
                                ['key' => 'seleksi_tahap2', 'label' => 'Tahap 2',    'sub' => 'Micro Teaching & Wawancara'],
                                ['key' => 'final',          'label' => 'Keputusan',  'sub' => $lamaran->status === 'diterima' ? 'Diterima' : ($lamaran->status === 'ditolak' ? 'Ditolak' : 'Diterima / Ditolak')],
                            ];
                        @endphp

                        <div x-data="{ editing: false, selected: '{{ $lamaran->status }}' }">

                            {{-- HEADER --}}
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Status Lamaran</h3>
                                @if(!$isFinished)
                                <button @click="editing = !editing; selected = '{{ $lamaran->status }}'"
                                    class="p-1.5 rounded-lg transition-colors"
                                    :class="editing ? 'bg-gray-100 text-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
                                    title="Ubah Status">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                @endif
                            </div>

                            {{-- STEPPER (read-only) --}}
                            <div x-show="!editing" class="flex items-start">
                                @foreach($steps as $i => $step)
                                @php
                                    if ($step['key'] === 'final') {
                                        $isActive = in_array($lamaran->status, ['diterima', 'ditolak']);
                                        $isPast   = false;
                                    } else {
                                        $keyOrder = $statusOrder[$step['key']] ?? 1;
                                        $isActive = $lamaran->status === $step['key'];
                                        $isPast   = $currentOrder > $keyOrder;
                                    }
                                    if ($isActive && $step['key'] === 'final') {
                                        $circleClass = $lamaran->status === 'diterima' ? 'bg-green-600 border-green-600 text-white' : 'bg-red-600 border-red-600 text-white';
                                        $labelClass  = $lamaran->status === 'diterima' ? 'text-green-700 font-bold' : 'text-red-700 font-bold';
                                    } elseif ($isActive) {
                                        $circleClass = 'bg-[#8b1515] border-[#8b1515] text-white';
                                        $labelClass  = 'text-[#8b1515] font-bold';
                                    } elseif ($isPast) {
                                        $circleClass = 'bg-gray-700 border-gray-700 text-white';
                                        $labelClass  = 'text-gray-500 font-semibold';
                                    } else {
                                        $circleClass = 'bg-white border-gray-300 text-gray-400';
                                        $labelClass  = 'text-gray-400 font-medium';
                                    }
                                @endphp
                                <div class="flex-1 flex flex-col items-center relative">
                                    @if($i > 0)
                                    <div class="absolute top-4 right-1/2 w-full h-0.5 {{ $isPast || $isActive ? 'bg-gray-700' : 'bg-gray-200' }} -translate-y-1/2 z-0"></div>
                                    @endif
                                    <div class="relative z-10 w-8 h-8 rounded-full border-2 flex items-center justify-center {{ $circleClass }}">
                                        @if($isPast)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                        <span class="text-xs font-bold">{{ $i + 1 }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-xs text-center {{ $labelClass }}">{{ $step['label'] }}</p>
                                    <p class="text-[0.6rem] text-center text-gray-400 leading-tight mt-0.5">{{ $step['sub'] }}</p>
                                </div>
                                @endforeach
                            </div>

                            {{-- STEPPER EDIT (form terintegrasi) --}}
                            @if(!$isFinished)
                            <form x-show="editing" x-cloak method="POST" action="{{ route('admin.lamaran.update', $lamaran) }}">
                                @csrf
                                @method('PUT')

                                <div class="flex items-start">
                                    @php
                                        $editSteps = [
                                            ['key' => 'menunggu',       'val' => 'menunggu',       'label' => 'Menunggu',   'sub' => 'Administrasi',                  'order' => 1],
                                            ['key' => 'seleksi_tahap1', 'val' => 'seleksi_tahap1', 'label' => 'Tahap 1',    'sub' => 'Seleksi Administrasi',          'order' => 2],
                                            ['key' => 'seleksi_tahap2', 'val' => 'seleksi_tahap2', 'label' => 'Tahap 2',    'sub' => 'Micro Teaching & Wawancara',    'order' => 3],
                                            ['key' => 'final_d',        'val' => 'diterima',        'label' => 'Diterima',   'sub' => 'Keputusan Akhir',               'order' => 4],
                                            ['key' => 'final_t',        'val' => 'ditolak',         'label' => 'Ditolak',    'sub' => 'Keputusan Akhir',               'order' => 4],
                                        ];
                                    @endphp
                                    @foreach($editSteps as $j => $es)
                                    @php
                                        $isDisabled = false;
                                        // Rule: tidak bisa kembali ke status sebelumnya
                                        if ($es['order'] < $currentOrder) $isDisabled = true;
                                        // Rule 2: ke Tahap 2 → wajib sudah di Tahap 1, direkomendasikan Kaprodi, DAN sudah ada jadwal!
                                        if ($es['val'] === 'seleksi_tahap2' && (!$lamaran->is_direkomendasikan_kaprodi || $lamaran->status !== 'seleksi_tahap1' || !$hasJadwal)) $isDisabled = true;
                                        // Rule 3: ke diterima → wajib semua nilai sudah masuk
                                        if ($es['val'] === 'diterima' && !$hasBothScores) $isDisabled = true;
                                        $disabledTitle = match(true) {
                                            $es['val'] === 'seleksi_tahap2' && !$lamaran->is_direkomendasikan_kaprodi => 'Pelamar belum direkomendasikan Kaprodi',
                                            $es['val'] === 'seleksi_tahap2' && $lamaran->status !== 'seleksi_tahap1' => 'Pelamar harus berada di Seleksi Tahap 1 terlebih dahulu',
                                            $es['val'] === 'seleksi_tahap2' && !$hasJadwal => 'Jadwal belum dibuat. Status ini akan terupdate OTOMATIS saat jadwal dibuat.',
                                            $es['val'] === 'diterima' && !$hasBothScores => 'Penilaian belum lengkap',
                                            default => 'Syarat belum terpenuhi',
                                        };
                                    @endphp
                                    <div class="flex-1 flex flex-col items-center relative">
                                        @if($j > 0)
                                        <div class="absolute top-4 right-1/2 w-full h-0.5 bg-gray-200 -translate-y-1/2 z-0"></div>
                                        @endif
                                        <label class="relative z-10 {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                                               {{ $isDisabled ? 'title="' . $disabledTitle . '"' : '' }}>
                                            <input type="radio" name="status" value="{{ $es['val'] }}"
                                                   x-model="selected"
                                                   class="sr-only"
                                                   {{ $lamaran->status === $es['val'] ? 'checked' : '' }}
                                                   {{ $isDisabled ? 'disabled' : '' }}>
                                            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="selected === '{{ $es['val'] }}'
                                                     ? '{{ in_array($es['val'], ['diterima']) ? 'bg-green-600 border-green-600' : (in_array($es['val'], ['ditolak']) ? 'bg-red-600 border-red-600' : 'bg-[#8b1515] border-[#8b1515]') }} text-white'
                                                     : '{{ $isDisabled ? 'bg-gray-100 border-gray-200 text-gray-300' : 'bg-white border-gray-300 text-gray-400 hover:border-gray-500' }}'">
                                                <span class="text-xs font-bold">{{ $j + 1 }}</span>
                                            </div>
                                        </label>
                                        <p class="mt-2 text-xs text-center font-medium"
                                           :class="selected === '{{ $es['val'] }}' ? 'text-gray-800 font-bold' : '{{ $isDisabled ? 'text-gray-300' : 'text-gray-500' }}'">
                                            {{ $es['label'] }}
                                        </p>
                                        <p class="text-[0.6rem] text-center text-gray-400 leading-tight mt-0.5">{{ $es['sub'] }}</p>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Tombol simpan muncul di bawah stepper, hanya jika pilihan berubah --}}
                                <div class="flex justify-center gap-2 mt-4" x-show="selected !== '{{ $lamaran->status }}'">
                                    <button type="submit" class="px-4 py-1.5 bg-[#8b1515] hover:bg-red-900 text-white text-xs font-semibold rounded-lg transition-colors">
                                        Simpan
                                    </button>
                                    <button type="button" @click="editing = false; selected = '{{ $lamaran->status }}'" class="px-4 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                                        Batal
                                    </button>
                                </div>
                                <div class="flex justify-center mt-3" x-show="selected === '{{ $lamaran->status }}'">
                                    <button type="button" @click="editing = false" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                                        Tutup
                                    </button>
                                </div>
                            </form>
                            @else
                            <p class="text-xs text-gray-400 mt-4">Alur seleksi telah selesai.</p>
                            @endif

                        </div>
                    </div>
            </div>



        </div>
    </div>
</div>

@endsection

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('status-lamaran');
        if (el) {
            setTimeout(() => {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 150);
        }
    });
</script>
@endif

