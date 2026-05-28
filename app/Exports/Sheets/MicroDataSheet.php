<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MicroDataSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected array $data;

    public function __construct(array $data) { $this->data = $data; }

    public function title(): string { return 'data microteaching'; }

    public function array(): array
    {
        $rows = [];

        $rows[] = [
            'Nama Penilai', 'Nama Kandidat', 'Prodi Tujuan',
            'PP.1', 'PP.2',
            'PM.3', 'PM.4', 'PM.5',
            'Sis.6', 'Sis.7', 'Sis.8', 'Sis.9', 'Sis.10', 'Sis.11',
            'PKI.12', 'PKI.13', 'PKI.14',
            'SE.15',
            'Catatan Penilai', 'Rekomendasi', 'Rekomendasi Prodi Tujuan',
            'Kelompok Keahlian', 'Bidang Keahlian Kandidat',
            'SUM', 'AVERAGE',
        ];

        foreach ($this->data['microRows'] as $r) {
            $rows[] = array_merge(
                [$r['penguji'], $r['kandidat'], $r['prodi_tujuan']],
                $r['items'],
                [
                    $r['catatan'], $r['rekomendasi'], $r['prodi_rek'],
                    $r['kelompok'], $r['bidang'],
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
        return ['A' => 20, 'B' => 20, 'C' => 30, 'X' => 10, 'Y' => 10];
    }
}
