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

                        {{-- 1. DETAIL PELAMAR LAINNYA (RIWAYAT PENDIDIKAN, DOKUMEN, DLL) --}}
            <div x-data="{ expanded: false }" class="pt-6 border-t border-gray-100">
                <button @click="expanded = !expanded" class="flex items-center justify-between w-full p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                    <span class="text-sm font-bold text-gray-800">Lihat Detail Profil Pelamar Lainnya</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="expanded" x-collapse class="mt-6 space-y-8">

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
                            <div>
                                <p class="text-[0.6rem] font-black text-gray-400 uppercase">Surat Lamaran</p>
                                @if($lamaran->file_surat_lamaran)
                                    <a href="{{ asset('storage/' . $lamaran->file_surat_lamaran) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>
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

            {{-- 2. JADWAL & SUMMARY PENILAIAN SELEKSI --}}
            @php
                $microDinilai     = $micro->filter(fn($j) => $j->penilaian !== null);
                $wawancaraDinilai = $wawancara->filter(fn($j) => $j->penilaian !== null);

                $microKategoriLabels = [
                    1 => 'PP',
                    2 => 'PM',
                    3 => 'Sis',
                    4 => 'PKI',
                    5 => 'SE',
                ];
                $microKategoriTooltips = [
                    1 => 'Perencanaan Pembelajaran',
                    2 => 'Penguasaan Materi',
                    3 => 'Sistematika',
                    4 => 'Pengelolaan Kelas & Interaksi',
                    5 => 'Sikap & Etika',
                ];
                // Wawancara: 8 indikator flat
                $wawancaraIndikatorLabels = [
                    1 => 'Mot',
                    2 => 'KMgj',
                    3 => 'KMKur',
                    4 => 'KPP',
                    5 => 'KAbd',
                    6 => 'KBT',
                    7 => 'KL',
                    8 => 'KW',
                ];
                $wawancaraIndikatorTooltips = [
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

            <div x-data="{ expandedJadwal: true }" class="pt-6 border-t border-gray-100">
                <button @click="expandedJadwal = !expandedJadwal" class="flex items-center justify-between w-full p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                    <span class="text-sm font-bold text-gray-800">Summary Penilaian</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{'rotate-180': expandedJadwal}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="expandedJadwal" x-collapse class="mt-6">
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Jadwal &amp; Penilaian Seleksi
                        </h3>

                @if(($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0))
                <div class="space-y-6">

                    {{-- ── MICRO TEACHING ── --}}
                    @if($micro && $micro->count() > 0)
                    <div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">
                            Micro Teaching
                            <span class="text-gray-500 font-normal">{{ $micro[0]->tanggal->format('d M Y') }} • {{ $micro[0]->session_label }}</span>
                            
                        </h4>
                        <div class="border border-gray-200 rounded-xl overflow-hidden">

                        @if($microDinilai->count() === 0)
                        <div class="px-4 py-3 bg-yellow-50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-yellow-700">Menunggu penilaian &mdash; Penguji: {{ $micro->pluck('penguji.nama')->filter()->implode(', ') }} (0/{{ $micro->count() }} sudah menilai)</p>
                        </div>
                        @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
                                        <th class="px-4 py-2 text-left font-semibold">Penguji</th>
                                        @foreach($microKategoriLabels as $kNum => $kShort)
                                        <th class="px-3 py-2 text-center font-semibold" title="{{ $microKategoriTooltips[$kNum] }}">{{ $kShort }}</th>
                                        @endforeach
                                        <th class="px-3 py-2 text-center font-semibold">Avg</th>
                                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                                        <th class="px-3 py-2 text-left font-semibold">Prodi</th>
                                        <th class="px-3 py-2 text-left font-semibold">Kelompok</th>
                                        <th class="px-3 py-2 text-left font-semibold">Bidang</th>
                                        <th class="px-3 py-2 text-left font-semibold">Catatan</th>
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
                                        <td class="px-4 py-2.5 text-xs font-mono text-gray-600 whitespace-nowrap" title="{{ $jadwalMicro->penguji->nama ?? '' }}">{{ $jadwalMicro->penguji->kode ?? '-' }}</td>
                                        @foreach($microKategoriLabels as $kNum => $kShort)
                                        <td class="px-3 py-2.5 text-center font-bold text-gray-800">{{ $p->{'kategori_'.$kNum} ?? '-' }}</td>
                                        @endforeach
                                        <td class="px-3 py-2.5 text-center">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-xs font-bold rounded">{{ $p->total_nilai }}</span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($rek)
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700">{{ $p->prodi_tujuan ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700">{{ $p->kelompok_keahlian ? ($kkLabels[$p->kelompok_keahlian] ?? $p->kelompok_keahlian) : '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700">{{ $p->bidang_keahlian ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-600 max-w-xs">{{ $p->catatan ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @foreach($micro->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                                    <tr class="bg-yellow-50/40">
                                        <td class="px-4 py-2.5 text-xs font-mono text-gray-500" title="{{ $jadwalBelum->penguji->nama ?? '' }}">{{ $jadwalBelum->penguji->kode ?? '-' }}</td>
                                        <td colspan="{{ count($microKategoriLabels) + 7 }}" class="px-3 py-2.5 text-xs text-yellow-600 font-semibold">Belum menilai</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($nilaiAkhirMicro !== null)
                                <tfoot>
                                    <tr class="bg-gray-100 border-t border-gray-200">
                                        <td class="px-4 py-2.5 text-xs font-bold text-gray-600 uppercase" colspan="{{ count($microKategoriLabels) + 1 }}">Nilai Akhir <span class="font-normal text-gray-400">(rata-rata {{ $microDinilai->count() }} penguji)</span></td>
                                        <td class="px-3 py-2.5 text-center">
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

                    {{-- ── WAWANCARA ── --}}
                    @if($wawancara && $wawancara->count() > 0)
                    <div class="mt-4">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">
                            Wawancara
                            <span class="text-gray-500 font-normal">{{ $wawancara[0]->tanggal->format('d M Y') }} • {{ $wawancara[0]->session_label }}</span>
                            
                        </h4>
                        <div class="border border-gray-200 rounded-xl overflow-hidden">

                        @if($wawancaraDinilai->count() === 0)
                        <div class="px-4 py-3 bg-yellow-50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-yellow-700">Menunggu penilaian &mdash; Penguji: {{ $wawancara->pluck('penguji.nama')->filter()->implode(', ') }} (0/{{ $wawancara->count() }} sudah menilai)</p>
                        </div>
                        @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
                                        <th class="px-4 py-2 text-left font-semibold">Penguji</th>
                                        @foreach($wawancaraIndikatorLabels as $iNum => $iShort)
                                        <th class="px-3 py-2 text-center font-semibold" title="{{ $wawancaraIndikatorTooltips[$iNum] }}">{{ $iShort }}</th>
                                        @endforeach
                                        <th class="px-3 py-2 text-center font-semibold">Avg</th>
                                        <th class="px-3 py-2 text-center font-semibold">Status</th>
                                        <th class="px-3 py-2 text-left font-semibold">Prodi</th>
                                        <th class="px-3 py-2 text-left font-semibold">Catatan</th>
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
                                        <td class="px-4 py-2.5 text-xs font-mono text-gray-600 whitespace-nowrap" title="{{ $jadwalWaw->penguji->nama ?? '' }}">{{ $jadwalWaw->penguji->kode ?? '-' }}</td>
                                        @foreach($wawancaraIndikatorLabels as $iNum => $iShort)
                                        <td class="px-3 py-2.5 text-center font-bold text-gray-800">{{ $detail['k1_item_'.$iNum] ?? '-' }}</td>
                                        @endforeach
                                        <td class="px-3 py-2.5 text-center">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-xs font-bold rounded">{{ $p->total_nilai }}</span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($rek)
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $rek['color'] }}">{{ $rek['label'] }}</span>
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-gray-700">{{ $p->prodi_tujuan ?: '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-gray-600 max-w-xs">{{ $p->catatan ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @foreach($wawancara->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                                    <tr class="bg-yellow-50/40">
                                        <td class="px-4 py-2.5 text-xs font-mono text-gray-500" title="{{ $jadwalBelum->penguji->nama ?? '' }}">{{ $jadwalBelum->penguji->kode ?? '-' }}</td>
                                        <td colspan="{{ count($wawancaraIndikatorLabels) + 4 }}" class="px-3 py-2.5 text-xs text-yellow-600 font-semibold">Belum menilai</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($nilaiAkhirWawancara !== null)
                                <tfoot>
                                    <tr class="bg-gray-100 border-t border-gray-200">
                                        <td class="px-4 py-2.5 text-xs font-bold text-gray-600 uppercase" colspan="{{ count($wawancaraIndikatorLabels) + 1 }}">Nilai Akhir <span class="font-normal text-gray-400">(rata-rata {{ $wawancaraDinilai->count() }} penguji)</span></td>
                                        <td class="px-3 py-2.5 text-center">
                                            <span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-sm font-black rounded">{{ $nilaiAkhirWawancara }}</span>
                                        </td>
                                        <td colspan="3"></td>
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

                    {{-- STATUS LAMARAN --}}
                    <div class="pt-6 border-t border-gray-100">
                        @php
                            $isFinished = in_array($lamaran->status, ['diterima', 'ditolak']);
                            $hasJadwal = ($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0);
                            $hasBothScores = ($wawancara && $wawancara->count() > 0 && $wawancara->every(fn($j) => $j->penilaian !== null)) && ($micro && $micro->count() > 0 && $micro->every(fn($j) => $j->penilaian !== null));
                            $statusOrder = ['menunggu' => 1, 'seleksi_tahap1' => 2, 'seleksi_tahap2' => 3, 'diterima' => 4, 'ditolak' => 4];
                            $currentOrder = $statusOrder[$lamaran->status] ?? 1;

                            // Stepper steps — diterima & ditolak adalah cabang di step 4
                            $steps = [
                                ['key' => 'menunggu',       'label' => 'Menunggu',      'sub' => 'Administrasi'],
                                ['key' => 'seleksi_tahap1', 'label' => 'Tahap 1',       'sub' => 'Seleksi Administrasi'],
                                ['key' => 'seleksi_tahap2', 'label' => 'Tahap 2',       'sub' => 'Micro Teaching & Wawancara'],
                                ['key' => 'final',          'label' => 'Keputusan',     'sub' => $lamaran->status === 'diterima' ? 'Diterima' : ($lamaran->status === 'ditolak' ? 'Ditolak' : 'Diterima / Ditolak')],
                            ];
                        @endphp

                        <div x-data="{ editing: false }">
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Status Lamaran</h3>
                                @if(!$isFinished)
                                <button @click="editing = !editing"
                                    class="p-1.5 rounded-lg transition-colors"
                                    :class="editing ? 'bg-gray-100 text-gray-600' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'"
                                    title="Ubah Status">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                @endif
                            </div>

                            {{-- STEPPER --}}
                            <div class="flex items-start gap-0 mb-6">
                                @foreach($steps as $i => $step)
                                @php
                                    $stepOrder = $i + 1;
                                    // step 4 = final (diterima/ditolak)
                                    if ($step['key'] === 'final') {
                                        $isDone    = in_array($lamaran->status, ['diterima', 'ditolak']);
                                        $isActive  = $isDone;
                                        $isPast    = false;
                                    } else {
                                        $keyOrder  = $statusOrder[$step['key']] ?? 1;
                                        $isDone    = $currentOrder > $keyOrder;
                                        $isActive  = $lamaran->status === $step['key'];
                                        $isPast    = $isDone;
                                    }

                                    if ($isActive) {
                                        $circleClass = 'bg-[#8b1515] border-[#8b1515] text-white';
                                        $labelClass  = 'text-[#8b1515] font-bold';
                                    } elseif ($isPast) {
                                        $circleClass = 'bg-gray-700 border-gray-700 text-white';
                                        $labelClass  = 'text-gray-500 font-semibold';
                                    } else {
                                        $circleClass = 'bg-white border-gray-300 text-gray-400';
                                        $labelClass  = 'text-gray-400 font-medium';
                                    }

                                    // Final step: warna sesuai hasil
                                    if ($step['key'] === 'final' && $isActive) {
                                        if ($lamaran->status === 'diterima') {
                                            $circleClass = 'bg-green-600 border-green-600 text-white';
                                            $labelClass  = 'text-green-700 font-bold';
                                        } elseif ($lamaran->status === 'ditolak') {
                                            $circleClass = 'bg-red-600 border-red-600 text-white';
                                            $labelClass  = 'text-red-700 font-bold';
                                        }
                                    }
                                @endphp
                                <div class="flex-1 flex flex-col items-center relative">
                                    {{-- Connector line kiri --}}
                                    @if($i > 0)
                                    <div class="absolute top-4 right-1/2 w-full h-0.5 {{ $isPast || $isActive ? 'bg-gray-700' : 'bg-gray-200' }} -translate-y-1/2 z-0"></div>
                                    @endif
                                    {{-- Circle --}}
                                    <div class="relative z-10 w-8 h-8 rounded-full border-2 flex items-center justify-center {{ $circleClass }} transition-all">
                                        @if($isPast)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                        <span class="text-xs font-bold">{{ $i + 1 }}</span>
                                        @endif
                                    </div>
                                    {{-- Label --}}
                                    <p class="mt-2 text-xs text-center {{ $labelClass }}">{{ $step['label'] }}</p>
                                    <p class="text-[0.6rem] text-center text-gray-400 leading-tight mt-0.5">{{ $step['sub'] }}</p>
                                </div>
                                @endforeach
                            </div>

                            {{-- EDITABLE FORM --}}
                            @if(!$isFinished)
                            <form x-show="editing" method="POST" action="{{ route('admin.lamaran.update', $lamaran) }}" x-cloak class="border-t border-gray-100 pt-4 text-center">
                                @csrf
                                @method('PUT')
                                <p class="text-xs text-gray-500 mb-3">Pilih status baru:</p>
                                <div class="flex flex-wrap justify-center gap-2 mb-4">
                                    @foreach(['menunggu' => 'Menunggu', 'seleksi_tahap1' => 'Tahap 1', 'seleksi_tahap2' => 'Tahap 2', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                                    @php
                                        $isDisabled = false;
                                        $targetOrder = $statusOrder[$val];
                                        if ($targetOrder < $currentOrder) $isDisabled = true;
                                        if ($val === 'seleksi_tahap2' && !$hasJadwal && $currentOrder < 3) $isDisabled = true;
                                        if (($val === 'diterima' || $val === 'ditolak') && !$hasBothScores) $isDisabled = true;
                                    @endphp
                                    <label class="{{ $isDisabled ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}" {{ $isDisabled ? 'title="Syarat belum terpenuhi atau tidak bisa kembali"' : '' }}>
                                        <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                                               {{ $lamaran->status === $val ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all select-none
                                            peer-checked:bg-gray-800 peer-checked:text-white peer-checked:border-gray-800
                                            bg-white text-gray-600 border-gray-200 hover:border-gray-400">
                                            {{ $label }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                                <button type="submit" class="px-4 py-1.5 bg-[#8b1515] hover:bg-red-900 text-white text-xs font-semibold rounded-lg transition-colors">
                                    Simpan
                                </button>
                                <button type="button" @click="editing = false" class="ml-2 px-4 py-1.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-semibold rounded-lg transition-colors">
                                    Batal
                                </button>
                            </form>
                            @else
                            <p class="text-xs text-gray-400 border-t border-gray-100 pt-3">Alur seleksi telah selesai.</p>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>

@endsection
