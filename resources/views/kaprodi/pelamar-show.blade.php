@extends('layouts.admin')

@section('title', 'Detail Pelamar')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('kaprodi.pelamar.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Daftar Pelamar</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $pelamar->nama }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
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

        <div class="p-6 md:p-8 space-y-8">
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

            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Dokumen Pendukung</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">CV (Resume)</p>@if($pelamar->file_cv)<a href="{{ asset('storage/' . $pelamar->file_cv) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Pas Foto</p>@if($pelamar->file_pas_foto)<a href="{{ asset('storage/' . $pelamar->file_pas_foto) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">KTP</p>@if($pelamar->file_ktp)<a href="{{ asset('storage/' . $pelamar->file_ktp) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">{{ $pelamar->kategori_sertifikat ?: 'Sertifikat' }}</p>@if($pelamar->file_sertifikat)<a href="{{ asset('storage/' . $pelamar->file_sertifikat) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Sertifikat Bahasa Inggris</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jenis Tes</p><p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Skor</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Sertifikat Bahasa</p>@if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/' . $pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-xs font-bold text-[#8b1515] hover:underline mt-1 inline-block">Preview</a>@else<p class="text-xs text-gray-400 mt-1">-</p>@endif</div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Data Akademik (Dosen)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">NIDN</p><p class="text-sm font-mono text-gray-700 mt-0.5">{{ $pelamar->nidn ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Homebase</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->homebase ?: '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">Jabatan Akademik</p><p class="text-sm text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik ? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik)) : '-' }}</p></div>
                    <div><p class="text-[0.6rem] font-black text-gray-400 uppercase">H-Index</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?: '-' }}</p></div>
                </div>
                <div class="mt-3"><p class="text-[0.6rem] font-black text-gray-400 uppercase">Minat Riset</p><p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?: '-' }}</p></div>
            </div>

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
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">Hasil Penilaian Seleksi</h3>
                @php
                    $microKategoriLabels = [1=>'PP', 2=>'PM', 3=>'Sis', 4=>'PKI', 5=>'SE'];
                    $microKategoriTooltips = [1=>'Perencanaan Pembelajaran', 2=>'Penguasaan Materi', 3=>'Sistematika', 4=>'Pengelolaan Kelas & Interaksi', 5=>'Sikap & Etika'];
                    $wawancaraIndikatorLabels = [1=>'Mot', 2=>'KMgj', 3=>'KMKur', 4=>'KPP', 5=>'KAbd', 6=>'KBT', 7=>'KL', 8=>'KW'];
                    $wawancaraIndikatorTooltips = [1=>'Motivasi', 2=>'Kemampuan Mengajar', 3=>'Kemampuan Mengembangkan Kurikulum', 4=>'Kemampuan Penelitian & Publikasi', 5=>'Kemampuan Abdimas', 6=>'Kemampuan Bekerjasama dengan Tim', 7=>'Keahlian Lainnya', 8=>'Komitmen Waktu'];
                    $rekLabels = ['direkomendasikan'=>['label'=>'Direkomendasikan','color'=>'bg-green-50 text-green-700'], 'tidak_direkomendasikan'=>['label'=>'Tidak Direkomendasikan','color'=>'bg-red-50 text-red-700'], 'perlu_dipertimbangkan'=>['label'=>'Perlu Dipertimbangkan','color'=>'bg-yellow-50 text-yellow-700']];
                    $kkLabels = ['scout'=>'SCoT','ethes'=>'ETHES','riib'=>'RIIB'];
                    $hasAnySchedule = false;
                @endphp

                @foreach($pelamar->lamarans as $lamaran)
                @php
                    $micro     = $jadwalPerLamaran[$lamaran->id]['micro'];
                    $wawancara = $jadwalPerLamaran[$lamaran->id]['wawancara'];
                    $microDinilai     = $micro->filter(fn($j) => $j->penilaian !== null);
                    $wawancaraDinilai = $wawancara->filter(fn($j) => $j->penilaian !== null);
                    $nilaiAkhirMicro     = $microDinilai->count() > 0 ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;
                    $nilaiAkhirWawancara = $wawancaraDinilai->count() > 0 ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;

                    // SPT + Kualifikasi (identik dengan admin)
                    $statusRekrutmenNilai = $wawancaraDinilai->first()?->penilaian?->status_rekrutmen ?? null;
                    $jenjangTertinggi = collect([$pelamar->jenjang_3, $pelamar->jenjang_2, $pelamar->jenjang])->filter()->map(fn($j) => strtolower(trim($j)))->first();
                    $isS3 = $jenjangTertinggi && (str_contains($jenjangTertinggi,'s3') || str_contains($jenjangTertinggi,'doktor'));
                    $isS2 = $jenjangTertinggi && (str_contains($jenjangTertinggi,'s2') || str_contains($jenjangTertinggi,'magister') || str_contains($jenjangTertinggi,'master'));
                    $sptSkor = 0; $sptLabel = '-'; $sptPending = false;
                    if ($isS3) {
                        if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 5; $sptLabel = 'S3 Prof Full Time'; }
                        elseif ($statusRekrutmenNilai === 'praktisi_part_time') { $sptSkor = 4; $sptLabel = 'S3 Praktisi Part Time'; }
                        elseif ($statusRekrutmenNilai === 'on_going') { $sptSkor = 3; $sptLabel = 'S3 On Going'; }
                        else { $sptPending = true; $sptLabel = 'S3 (belum dinilai)'; }
                    } elseif ($isS2) {
                        if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 2; $sptLabel = 'S2 Prof Full Time'; }
                        elseif ($statusRekrutmenNilai === 'praktisi_part_time') { $sptSkor = 1; $sptLabel = 'S2 Praktisi Part Time'; }
                        else { $sptPending = true; $sptLabel = 'S2 (belum dinilai)'; }
                    } else { $sptLabel = $jenjangTertinggi ? strtoupper($jenjangTertinggi) : '-'; }
                    $jfaSkorMap = ['guru_besar'=>5,'lektor_kepala'=>4,'lektor'=>3,'asisten_ahli'=>2,'non_jabatan'=>1];
                    $jfaLabelMap = ['guru_besar'=>'Guru Besar (GB)','lektor_kepala'=>'Lektor Kepala (LK)','lektor'=>'Lektor (L)','asisten_ahli'=>'Asisten Ahli (AA)','non_jabatan'=>'Non Jabatan (NJAD)'];
                    $jfaKey = $pelamar->jabatan_akademik ?? 'non_jabatan';
                    $jfaSkor = $jfaSkorMap[$jfaKey] ?? 1;
                    $jfaLabel = $jfaLabelMap[$jfaKey] ?? 'Non Jabatan (NJAD)';
                    $hIndex = (int)($pelamar->h_index ?? 0);
                    if ($hIndex > 10) $hSkor = 5;
                    elseif ($hIndex >= 5) $hSkor = 4;
                    elseif ($hIndex >= 2) $hSkor = 3;
                    elseif ($hIndex >= 1) $hSkor = 2;
                    else $hSkor = 1;
                    $avgKualifikasi = $sptPending ? null : round(($sptSkor + $jfaSkor + $hSkor) / 3, 2);
                    $hasilAkhir = null;
                    if ($nilaiAkhirMicro !== null && $nilaiAkhirWawancara !== null && $avgKualifikasi !== null) {
                        $hasilAkhir = round(($nilaiAkhirMicro * 0.20) + ($nilaiAkhirWawancara * 0.40) + ($avgKualifikasi * 0.40), 2);
                    }
                    if ($micro->count() > 0 || $wawancara->count() > 0) $hasAnySchedule = true;
                @endphp

                @if($micro->count() > 0 || $wawancara->count() > 0)
                <div class="mb-8">
                    <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-wider mb-4">Posisi: <span class="text-[#8b1515]">{{ $lamaran->lowongan?->nama_posisi ?? '-' }}</span></p>
                    <div class="space-y-6">

                        {{-- ── MICRO TEACHING ── --}}
                        @if($micro->count() > 0)
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
                                <table class="w-full text-sm border border-gray-200 border-collapse">
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
                                            <th class="px-3 py-2 text-left font-semibold border border-gray-200">Bidang</th>
                                            <th class="px-3 py-2 text-left font-semibold border border-gray-200">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($microDinilai->values() as $jadwalMicro)
                                        @php $p = $jadwalMicro->penilaian; $rek = $p->rekomendasi ? ($rekLabels[$p->rekomendasi] ?? ['label'=>$p->rekomendasi,'color'=>'bg-gray-50 text-gray-700']) : null; @endphp
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-4 py-2.5 text-xs font-mono text-gray-600 whitespace-nowrap border border-gray-200" title="{{ $jadwalMicro->penguji->nama ?? '' }}">{{ $jadwalMicro->penguji->kode ?? '-' }}</td>
                                            @foreach($microKategoriLabels as $kNum => $kShort)
                                            <td class="px-3 py-2.5 text-center font-bold text-gray-800 border border-gray-200">{{ $p->{'kategori_'.$kNum} ?? '-' }}</td>
                                            @endforeach
                                            <td class="px-3 py-2.5 text-center border border-gray-200"><span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-xs font-bold rounded">{{ $p->total_nilai }}</span></td>
                                            <td class="px-3 py-2.5 text-center border border-gray-200">@if($rek)<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $rek['color'] }}">{{ $rek['label'] }}</span>@else<span class="text-gray-400">-</span>@endif</td>
                                            <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->prodi_tujuan ?: '-' }}</td>
                                            <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->kelompok_keahlian ? ($kkLabels[$p->kelompok_keahlian] ?? $p->kelompok_keahlian) : '-' }}</td>
                                            <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->bidang_keahlian ?: '-' }}</td>
                                            <td class="px-3 py-2.5 text-xs text-gray-600 max-w-xs border border-gray-200">{{ $p->catatan ?: '-' }}</td>
                                        </tr>
                                        @endforeach
                                        @foreach($micro->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                                        <tr class="bg-yellow-50/40">
                                            <td class="px-4 py-2.5 text-xs font-mono text-gray-500 border border-gray-200" title="{{ $jadwalBelum->penguji->nama ?? '' }}">{{ $jadwalBelum->penguji->kode ?? '-' }}</td>
                                            <td colspan="{{ count($microKategoriLabels) + 7 }}" class="px-3 py-2.5 text-xs text-yellow-600 font-semibold border border-gray-200">Belum menilai</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @if($nilaiAkhirMicro !== null)
                                    <tfoot>
                                        <tr class="bg-gray-100 border-t border-gray-200">
                                            <td class="px-4 py-2.5 text-xs text-center font-bold text-gray-600 uppercase" colspan="{{ count($microKategoriLabels) + 1 }}">Nilai Akhir</td>
                                            <td class="px-3 py-2.5 text-center border border-gray-200"><span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-sm font-black rounded">{{ $nilaiAkhirMicro }}</span></td>
                                            <td colspan="6"></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                            @endif
                            </div>
                        </div>
                        @endif

                        {{-- ── WAWANCARA ── --}}
                        @if($wawancara->count() > 0)
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
                                <table class="w-full text-sm border border-gray-200 border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-xs text-gray-500 border-b border-gray-200">
                                            <th class="px-4 py-2 text-left font-semibold border border-gray-200">Penguji</th>
                                            @foreach($wawancaraIndikatorLabels as $iNum => $iShort)
                                            <th class="px-3 py-2 text-center font-semibold border border-gray-200" title="{{ $wawancaraIndikatorTooltips[$iNum] }}">{{ $iShort }}</th>
                                            @endforeach
                                            <th class="px-3 py-2 text-center font-semibold border border-gray-200">Avg</th>
                                            <th class="px-3 py-2 text-center font-semibold border border-gray-200">Status</th>
                                            <th class="px-3 py-2 text-left font-semibold border border-gray-200">Prodi</th>
                                            <th class="px-3 py-2 text-left font-semibold border border-gray-200">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($wawancaraDinilai->values() as $jadwalWaw)
                                        @php $p = $jadwalWaw->penilaian; $detail = $p->detail_nilai ?? []; $rek = $p->rekomendasi ? ($rekLabels[$p->rekomendasi] ?? ['label'=>$p->rekomendasi,'color'=>'bg-gray-50 text-gray-700']) : null; @endphp
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-4 py-2.5 text-xs font-mono text-gray-600 whitespace-nowrap border border-gray-200" title="{{ $jadwalWaw->penguji->nama ?? '' }}">{{ $jadwalWaw->penguji->kode ?? '-' }}</td>
                                            @foreach($wawancaraIndikatorLabels as $iNum => $iShort)
                                            <td class="px-3 py-2.5 text-center font-bold text-gray-800 border border-gray-200">{{ $detail['k1_item_'.$iNum] ?? '-' }}</td>
                                            @endforeach
                                            <td class="px-3 py-2.5 text-center border border-gray-200"><span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-xs font-bold rounded">{{ $p->total_nilai }}</span></td>
                                            <td class="px-3 py-2.5 text-center border border-gray-200">@if($rek)<span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $rek['color'] }}">{{ $rek['label'] }}</span>@else<span class="text-gray-400">-</span>@endif</td>
                                            <td class="px-3 py-2.5 text-xs text-gray-700 border border-gray-200">{{ $p->prodi_tujuan ?: '-' }}</td>
                                            <td class="px-3 py-2.5 text-xs text-gray-600 max-w-xs border border-gray-200">{{ $p->catatan ?: '-' }}</td>
                                        </tr>
                                        @endforeach
                                        @foreach($wawancara->filter(fn($j) => $j->penilaian === null) as $jadwalBelum)
                                        <tr class="bg-yellow-50/40">
                                            <td class="px-4 py-2.5 text-xs font-mono text-gray-500 border border-gray-200" title="{{ $jadwalBelum->penguji->nama ?? '' }}">{{ $jadwalBelum->penguji->kode ?? '-' }}</td>
                                            <td colspan="{{ count($wawancaraIndikatorLabels) + 4 }}" class="px-3 py-2.5 text-xs text-yellow-600 font-semibold border border-gray-200">Belum menilai</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    @if($nilaiAkhirWawancara !== null)
                                    <tfoot>
                                        <tr class="bg-gray-100 border-t border-gray-200">
                                            <td class="px-4 py-2.5 text-xs text-center font-bold text-gray-600 uppercase border border-gray-200" colspan="{{ count($wawancaraIndikatorLabels) + 1 }}">Nilai Akhir</td>
                                            <td class="px-3 py-2.5 text-center border border-gray-200"><span class="inline-block px-2 py-0.5 bg-gray-800 text-white text-sm font-black rounded">{{ $nilaiAkhirWawancara }}</span></td>
                                            <td colspan="3" class="border border-gray-200"></td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                            @endif
                            </div>
                        </div>
                        @endif

                        {{-- ── KUALIFIKASI & HASIL AKHIR ── --}}
                        @if($nilaiAkhirWawancara !== null || $avgKualifikasi !== null)
                        <div class="mt-4">
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Kualifikasi &amp; Hasil Akhir</h4>
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-xs text-gray-500">
                                            <th class="px-4 py-2 text-center font-semibold border border-gray-200">SPT</th>
                                            <th class="px-4 py-2 text-center font-semibold border border-gray-200">JFA</th>
                                            <th class="px-4 py-2 text-center font-semibold border border-gray-200">H-Index</th>
                                            <th class="px-4 py-2 text-center font-semibold border border-gray-200">Avg Kualifikasi</th>
                                            <th class="px-4 py-2 text-center font-semibold border border-gray-200">AVG Micro</th>
                                            <th class="px-4 py-2 text-center font-semibold border border-gray-200">AVG WWC</th>
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
                                    <p class="text-[0.65rem] text-gray-400">(Micro×20%) + (WWC×40%) + (Kualifikasi×40%)</p>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                @endif
                @endforeach

                @if(!$hasAnySchedule)
                <div class="p-8 rounded-2xl border border-gray-200 bg-gray-50 text-center">
                    <p class="text-sm font-bold text-gray-600">Belum Ada Jadwal</p>
                    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Jadwal seleksi belum ditentukan untuk pelamar ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
