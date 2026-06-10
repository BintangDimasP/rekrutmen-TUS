<?php

namespace App\Exports\Sheets;

use App\Models\JadwalSeleksi;
use App\Models\Lowongan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class HasilAkhirSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected array $data;
    protected Lowongan $lowongan;

    public function __construct(array $data, Lowongan $lowongan)
    {
        $this->data     = $data;
        $this->lowongan = $lowongan;
    }

    public function title(): string { return 'Hasil Akhir'; }

    public function array(): array
    {
        $rows = [];

        // ── Row 1: Title ────────────────────────────────────────────────────
        $rows[] = ['REKAP HASIL PENILAIAN MICROTEACHING DAN WAWANCARA — ' . strtoupper($this->lowongan->nama_posisi)];

        // ── Row 2: Header ────────────────────────────────────────────────────
        $rows[] = [
            'No', 'Nama Kandidat', 'Batch', 'Prodi Tujuan', 'Bidang Keahlian',
            // Kualifikasi (7 cols: F-L)
            'Status Pendidikan', 'Skor', 'Status JFA', 'Skor', 'H-Index', 'Skor', 'Rerata Kualifikasi (A)',
            // Micro (up to 3 penguji)
            'Penguji Micro 1', 'Rerata M1',
            'Penguji Micro 2', 'Rerata M2',
            'Penguji Micro 3', 'Rerata M3',
            'Rerata Microteaching (B)',
            // Wawancara (up to 3 penguji)
            'Penguji Wawancara 1', 'Rerata W1',
            'Penguji Wawancara 2', 'Rerata W2',
            'Penguji Wawancara 3', 'Rerata W3',
            'Rerata Wawancara (C)',
            // Final
            'Nilai Akhir', 'Hasil Rekomendasi',
        ];

        // ── Data rows ────────────────────────────────────────────────────────
        $no = 1;
        $rekMap = ['direkomendasikan' => 'Direkomendasikan', 'tidak_direkomendasikan' => 'Tidak Direkomendasikan', 'perlu_dipertimbangkan' => 'Perlu Dipertimbangkan'];

        $eligibleIds = $this->data['eligibleIds'] ?? [];

        foreach ($this->lowongan->lamarans as $lamaran) {
            // Hanya pelamar yang sudah dinilai micro & wawancara
            if (!in_array($lamaran->pelamar_id, $eligibleIds, true)) continue;

            // Use snapshot data (data saat melamar) for profile fields
            $pelamar    = $lamaran->effectivePelamar;
            $pelamarId  = $lamaran->pelamar_id;

            $jadwals = JadwalSeleksi::with(['penguji', 'penilaian'])
                ->where('pelamar_id', $pelamarId)
                ->where('lowongan_id', $this->lowongan->id)
                ->get();

            $microDinilai     = $jadwals->where('tipe_seleksi', 'micro_teaching')->filter(fn($j) => $j->penilaian !== null)->values();
            $wawancaraDinilai = $jadwals->where('tipe_seleksi', 'wawancara')->filter(fn($j) => $j->penilaian !== null)->values();

            // Kualifikasi from pre-computed data
            $kual = collect($this->data['kualifikasiRows'])->firstWhere('nama', $pelamar->nama);

            $avgKualifikasi = $kual ? $kual['avg'] : '-';
            $sptLabel       = $kual ? $kual['spt_label'] : '-';
            $sptSkor        = $kual ? $kual['spt_skor'] : '-';
            $jfaLabel       = $kual ? $kual['jfa_label'] : '-';
            $jfaSkor        = $kual ? $kual['jfa_skor'] : '-';
            $hIndex         = $kual ? $kual['h_index'] : '-';
            $hSkor          = $kual ? $kual['h_skor'] : '-';
            $bidang         = $kual ? $kual['bidang'] : '-';

            // Micro per penguji (max 3)
            $microCols = [];
            for ($i = 0; $i < 3; $i++) {
                $j = $microDinilai->get($i);
                $microCols[] = $j ? ($j->penguji->nama ?? '-') : '';
                $microCols[] = $j ? (float) $j->penilaian->total_nilai : '';
            }
            $avgMicro = $microDinilai->count() > 0
                ? round($microDinilai->avg(fn($j) => (float) $j->penilaian->total_nilai), 2) : '-';

            // Wawancara per penguji (max 3)
            $wawancaraCols = [];
            for ($i = 0; $i < 3; $i++) {
                $j = $wawancaraDinilai->get($i);
                $wawancaraCols[] = $j ? ($j->penguji->nama ?? '-') : '';
                $wawancaraCols[] = $j ? (float) $j->penilaian->total_nilai : '';
            }
            $avgWawancara = $wawancaraDinilai->count() > 0
                ? round($wawancaraDinilai->avg(fn($j) => (float) $j->penilaian->total_nilai), 2) : '-';

            // Nilai akhir: A×0.40 + B×0.20 + C×0.40
            $nilaiAkhir = (is_numeric($avgKualifikasi) && is_numeric($avgMicro) && is_numeric($avgWawancara))
                ? round(($avgKualifikasi * 0.40) + ($avgMicro * 0.20) + ($avgWawancara * 0.40), 2)
                : '-';

            // Rekomendasi final: tidak direkomendasikan jika ada satu penguji tidak merekomendasikan
            $rekAll = collect([...$microDinilai->pluck('penilaian'), ...$wawancaraDinilai->pluck('penilaian')])->pluck('rekomendasi')->filter();
            $rekFinal = $rekAll->isEmpty() ? '-'
                : (($rekAll->contains('direkomendasikan') && !$rekAll->contains('tidak_direkomendasikan'))
                    ? 'Direkomendasikan' : 'Tidak Direkomendasikan');

            $rows[] = array_merge(
                [
                    $no++,
                    $pelamar->nama,
                    '',  // Batch — kosong, bisa diisi manual
                    $this->lowongan->prodi->nama ?? $this->lowongan->nama_posisi,
                    $bidang,
                    $sptLabel, $sptSkor,
                    $jfaLabel, $jfaSkor,
                    $hIndex, $hSkor,
                    $avgKualifikasi,
                ],
                $microCols,
                [$avgMicro],
                $wawancaraCols,
                [$avgWawancara, $nilaiAkhir, $rekFinal]
            );
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Merge title cell (row 1)
                $sheet->mergeCells('A1:AB1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '8B1515']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Freeze at row 3 (data starts row 3)
                $sheet->freezePane('A3');

                // Auto-filter on header row 2
                $sheet->setAutoFilter('A2:' . $sheet->getHighestColumn() . '2');

                // Color data rows starting from row 3
                for ($row = 3; $row <= $highestRow; $row++) {
                    // Nilai Akhir col Y (25th col)
                    $nilaiCell = $sheet->getCell('Y' . $row)->getValue();
                    if (is_numeric($nilaiCell)) {
                        $color = $nilaiCell >= 3.5 ? 'D1FAE5' : ($nilaiCell >= 2.5 ? 'FEF3C7' : 'FEE2E2');
                        $sheet->getStyle('Y' . $row)->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $color]],
                            'font' => ['bold' => true],
                        ]);
                    }

                    // Rekomendasi col Z
                    $rekCell = $sheet->getCell('Z' . $row)->getValue();
                    if ($rekCell === 'Direkomendasikan') {
                        $sheet->getStyle('Z' . $row)->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '065F46']],
                        ]);
                    } elseif ($rekCell === 'Tidak Direkomendasikan') {
                        $sheet->getStyle('Z' . $row)->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '991B1B']],
                        ]);
                    }

                    // Alternate row shading
                    if ($row % 2 === 0) {
                        $sheet->getStyle('A' . $row . ':Z' . $row)->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F9FAFB']],
                        ]);
                    }
                }
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row 2
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '6B1111']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 26, 'C' => 10, 'D' => 28, 'E' => 22,
            'F' => 26, 'G' => 8,  'H' => 22, 'I' => 8,
            'J' => 10, 'K' => 8,  'L' => 18,
            'M' => 18, 'N' => 10, 'O' => 18, 'P' => 10, 'Q' => 18, 'R' => 10,
            'S' => 18,
            'T' => 18, 'U' => 10, 'V' => 18, 'W' => 10, 'X' => 18, 'Y' => 10,
            'Z' => 18, 'AA' => 14, 'AB' => 22,
        ];
    }
}
