<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara — {{ $lowongan->nama_posisi }}</title>
    <!-- load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Times New Roman', 'Times', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            body {
                background-color: #ffffff !important;
            }
            .no-print {
                display: none !important;
            }
            .page {
                margin: 0 !important;
                box-shadow: none !important;
                width: 297mm !important;
                height: 210mm !important;
            }
        }
        @page {
            size: A4 landscape;
            margin: 0;
        }
    </style>
</head>
<body class="bg-gray-200 font-serif text-[10.5pt] text-black">

<div class="no-print fixed top-4 right-4 z-50 flex gap-2">
    <button onclick="window.print()" class="px-5 py-2 bg-[#8b1515] text-white font-bold rounded-lg text-sm cursor-pointer shadow hover:bg-red-900 transition">🖨️ Cetak</button>
    <button onclick="window.close()" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 font-bold rounded-lg text-sm cursor-pointer shadow hover:bg-gray-100 transition">✕ Tutup</button>
</div>

<div class="page w-[297mm] min-h-[210mm] mx-auto bg-white p-[12mm_18mm_0_18mm] relative shadow-xl">
    <div class="relative flex flex-col justify-between min-h-[calc(210mm-24mm)]">
        
        {{-- HEADER --}}
        <div class="text-center mb-2">
            <div class="mb-1 flex justify-center">
                <img src="{{ asset('images/logo-telu.png') }}" alt="Logo Telkom University" class="w-[60px] h-auto">
            </div>
            <div class="font-serif text-[13pt] font-bold uppercase tracking-wider leading-tight">TELKOM UNIVERSITY SURABAYA</div>
            <hr class="border-t-4 border-black my-2">
            <div class="font-serif text-[10.5pt] font-bold uppercase leading-normal tracking-wide">
                @if($lowongan->kategori === 'Tenaga Kependidikan')
                    PENETAPAN HASIL SELEKSI REKRUT TENAGA KEPENDIDIKAN<br>
                @else
                    PENETAPAN HASIL AKHIR MICROTEACHING DAN WAWANCARA REKRUT DOSEN TENAGA PROFESIONAL<br>
                @endif
                UNIVERSITAS TELKOM (KAMPUS KOTA SURABAYA)<br>
                BATCH 1 TAHUN {{ now()->year }}
            </div>
            <div class="text-[8.5pt] mt-1 font-bold">SDM03/TUKS/{{ now()->year }}</div>
        </div>

        {{-- PEMBUKA --}}
        <p class="font-serif text-[10pt] leading-relaxed text-justify mb-2">
            Pada hari ini <strong class="font-bold">{{ $hariStr }}</strong> tanggal <strong class="font-bold">{{ $tglStr }}</strong>
            bulan <strong class="font-bold">{{ $bulanStr }}</strong> tahun <strong class="font-bold">{{ $tahunStr }}</strong>,
            bertempat di Ruang Rapat Universitas Telkom (Kampus Kota Surabaya), telah ditetapkan Hasil
            @if($lowongan->kategori === 'Tenaga Kependidikan')
                Seleksi Rekrut Tenaga Kependidikan Universitas Telkom (Kampus Kota Surabaya)
                Batch 1 tahun {{ now()->year }} untuk dapat ditindak lanjuti dengan rincian sebagai berikut :
            @else
                Akhir Microteaching dan Wawancara Rekrut Dosen Tenaga Profesional Universitas Telkom (Kampus Kota Surabaya)
                Batch 1 tahun {{ now()->year }} untuk dapat ditindak lanjuti dalam proses psikotest dengan rincian sebagai berikut :
            @endif
        </p>

        {{-- TABEL UTAMA --}}
        @if($lowongan->kategori === 'Tenaga Kependidikan')
        {{-- Tabel Tendik: Sederhana --}}
        <table class="w-full border-collapse border border-black font-serif text-[8.5pt] mb-3">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[22px]">No</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[100px]">Nama</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[60px]">Pendidikan</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[80px]">Nilai Wawancara</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[60px]">Nilai Akhir</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt]">Catatan</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[80px]">Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kandidats as $i => $k)
                <tr class="hover:bg-gray-50">
                    <td class="border border-black p-1 text-center">{{ $i + 1 }}</td>
                    <td class="border border-black p-1 text-left font-semibold">{{ $k->nama }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->jenjangDisplay }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->avgWawancara ?? '-' }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $k->avgWawancara ?? '-' }}</td>
                    <td class="border border-black p-1 text-justify text-[8pt] leading-normal">{{ $k->catatan ?: '-' }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $k->rekomendasi }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="border border-black p-3 text-center italic text-gray-500">
                        Belum ada kandidat yang ditetapkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @else
        {{-- Tabel Dosen: Lengkap --}}
        <table class="w-full border-collapse border border-black font-serif text-[8.5pt] mb-3">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[22px]" rowspan="2">No</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[70px]" rowspan="2">Nama</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[52px]" colspan="3">Nilai Kualifikasi (40%)</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[44px]" rowspan="2">Nilai Akhir</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt]" rowspan="2">Catatan</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[72px]" rowspan="2">Rekomendasi</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[90px]" rowspan="2">Prodi</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[34px]" rowspan="2">JFA</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[36px]" rowspan="2">Pendidikan</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[80px]" rowspan="2">Kelompok Keahlian</th>
                </tr>
                <tr class="bg-gray-100">
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[52px]">Nilai Kualifikasi (40%)</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[52px]">Nilai Microteaching (20%)</th>
                    <th class="border border-black p-1 font-bold text-center text-[8pt] w-[52px]">Nilai Wawancara (40%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kandidats as $i => $k)
                <tr class="hover:bg-gray-50">
                    <td class="border border-black p-1 text-center">{{ $i + 1 }}</td>
                    <td class="border border-black p-1 text-left font-semibold">{{ $k->nama }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->avgKualifikasi ?? '-' }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->avgMicro ?? '-' }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->avgWawancara ?? '-' }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $k->hasilAkhir ?? '-' }}</td>
                    <td class="border border-black p-1 text-justify text-[8pt] leading-normal">{{ $k->catatan ?: '-' }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $k->rekomendasi }}</td>
                    <td class="border border-black p-1 text-left">{{ $k->prodiTujuan }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->jfaLabel }}</td>
                    <td class="border border-black p-1 text-center">{{ $k->jenjangDisplay }}</td>
                    <td class="border border-black p-1 text-center text-[8pt]">{{ $k->kelompokKeahlian }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="border border-black p-3 text-center italic text-gray-500">
                        Belum ada kandidat yang ditetapkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="text-center font-serif text-[10pt] mb-1">{{ $tanggalFormatted }}</div>
        <div class="text-center font-serif text-[10pt] font-bold mb-3">Mengetahui dan Menyetujui,</div>

        <div class="grid grid-cols-4 gap-4 items-stretch font-serif text-[9.5pt]">
            <!-- Direktur -->
            <div class="flex flex-col justify-between items-center text-center h-[110px]">
                <div class="font-bold leading-normal flex items-start justify-center h-[2.8em] text-center w-full">
                    Direktur Universitas Telkom Kampus Surabaya
                </div>
                <div class="font-bold mt-auto w-full">(................................)</div>
            </div>
            <!-- Wakil Direktur Akademik -->
            <div class="flex flex-col justify-between items-center text-center h-[110px]">
                <div class="font-bold leading-normal flex items-start justify-center h-[2.8em] text-center w-full">
                    Wakil Direktur Bidang Akademik dan Riset
                </div>
                <div class="font-bold mt-auto w-full">(................................)</div>
            </div>
            <!-- Wakil Direktur Sumber Daya -->
            <div class="flex flex-col justify-between items-center text-center h-[110px]">
                <div class="font-bold leading-normal flex items-start justify-center h-[2.8em] text-center w-full">
                    Wakil Direktur Bidang Sumber Daya
                </div>
                <div class="font-bold mt-auto w-full">(................................)</div>
            </div>
            <!-- Kepala Urusan SDM -->
            <div class="flex flex-col justify-between items-center text-center h-[110px]">
                <div class="font-bold leading-normal flex items-start justify-center h-[2.8em] text-center w-full">
                    Kepala Urusan SDM
                </div>
                <div class="font-bold mt-auto w-full">(................................)</div>
            </div>
        </div>


    </div>
</div>

<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
