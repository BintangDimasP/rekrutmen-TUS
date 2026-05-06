<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Berita Acara</title>
    <style>
        * { margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
        }

        /* ==== PAGE ==== */
        .page {
            padding: 18mm 20mm 18mm 25mm;
        }

        /* ==== KOP ==== */
        .kop-wrapper {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .kop-logo {
            height: 70px;
            width: auto;
            margin-bottom: 6px;
        }

        .kop-logo-placeholder {
            display: inline-block;
            width: 70px;
            height: 70px;
            background: #8b1515;
            text-align: center;
            line-height: 70px;
            font-size: 26pt;
            font-weight: bold;
            color: #fff;
            margin-bottom: 6px;
        }

        .kop-nama {
            font-size: 14.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
        }

        .kop-alamat {
            font-size: 9pt;
            color: #333;
            line-height: 1.5;
            margin-top: 3px;
        }

        /* ==== JUDUL ==== */
        .judul-wrapper {
            text-align: center;
            margin: 16px 0 6px 0;
        }

        .judul-wrapper h2 {
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .judul-nomor {
            font-size: 10pt;
            color: #333;
            margin-top: 4px;
        }

        /* ==== BODY ==== */
        .body-p {
            font-size: 12pt;
            line-height: 1.9;
            text-align: justify;
            margin-top: 20px;
        }

        .body-p + .body-p {
            margin-top: 12px;
        }

        /* ==== TABEL KANDIDAT ==== */
        .t-kandidat {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 11pt;
        }

        .t-kandidat th {
            border: 1px solid #aaa;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .t-kandidat td {
            border: 1px solid #aaa;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .td-no     { text-align: center; width: 32px; }
        .td-status { text-align: center; width: 105px; font-weight: bold; }
        .td-empty  { height: 22px; }

        /* ==== PENUTUP ==== */
        .penutup {
            font-size: 12pt;
            line-height: 1.9;
            text-align: justify;
            margin-top: 16px;
        }

        /* ==== TTD ==== */
        .t-ttd {
            width: 100%;
            margin-top: 36px;
            font-size: 11.5pt;
        }

        .t-ttd td {
            vertical-align: top;
            width: 50%;
            line-height: 1.7;
        }

        .t-ttd .right {
            text-align: right;
        }

        .ttd-line {
            display: inline-block;
            border-top: 1px solid #000;
            min-width: 175px;
            text-align: center;
            padding-top: 3px;
            font-size: 10.5pt;
            margin-top: 58px;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ===== KOP ===== --}}
    @php $logoPath = public_path('images/logo-telu.png'); @endphp
    <div class="kop-wrapper">
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="Logo" class="kop-logo">
        @else
            <span class="kop-logo-placeholder">T</span>
        @endif
        <div class="kop-nama">Telkom University Surabaya</div>
        <div class="kop-alamat">
            JL. Ketintang No.156, Gayungan, Surabaya, Jawa Timur 60231<br>
           
        </div>
    </div>

    {{-- ===== JUDUL ===== --}}
    <div class="judul-wrapper">
        <h2>Berita Acara Hasil Seleksi Rekrutmen</h2>
        <div class="judul-nomor">Nomor: &nbsp;..., SK-REKTUS/....{{ now()->year }}</div>
    </div>

    {{-- ===== PARAGRAF 1 ===== --}}
    <p class="body-p">
        Pada hari ini, {{ $hari }} tanggal {{ $tanggalFormatted }},
        bertempat di Sekretariat Telkom University Surabaya, Panitia Seleksi telah melakukan
        evaluasi akhir terhadap seluruh kandidat yang mengikuti proses rekrutmen.
    </p>

    {{-- ===== PARAGRAF 2 ===== --}}
    <p class="body-p">
        Berikut adalah daftar nama kandidat yang telah mengikuti serangkaian tahapan seleksi
        secara keseluruhan pada lowongan {{ $lowongan->nama_posisi }} oleh
       Prodi {{ $lowongan->prodi?->nama ?? '-' }}
        (meliputi Seleksi Administrasi, Seleksi Wawancara, dan Micro Teaching)
        beserta status kelulusan akhir:
    </p>

    {{-- ===== TABEL KANDIDAT ===== --}}
    <table class="t-kandidat" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="td-no">No</th>
                <th>Nama Kandidat</th>
                <th>Jabatan</th>
                <th class="td-status">Status Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kandidats as $i => $lamaran)
            <tr>
                <td class="td-no">{{ $i + 1 }}</td>
                <td>{{ $lamaran->pelamar->nama }}</td>
                <td>
                    Dosen Pengajar
                </td>
                <td class="td-status {{ $lamaran->status === 'diterima' ? 'diterima' : 'ditolak' }}">
                    {{ strtoupper($lamaran->status_label) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;font-style:italic;color:#777;padding:14px;">
                    Belum ada kandidat yang diterima atau ditolak.
                </td>
            </tr>
            @endforelse
            {{-- Baris kosong agar tabel tidak terlalu pendek --}}
            @for($x = 0; $x < max(0, 3 - $kandidats->count()); $x++)
            <tr>
                <td class="td-no td-empty">&nbsp;</td>
                <td class="td-empty">&nbsp;</td>
                <td class="td-empty">&nbsp;</td>
                <td class="td-empty">&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- ===== PENUTUP ===== --}}
    <p class="penutup">
        Demikian Berita Acara ini dibuat dengan sebenarnya untuk menjadi dokumen acuan dan dapat
        dipergunakan sebagaimana mestinya.
    </p>

    {{-- ===== TANDA TANGAN ===== --}}
    <table class="t-ttd" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                Mengetahui,<br>
                Ketua Panitia Seleksi
                <br>
                <span class="ttd-line">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</span>
            </td>
            <td class="right">
                Surabaya, {{ $tanggalFormatted }}<br>
                Sekretaris Panitia
                <br>
                <span class="ttd-line">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</span>
            </td>
        </tr>
    </table>

</div>
</body>
</html>