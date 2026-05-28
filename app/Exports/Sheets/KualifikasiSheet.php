<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KualifikasiSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected array $data;

    public function __construct(array $data) { $this->data = $data; }

    public function title(): string { return 'kualifikasi dosen'; }

    public function array(): array
    {
        $rows = [];

        $rows[] = [
            'NO', 'NAMA', 'BIDANG KEAHLIAN',
            'PENDIDIKAN TERAKHIR', 'STATUS', 'PENDIDIKAN TERAKHIR STATUS',
            'KOMPOSISI PENILAIAN (PENDIDIKAN)',
            'JFA', 'KOMPOSISI PENILAIAN (JFA)',
            'H-INDEX', 'KOMPOSISI PENILAIAN (H-INDEX)',
            'SUM', 'AVG',
        ];

        foreach ($this->data['kualifikasiRows'] as $r) {
            $rows[] = [
                $r['no'],
                $r['nama'],
                $r['bidang'],
                $r['jenjang'],
                $r['status'],
                $r['spt_label'],
                $r['spt_skor'],
                $r['jfa_label'],
                $r['jfa_skor'],
                $r['h_index'],
                $r['h_skor'],
                $r['sum'],
                $r['avg'],
            ];
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
        return [
            'A' => 5, 'B' => 28, 'C' => 24, 'D' => 14, 'E' => 24,
            'F' => 28, 'G' => 14, 'H' => 24, 'I' => 14,
            'J' => 10, 'K' => 14, 'L' => 8, 'M' => 10,
        ];
    }
}
