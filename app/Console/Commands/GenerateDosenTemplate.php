<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateDosenTemplate extends Command
{
    protected $signature   = 'generate:dosen-template';
    protected $description = 'Generate Excel template for dosen import';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dosen');

        $columns = [
            // [key, label, wajib]
            ['nama', 'nama', true],
            ['kode', 'kode', true],
            ['nip',  'nip',  false],
            ['nidn', 'nidn', false],
        ];

        // ── Header row ────────────────────────────────────────────────────
        foreach ($columns as $idx => [$key, $label, $required]) {
            $col  = $idx + 1;
            $cell = $sheet->getCellByColumnAndRow($col, 1);
            $cell->setValue($label);

            $bgColor = $required ? 'FF8b1515' : 'FF1a5e1a'; // merah / hijau tua
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                 ->getStartColor()->setARGB($bgColor);
            $cell->getStyle()->getFont()->setBold(true);
            $cell->getStyle()->getFont()->getColor()->setARGB('FFFFFFFF');
            $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // ── Contoh data dosen 1 (baris 2) ────────────────────────────────
        $row2 = [
            'Dr. Budi Santoso, M.Kom.',
            'BDS',
            '19850101202201',
            '0401018503',
        ];

        // ── Contoh data dosen 2 (baris 3) ────────────────────────────────
        $row3 = [
            'Siti Rahayu, S.T., M.T.',
            'SRH',
            '19900425202301',
            '0425049005',
        ];

        foreach ($row2 as $idx => $value) {
            $sheet->getCellByColumnAndRow($idx + 1, 2)->setValue($value);
        }
        foreach ($row3 as $idx => $value) {
            $sheet->getCellByColumnAndRow($idx + 1, 3)->setValue($value);
        }

        // ── Lebar kolom & freeze ─────────────────────────────────────────
        $sheet->getColumnDimensionByColumn(1)->setWidth(30); // nama
        $sheet->getColumnDimensionByColumn(2)->setWidth(12); // kode
        $sheet->getColumnDimensionByColumn(3)->setWidth(20); // nip
        $sheet->getColumnDimensionByColumn(4)->setWidth(16); // nidn
        $sheet->freezePane('A2');

        // ── Legenda ──────────────────────────────────────────────────────
        $legendCol = count($columns) + 2;
        $sheet->getCellByColumnAndRow($legendCol, 1)->setValue('KETERANGAN WARNA:');
        $sheet->getCellByColumnAndRow($legendCol, 2)->setValue('Merah  = WAJIB diisi');
        $sheet->getCellByColumnAndRow($legendCol, 3)->setValue('Hijau  = OPSIONAL (boleh kosong)');

        // ── Simpan ────────────────────────────────────────────────────────
        $writer = new Xlsx($spreadsheet);
        $path   = public_path('templates/dosen_template.xlsx');

        if (!is_dir(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        $writer->save($path);
        $this->info('Template dosen berhasil dibuat: ' . $path);
    }
}
