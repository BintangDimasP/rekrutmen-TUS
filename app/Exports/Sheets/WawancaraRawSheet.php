<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WawancaraRawSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data) { $this->data = $data; }

    public function title(): string { return 'wawancara'; }

    public function array(): array
    {
        $rows = [];

        $rows[] = [
            'Nama Penilai', 'Nama Kandidat', 'Prodi Tujuan',
            'Ind.1. motivasi',
            'Ind.2. Potensi Kontribusi terhadap Program Studi dan Institusi',
            'Ind.3. Kemampuan Penelitian & Publikasi',
            'Ind.4. Kemampuan Komunikasi, Terutama Menjawab Pertanyaan Dengan Cepat dan Tepat',
            'Ind.5. Kontribusi yang Pernah Dilakukan / Memiliki Link Relasi Dengan Pihak Lain',
            'Catatan Penilai', 'Rekomendasi', 'Rekomendasi Prodi Tujuan',
        ];

        foreach ($this->data['wawancaraRows'] as $r) {
            $rows[] = array_merge(
                [$r['penguji'], $r['kandidat'], $r['prodi_tujuan']],
                $r['items'],
                [$r['catatan'], $r['rekomendasi'], $r['prodi_rek']]
            );
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '8B1515']],
                'alignment' => ['wrapText' => true, 'vertical' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 20, 'C' => 30];
    }
}
