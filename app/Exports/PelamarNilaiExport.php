<?php

namespace App\Exports;

use App\Models\Pelamar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PelamarNilaiExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected array $pelamarIds;

    public function __construct(array $pelamarIds)
    {
        $this->pelamarIds = $pelamarIds;
    }

    public function title(): string
    {
        return 'Rekap Nilai Pelamar';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pelamar',
            'Posisi Dilamar',
            'Jenjang Tertinggi',
            'Jabatan Akademik (JFA)',
            'H-Index',
            'Status Rekrutmen',
            'Skor Pendidikan (SPT)',
            'Skor JFA',
            'Skor H-Index',
            'Rata-Rata Kualifikasi (A)',
            'Avg Micro Teaching (B)',
            'Avg Wawancara (C)',
            'Nilai Akhir',
            'Rekomendasi',
            'Prodi Tujuan',
        ];
    }

    public function collection(): Collection
    {
        $pelamars = Pelamar::with([
            'lamarans.lowongan',
            'lamarans.jadwalSeleksis.penilaian',
            'lamarans.jadwalSeleksis.penguji',
        ])
            ->whereIn('id', $this->pelamarIds)
            ->get();

        $rows = collect();
        $no   = 1;

        foreach ($pelamars as $pelamar) {
            foreach ($pelamar->lamarans as $lamaran) {
                // Kelompokkan jadwal per tipe
                $semuaJadwal = $lamaran->jadwalSeleksis ?? collect();

                $microDinilai     = $semuaJadwal->where('tipe', 'micro_teaching')
                    ->filter(fn($j) => $j->penilaian !== null)
                    ->values();
                $wawancaraDinilai = $semuaJadwal->where('tipe', 'wawancara')
                    ->filter(fn($j) => $j->penilaian !== null)
                    ->values();

                // Hanya export pelamar yang sudah ada nilai akhirnya
                if ($microDinilai->count() === 0 || $wawancaraDinilai->count() === 0) {
                    continue;
                }

                // AVG Micro & Wawancara
                $avgMicro     = round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2);
                $avgWawancara = round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2);

                // Status rekrutmen (dari penilaian wawancara pertama)
                $statusRekrutmenNilai = $wawancaraDinilai->first()?->penilaian?->status_rekrutmen ?? null;

                // Jenjang tertinggi
                $jenjangTertinggi = collect([
                    $pelamar->jenjang_3 ?? null,
                    $pelamar->jenjang_2 ?? null,
                    $pelamar->jenjang   ?? null,
                ])
                    ->filter()
                    ->map(fn($j) => strtolower(trim($j)))
                    ->first();

                $isS3 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's3') || str_contains($jenjangTertinggi, 'doktor'));
                $isS2 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's2') || str_contains($jenjangTertinggi, 'magister') || str_contains($jenjangTertinggi, 'master'));

                // SPT
                $sptSkor  = 0;
                $sptLabel = '-';
                if ($isS3) {
                    if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 5; $sptLabel = 'S3 Prof Full Time'; }
                    elseif ($statusRekrutmenNilai === 'praktisi_part_time')  { $sptSkor = 4; $sptLabel = 'S3 Praktisi Part Time'; }
                    else { $sptSkor = 3; $sptLabel = 'S3 On Going'; }
                } elseif ($isS2) {
                    if ($statusRekrutmenNilai === 'profesional_full_time') { $sptSkor = 2; $sptLabel = 'S2 Prof Full Time'; }
                    else { $sptSkor = 1; $sptLabel = 'S2 Praktisi Part Time'; }
                }

                $jenjangDisplay = $isS3 ? 'S3' : ($isS2 ? 'S2' : ($pelamar->jenjang ?? '-'));

                // JFA
                $jfaSkorMap  = ['guru_besar' => 5, 'lektor_kepala' => 4, 'lektor' => 3, 'asisten_ahli' => 2, 'non_jabatan' => 1];
                $jfaLabelMap = ['guru_besar' => 'Guru Besar', 'lektor_kepala' => 'Lektor Kepala', 'lektor' => 'Lektor', 'asisten_ahli' => 'Asisten Ahli', 'non_jabatan' => 'Non Jabatan'];
                $jfaKey   = $pelamar->jabatan_akademik ?? 'non_jabatan';
                $jfaSkor  = $jfaSkorMap[$jfaKey]  ?? 1;
                $jfaLabel = $jfaLabelMap[$jfaKey] ?? 'Non Jabatan';

                // H-Index
                $hIndex = (int)($pelamar->h_index ?? 0);
                if ($hIndex > 10)     $hSkor = 5;
                elseif ($hIndex >= 5) $hSkor = 4;
                elseif ($hIndex >= 2) $hSkor = 3;
                elseif ($hIndex >= 1) $hSkor = 2;
                else                  $hSkor = 1;

                // Rata-rata kualifikasi
                $avgKualifikasi = round(($sptSkor + $jfaSkor + $hSkor) / 3, 4);

                // Nilai akhir
                $nilaiAkhir = round(($avgKualifikasi * 0.40) + ($avgMicro * 0.20) + ($avgWawancara * 0.40), 2);

                // Rekomendasi
                $rekAll = collect([...$microDinilai->pluck('penilaian'), ...$wawancaraDinilai->pluck('penilaian')])
                    ->pluck('rekomendasi')->filter();
                $direkomendasikan = $rekAll->contains('direkomendasikan');
                $tidakDirek       = $rekAll->contains('tidak_direkomendasikan');
                $rekomendasi      = ($direkomendasikan && !$tidakDirek) ? 'Direkomendasikan' : 'Tidak Direkomendasikan';

                // Prodi tujuan
                $rekDetail   = $wawancaraDinilai->first(fn($j) => $j->penilaian?->rekomendasi === 'direkomendasikan')?->penilaian
                             ?? $wawancaraDinilai->first()?->penilaian;
                $prodiTujuan = $rekDetail?->prodi_tujuan ?? '-';

                // Status rekrutmen label
                $statusRekLabel = [
                    'on_going'              => 'On Going',
                    'praktisi_part_time'    => 'Praktisi Part Time',
                    'profesional_full_time' => 'Profesional Full Time',
                ];
                $statusDisplay = $statusRekLabel[$statusRekrutmenNilai] ?? '-';

                $rows->push([
                    $no++,
                    $pelamar->nama,
                    $lamaran->lowongan->nama_posisi ?? '-',
                    $jenjangDisplay,
                    $jfaLabel,
                    $hIndex,
                    $statusDisplay,
                    $sptSkor,
                    $jfaSkor,
                    $hSkor,
                    $avgKualifikasi,
                    $avgMicro,
                    $avgWawancara,
                    $nilaiAkhir,
                    $rekomendasi,
                    $prodiTujuan,
                ]);
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row — bold, bg maroon, white text
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '8B1515']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 28,  // Nama
            'C' => 28,  // Posisi
            'D' => 14,  // Jenjang
            'E' => 20,  // JFA
            'F' => 10,  // H-Index
            'G' => 22,  // Status Rekrutmen
            'H' => 14,  // SPT
            'I' => 12,  // JFA Skor
            'J' => 14,  // H Skor
            'K' => 22,  // Rata-Rata A
            'L' => 22,  // Avg Micro
            'M' => 22,  // Avg Wawancara
            'N' => 14,  // Nilai Akhir
            'O' => 24,  // Rekomendasi
            'P' => 28,  // Prodi Tujuan
        ];
    }
}
