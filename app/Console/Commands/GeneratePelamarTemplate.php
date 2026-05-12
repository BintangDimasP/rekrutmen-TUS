<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GeneratePelamarTemplate extends Command
{
    protected $signature = 'generate:pelamar-template';
    protected $description = 'Generate Excel template for pelamar import';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pelamar');

        // Headers
        $headers = [
            'nama',
            'nik',
            'no_telepon',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'jenjang',
            'institusi',
            'prodi_pendidikan',
            'ipk',
            'jenjang_2',
            'institusi_2',
            'prodi_pendidikan_2',
            'ipk_2',
            'jenjang_3',
            'institusi_3',
            'prodi_pendidikan_3',
            'ipk_3',
            'nidn',
            'homebase',
            'jabatan_akademik',
            'minat_riset',
            'h_index',
            'jenis_tes_bahasa',
            'skor_bahasa',
            'tanggal_tes_bahasa',
            'kategori_sertifikat',
        ];

        // Write headers
        foreach ($headers as $index => $header) {
            $cell = $sheet->getCellByColumnAndRow($index + 1, 1);
            $cell->setValue($header);
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF8b1515');
            $cell->getStyle()->getFont()->setBold(true);
            $cell->getStyle()->getFont()->getColor()->setARGB('FFFFFFFF');
            $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Add example row
        $exampleData = [
            'Budi Santoso',
            '1234567890123456',
            '081234567890',
            'L',
            'Jakarta',
            '1990-01-15',
            'Jl. Merdeka No. 123, Jakarta',
            'S2',
            'Universitas Indonesia',
            'Teknik Informatika',
            '3.75',
            'S1',
            'ITB',
            'Teknik Komputer',
            '3.50',
            '',
            '',
            '',
            '',
            '123456789',
            'Teknik Informatika',
            'lektor',
            'Artificial Intelligence, Machine Learning',
            '15',
            'TOEFL_ITP',
            '550',
            '2023-06-15',
            'kompetensi',
        ];

        foreach ($exampleData as $index => $value) {
            $sheet->getCellByColumnAndRow($index + 1, 2)->setValue($value);
        }

        // Set column widths
        foreach ($headers as $index => $header) {
            $sheet->getColumnDimensionByColumn($index + 1)->setWidth(18);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Save file
        $writer = new Xlsx($spreadsheet);
        $path = public_path('templates/pelamar_template.xlsx');
        
        if (!is_dir(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        $writer->save($path);
        $this->info('Template generated successfully at: ' . $path);
    }
}
