<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GeneratePelamarTemplate extends Command
{
    protected $signature   = 'generate:pelamar-template';
    protected $description = 'Generate Excel template for pelamar import';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pelamar');

        // ── Hanya 2 kolom WAJIB ───────────────────────────────────────────
        $columns = [
            ['email', 'email'],
            ['nama',  'nama'],
        ];

        // ── Baris 1: Header (merah) ───────────────────────────────────────
        foreach ($columns as $idx => [$key, $label]) {
            $col  = $idx + 1;
            $cell = $sheet->getCellByColumnAndRow($col, 1);
            $cell->setValue($label);

            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                 ->getStartColor()->setARGB('FF8b1515');
            $cell->getStyle()->getFont()->setBold(true)->setSize(11);
            $cell->getStyle()->getFont()->getColor()->setARGB('FFFFFFFF');
            $cell->getStyle()->getAlignment()
                 ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                 ->setVertical(Alignment::VERTICAL_CENTER);
            $cell->getStyle()->getBorders()->getAllBorders()
                 ->setBorderStyle(Border::BORDER_THIN)
                 ->getColor()->setARGB('FFcccccc');
        }

        $sheet->getRowDimension(1)->setRowHeight(28);

        // ── Baris 2+: Contoh data (langsung setelah header, tanpa row keterangan) ─
        $examples = [
            ['budi.santoso@gmail.com', 'Budi Santoso, S.T., M.T.'],
            ['siti.rahayu@gmail.com',  'Siti Rahayu, S.Kom., M.Kom.'],
            ['ahmad.fauzi@gmail.com',  'Ahmad Fauzi'],
        ];

        foreach ($examples as $rowOff => $data) {
            $excelRow = $rowOff + 2;
            foreach ($data as $colIdx => $val) {
                $cell = $sheet->getCellByColumnAndRow($colIdx + 1, $excelRow);
                $cell->setValue($val);

                $bg = ($rowOff % 2 === 0) ? 'FFF7F7F7' : 'FFFFFFFF';
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                     ->getStartColor()->setARGB($bg);
                $cell->getStyle()->getBorders()->getAllBorders()
                     ->setBorderStyle(Border::BORDER_THIN)
                     ->getColor()->setARGB('FFcccccc');
                $cell->getStyle()->getAlignment()
                     ->setVertical(Alignment::VERTICAL_CENTER);
            }
        }

        // ── Lebar kolom ───────────────────────────────────────────────────
        $sheet->getColumnDimensionByColumn(1)->setWidth(35); // email
        $sheet->getColumnDimensionByColumn(2)->setWidth(35); // nama

        // ── Freeze header ─────────────────────────────────────────────────
        $sheet->freezePane('A2');

        // ── Simpan ────────────────────────────────────────────────────────
        $writer = new Xlsx($spreadsheet);
        $path   = public_path('templates/pelamar_template.xlsx');

        if (!is_dir(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        $writer->save($path);
        $this->info('✅ Template berhasil dibuat: ' . $path);
        $this->info('   Kolom  : email, nama');
        $this->info('   Format : Row 1 = header, Row 2+ = data');
    }
}
