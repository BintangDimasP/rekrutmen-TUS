<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara — {{ $lamaran->effective_pelamar->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            color: #000;
            background: #e5e5e5;
        }

        .page {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            background: #fff;
            padding: 12mm 20mm 0 20mm;
        }

        .content {
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* ── HEADER ── */
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header .logo-wrap {
            margin-bottom: 4px;
        }
        .header .logo-wrap img {
            width: 60px;
        }
        .header .univ-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1.2;
        }
        .header .univ-address {
            font-size: 8.5pt;
            margin-top: 2px;
            line-height: 1.3;
        }
        .header hr.header-line {
            border: none;
            border-top: 3px solid #000;
            margin: 5px 0 5px 0;
        }
        .header .judul-acara {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        .header .nomor-acara {
            font-size: 9pt;
            margin-top: 1px;
        }

        /* ── PEMBUKA ── */
        .intro {
            font-size: 10pt;
            line-height: 1.5;
            margin-bottom: 5px;
            text-align: justify;
        }
        .kandidat {
            margin-left: 32px;
            margin-bottom: 5px;
            line-height: 1.6;
            font-size: 10pt;
        }
        .kandidat-row { display: flex; }
        .kandidat-label { width: 110px; flex-shrink: 0; }
        .kandidat-sep   { width: 12px;  flex-shrink: 0; }
        .kandidat-value { font-weight: bold; }
        .dengan-nilai { margin-bottom: 4px; font-size: 10pt; }

        /* ── TABEL PENILAIAN ── */
        table.penilaian {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 5px;
        }
        table.penilaian th,
        table.penilaian td {
            border: 1px solid #000;
            padding: 2px 5px;
            vertical-align: middle;
        }
        table.penilaian thead th {
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
        }
        .section-header td {
            font-weight: bold;
            padding: 2px 5px;
            font-size: 9pt;
        }
        .sub-header td {
            background: #e6e6e6;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
        .rata-rata-cell { text-align: center; }
        .nilai-akhir-row td { font-weight: bold; background: #fff; }
        .col-no  { width: 24px; }
        .col-avg { width: 90px; }
        .col-ket { width: 90px; }

        /* ── REKOMENDASI ── */
        .rekomendasi {
            margin: 5px 0 4px 0;
            font-size: 10pt;
            line-height: 1.5;
        }
        .rek-header { margin-bottom: 2px; }
        .rekomendasi-item { margin-bottom: 1px; }
        .detail-block { margin-left: 18px; line-height: 1.5; font-size: 9.5pt; }
        .detail-item  { display: flex; }
        .detail-label { width: 150px; flex-shrink: 0; }
        .detail-sep   { width: 10px;  flex-shrink: 0; }

        .kalimat-penutup { margin: 5px 0 5px 0; font-size: 10pt; }

        /* ── TANDA TANGAN ── */
        .ttd-section { margin-bottom: 2px; }
        .ttd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 40px;
        }
        .ttd-block { text-align: center; font-size: 10pt; }
        .ttd-space { height: 45px; }
        .ttd-name {
            border-top: 1px solid #000;
            padding-top: 2px;
            display: inline-block;
            min-width: 100px;
        }
        .catatan-bawah {
            font-size: 8.5pt;
            margin-top: 2px;
            font-style: italic;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 1.5px solid #000;
            padding-top: 3px;
            margin-top: auto;
            font-size: 6.5pt;
            line-height: 1.35;
            color: #000;
        }
        .footer .footer-main { font-weight: bold; }
        .footer .footer-url {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            margin-top: 2px;
        }
        .footer-red-bar {
            height: 5px;
            background: linear-gradient(to right, #cc0000 60%, #ff4400 100%);
            margin-top: 4px;
        }

        @media print {
            body { background: #fff; }
            .page {
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

@php
    use Carbon\Carbon;

    $pelamar   = $lamaran->effective_pelamar;
    $lowongan  = $lamaran->lowongan;

    $microDinilai     = $micro->filter(fn($j) => $j->penilaian !== null)->values();
    $wawancaraDinilai = $wawancara->filter(fn($j) => $j->penilaian !== null)->values();

    $avgMicro     = $microDinilai->count() > 0
        ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;
    $avgWawancara = $wawancaraDinilai->count() > 0
        ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;

    $statusRekrutmenNilai = $wawancaraDinilai->first()?->penilaian?->status_rekrutmen ?? null;

    $jenjangTertinggi = collect([$pelamar->jenjang_3 ?? null, $pelamar->jenjang_2 ?? null, $pelamar->jenjang ?? null])
        ->filter()->map(fn($j) => strtolower(trim($j)))->first();
    $isS3 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's3') || str_contains($jenjangTertinggi, 'doktor'));
    $isS2 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's2') || str_contains($jenjangTertinggi, 'magister') || str_contains($jenjangTertinggi, 'master'));

    $sptSkor = 0; $sptLabel = '-';
    if ($isS3) {
        if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 5; $sptLabel = 'S3 Prof Full Time'; }
        elseif ($statusRekrutmenNilai === 'praktisi_part_time')  { $sptSkor = 4; $sptLabel = 'S3 Praktisi Part Time'; }
        elseif ($statusRekrutmenNilai === 'on_going')            { $sptSkor = 3; $sptLabel = 'S3 On Going'; }
        else { $sptSkor = 3; $sptLabel = 'S3'; }
    } elseif ($isS2) {
        if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 2; $sptLabel = 'S2 Prof Full Time'; }
        else { $sptSkor = 1; $sptLabel = 'S2 Praktisi Part Time'; }
    }

    $jfaSkorMap  = ['guru_besar' => 5, 'lektor_kepala' => 4, 'lektor' => 3, 'asisten_ahli' => 2, 'non_jabatan' => 1];
    $jfaLabelMap = ['guru_besar' => 'GB', 'lektor_kepala' => 'LK', 'lektor' => 'L', 'asisten_ahli' => 'AA', 'non_jabatan' => 'NJAD'];
    $jfaKey   = $pelamar->jabatan_akademik ?? 'non_jabatan';
    $jfaSkor  = $jfaSkorMap[$jfaKey] ?? 1;
    $jfaLabel = $jfaLabelMap[$jfaKey] ?? 'NJAD';

    $hIndex = (int)($pelamar->h_index ?? 0);
    if ($hIndex > 10) $hSkor = 5;
    elseif ($hIndex >= 5) $hSkor = 4;
    elseif ($hIndex >= 2) $hSkor = 3;
    elseif ($hIndex >= 1) $hSkor = 2;
    else $hSkor = 1;

    $avgKualifikasi = round(($sptSkor + $jfaSkor + $hSkor) / 3, 4);
    $hasilAkhir = null;
    if ($avgMicro !== null && $avgWawancara !== null) {
        $hasilAkhir = round(($avgKualifikasi * 0.40) + ($avgMicro * 0.20) + ($avgWawancara * 0.40), 2);
    }

    $rekAll = collect([...$microDinilai->pluck('penilaian'), ...$wawancaraDinilai->pluck('penilaian')])->pluck('rekomendasi')->filter();
    $direkomendasikan = $rekAll->contains('direkomendasikan');
    $tidakDirek       = $rekAll->contains('tidak_direkomendasikan');
    $rekomendasi      = $direkomendasikan && !$tidakDirek ? 'direkomendasikan' : 'tidak_direkomendasikan';

    $catatan = collect([...$microDinilai->pluck('penilaian'), ...$wawancaraDinilai->pluck('penilaian')])
        ->pluck('catatan')->filter()->implode(' ');

    $rekDetail = $wawancaraDinilai->first(fn($j) => $j->penilaian?->rekomendasi === 'direkomendasikan')?->penilaian
              ?? $wawancaraDinilai->first()?->penilaian;

    $prodiTujuan    = $rekDetail?->prodi_tujuan ?? '-';
    $statusRekLabel = ['on_going' => 'On Going', 'praktisi_part_time' => 'Praktisi Part Time', 'profesional_full_time' => 'Profesional Full Time'];
    $statusDisplay  = $statusRekLabel[$statusRekrutmenNilai] ?? '-';

    $kelompokMap = ['scout' => 'SCoT', 'ethes' => 'ETHES', 'riib' => 'RIIB'];
    $kelompokDisplay = ''; $bidangKeahlian = '';
    foreach ($microDinilai as $jm) {
        if (!$kelompokDisplay && $jm->penilaian?->kelompok_keahlian) $kelompokDisplay = $kelompokMap[$jm->penilaian->kelompok_keahlian] ?? $jm->penilaian->kelompok_keahlian;
        if (!$bidangKeahlian && $jm->penilaian?->bidang_keahlian) $bidangKeahlian = $jm->penilaian->bidang_keahlian;
    }

    $tanggalSeleksi = $micro->first()?->tanggal ?? $wawancara->first()?->tanggal ?? now();
    $hariMap = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
    $hariStr = $hariMap[$tanggalSeleksi->format('l')] ?? $tanggalSeleksi->format('l');

    $sesiInfo = $micro->first() ? (\App\Models\JadwalSeleksi::SESSIONS['micro_teaching'][$micro->first()->sesi] ?? null) : null;
    $jamStr = $sesiInfo ? $sesiInfo['start'] . ' WIB' : '-';

    $jenjangDisplay = $isS3 ? 'S3' : ($isS2 ? 'S2' : ($pelamar->jenjang ?? '-'));

    $semua = collect([...$micro->values(), ...$wawancara->values()])->map->penguji->filter()->unique('id')->values();
@endphp

<div class="no-print" style="position:fixed;top:16px;right:16px;z-index:999;display:flex;gap:8px;">
    <button onclick="window.print()" style="padding:8px 18px;background:#8b1515;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:bold;cursor:pointer;">🖨️ Cetak</button>
    <button onclick="window.close()" style="padding:8px 14px;background:#e5e7eb;color:#374151;border:none;border-radius:6px;font-size:13px;font-weight:bold;cursor:pointer;">✕ Tutup</button>
</div>

<div class="page">
  <div class="content">

    {{-- HEADER --}}
    <div class="header">
        <div class="logo-wrap">
            <img src="{{ asset('images/logo-telu.png') }}" alt="Logo Telkom University">
        </div>
        <div class="univ-name">TELKOM UNIVERSITY SURABAYA</div>
      
        <hr class="header-line">
        <div class="judul-acara">BERITA ACARA MICROTEACHING &amp; INTERVIEW</div>
        <div class="nomor-acara"> SDM03/TUKS/2026</div>
    </div>

    {{-- PEMBUKA --}}
    <p class="intro">
        Pada hari ini, <strong>{{ $hariStr }}</strong> tanggal <strong>{{ $tanggalSeleksi->format('d') }}</strong>
        bulan <strong>{{ $tanggalSeleksi->format('m') }}</strong> tahun <strong>{{ $tanggalSeleksi->format('Y') }}</strong>,
        pukul <strong>{{ $jamStr }}</strong> di Ruang Rapat, Universitas Telkom (Kampus Kota Surabaya)
        Jalan Ketintang No. 156, Ketintang, Gayungan, Kota Surabaya, telah dilaksanakan Microteaching &amp; Interview
        untuk calon dosen profesional :
    </p>

    <table style="margin-left:50px;margin-bottom:5px;font-size:10pt;line-height:1.7;" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:130px;">Nama</td>
            <td style="width:14px;">:</td>
            <td><strong>{{ $pelamar->nama }}</strong></td>
        </tr>
        <tr>
            <td>Bidang Keahlian</td>
            <td>:</td>
            <td>{{ $bidangKeahlian ?: ($pelamar->minat_riset ?? '-') }}</td>
        </tr>
    </table>

    <p class="dengan-nilai">Dengan nilai sebagai berikut :</p>

    {{-- TABEL PENILAIAN --}}
    <table class="penilaian">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th>Kategori Penilaian</th>
                <th class="col-avg">Rata-Rata</th>
                <th class="col-ket">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header"><td>A.</td><td colspan="3">Kualifikasi (40%)</td></tr>
            <tr class="sub-header"><td></td><td>Kualifikasi</td><td>Nilai</td><td>Rata-Rata A :</td></tr>
            <tr><td></td><td>Pendidikan : {{ $jenjangDisplay }}</td><td class="rata-rata-cell">{{ $sptSkor }}</td><td class="rata-rata-cell" rowspan="3" style="vertical-align:middle;">{{ $avgKualifikasi }}</td></tr>
            <tr><td></td><td>JFA : {{ $jfaLabel }}</td><td class="rata-rata-cell">{{ $jfaSkor }}</td></tr>
            <tr><td></td><td>H-Index : {{ $hIndex }}</td><td class="rata-rata-cell">{{ $hSkor }}</td></tr>

            <tr class="section-header"><td>B.</td><td colspan="3">Microteaching (20%)</td></tr>
            <tr class="sub-header"><td></td><td>Penguji</td><td>Nilai Rata Rata</td><td>Rata-Rata B :</td></tr>
            @if($microDinilai->count() > 0)
                @foreach($microDinilai->values() as $idx => $jm)
                <tr><td></td><td>Penguji {{ $idx+1 }} : {{ $jm->penguji->nama ?? '-' }}</td><td class="rata-rata-cell">{{ $jm->penilaian->total_nilai }}</td>@if($idx===0)<td class="rata-rata-cell" rowspan="{{ $microDinilai->count() }}" style="vertical-align:middle;">{{ $avgMicro }}</td>@endif</tr>
                @endforeach
            @else
            <tr><td></td><td colspan="2" style="text-align:center;font-style:italic;color:#555;">Belum dinilai</td><td class="rata-rata-cell">-</td></tr>
            @endif

            <tr class="section-header"><td>C.</td><td colspan="3">Wawancara (40%)</td></tr>
            <tr class="sub-header"><td></td><td>Penguji</td><td>Nilai Rata Rata</td><td>Rata-Rata C :</td></tr>
            @if($wawancaraDinilai->count() > 0)
                @foreach($wawancaraDinilai->values() as $idx => $jw)
                <tr><td></td><td>Penguji {{ $microDinilai->count()+$idx+1 }} : {{ $jw->penguji->nama ?? '-' }}</td><td class="rata-rata-cell">{{ $jw->penilaian->total_nilai }}</td>@if($idx===0)<td class="rata-rata-cell" rowspan="{{ $wawancaraDinilai->count() }}" style="vertical-align:middle;">{{ $avgWawancara }}</td>@endif</tr>
                @endforeach
            @else
            <tr><td></td><td colspan="2" style="text-align:center;font-style:italic;color:#555;">Belum dinilai</td><td class="rata-rata-cell">-</td></tr>
            @endif

            <tr class="section-header"><td>D.</td><td colspan="3">Catatan Penilai</td></tr>
            <tr><td></td><td colspan="3" style="line-height:1.5;text-align:justify;">{{ $catatan ?: '-' }}</td></tr>

            <tr class="nilai-akhir-row">
                <td colspan="2" style="text-align:center;vertical-align:middle;font-weight:bold;padding:5px;"><strong>Nilai Rata-Rata Akhir</strong></td>
                <td colspan="2" style="vertical-align:top;padding:5px;font-size:8.5pt;">
                    (Rata-Rata A x 40%) + (Rata-Rata B x 20%) + (Rata-Rata C x 40%)<br>
                    <strong style="font-size:11pt;">{{ $hasilAkhir ?? '-' }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- REKOMENDASI --}}
    <div class="rekomendasi">
        @php
            $isRekYa = $rekomendasi === 'direkomendasikan';
        @endphp
        <p class="rek-header">Rekomendasi akhir dinyatakan : <strong>*)&nbsp;&nbsp;{{ $isRekYa ? 'Direkomendasikan' : 'Tidak Direkomendasikan' }}</strong></p>
        <table style="margin-top:4px;font-size:9.5pt;line-height:1.7;width:100%;" cellspacing="0" cellpadding="0">
            {{-- OPSI A --}}
            <tr>
                <td style="width:18px;vertical-align:top;">a.</td>
                <td style="padding-left:8px;vertical-align:top;">
                    @if($isRekYa)
                        <strong>Direkomendasikan</strong>
                        <table style="margin-top:1px;" cellspacing="0" cellpadding="0">
                            <tr><td style="width:210px;">Prodi</td><td style="width:14px;">:</td><td>{{ $prodiTujuan }}</td></tr>
                            <tr><td>Status</td><td>:</td><td>{{ $statusDisplay }}</td></tr>
                            <tr><td>JFA yang diakui</td><td>:</td><td>{{ strtoupper($jfaLabel) }}</td></tr>
                            <tr><td>Pendidikan yang diakui</td><td>:</td><td>{{ $jenjangDisplay }}</td></tr>
                            <tr><td>Kelompok Keahlian</td><td>:</td><td>{{ $kelompokDisplay ?: '-' }}</td></tr>
                        </table>
                    @else
                        Direkomendasikan
                    @endif
                </td>
            </tr>
            {{-- OPSI B --}}
            <tr>
                <td style="width:18px;vertical-align:top;padding-top:3px;">b.</td>
                <td style="padding-left:8px;padding-top:3px;vertical-align:top;">
                    @if(!$isRekYa)
                        <strong>Tidak direkomendasikan</strong>
                        <table style="margin-top:1px;" cellspacing="0" cellpadding="0">
                            <tr><td style="width:210px;">Prodi</td><td style="width:14px;">:</td><td>{{ $prodiTujuan }}</td></tr>
                            <tr><td>Status</td><td>:</td><td>{{ $statusDisplay }}</td></tr>
                            <tr><td>JFA yang diakui</td><td>:</td><td>{{ strtoupper($jfaLabel) }}</td></tr>
                            <tr><td>Pendidikan yang diakui</td><td>:</td><td>{{ $jenjangDisplay }}</td></tr>
                            <tr><td>Kelompok Keahlian</td><td>:</td><td>{{ $kelompokDisplay ?: '-' }}</td></tr>
                        </table>
                    @else
                        Tidak direkomendasikan
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <p class="kalimat-penutup" style="margin-bottom: 26px;">Berita acara ini dibuat dengan sesungguhnya untuk dipergunakan sebagaimana mestinya.</p>

    {{-- TANDA TANGAN --}}
    <div class="ttd-section">
        <div class="ttd-grid">
            @foreach($semua as $idx => $pg)
            <div class="ttd-block">
                <div>PENILAI {{ $idx+1 }}</div>
                <div class="ttd-space"></div>
                <div><span class="ttd-name">({{ $pg->nama }})</span></div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="catatan-bawah">*) lingkari salah satu</div>

    {{-- FOOTER --}}
    <div class="footer">
        <p style="text-align:center;"><span class="footer-main">Main Campus</span> Bangkit Building Telkom University, Jl. Telekomunikasi, Terusan Buah Batu, Bandung 40257, West Java, Indonesia | t: (022) 7566456 | e: info@telkomuniversity.ac.id</p>
        <p style="text-align:center;"><span class="footer-main">Jakarta Campus</span> Jl. Daan Mogot KM. 11, Kedaung Kali Angke, Cengkareng, West Jakarta 11710, Indonesia | t: +62 81197800200 / (021) 545 1597 / 545 1697</p>
        <p style="text-align:center;"><span class="footer-main">Surabaya Campus</span> Jl. Ketintang No.156, Ketintang, Kec. Gayungan, Surabaya, East Java 60231 | t: (031) 8280800 / 081139808009</p>
        <div class="footer-url">www.telkomuniversity.ac.id</div>
        <div class="footer-red-bar"></div>
    </div>

  </div>
</div>

<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
