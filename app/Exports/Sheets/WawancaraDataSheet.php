<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WawancaraDataSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected array $data;

    public function __construct(array $data) { $this->data = $data; }

    public function title(): string { return 'data wawancara'; }

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
            'SUM', 'AVERAGE',
        ];

        foreach ($this->data['wawancaraRows'] as $r) {
            $rows[] = array_merge(
                [$r['penguji'], $r['kandidat'], $r['prodi_tujuan']],
                $r['items'],
                [
                    $r['catatan'], $r['rekomendasi'], $r['prodi_rek'],
                    $r['sum'], $r['avg'],
                ]
            );
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:' . $sheet->getHighestColumn() . '1');
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '8B1515']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 20, 'C' => 30, 'P' => 10, 'Q' => 10];
    }
}
