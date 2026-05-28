<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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

        /*
         * Kolom WAJIB  → background merah  (harus diisi)
         * Kolom OPSIONAL → background hijau (boleh kosong)
         */
        $columns = [
            // [key, label, wajib]
            ['nama',               'nama',               true],
            ['nik',                'nik',                true],
            ['tempat_lahir',       'tempat_lahir',       true],
            ['tanggal_lahir',      'tanggal_lahir',      true],   // YYYY-MM-DD
            ['no_telepon',         'no_telepon',         true],
            ['jenis_kelamin',      'jenis_kelamin',      true],   // L / P
            ['kewarganegaraan',    'kewarganegaraan',    false],
            ['status_pernikahan',  'status_pernikahan',  false],
            ['alamat_domisili',    'alamat_domisili',    false],
            ['alamat_ktp',         'alamat_ktp',         false],

            // Pendidikan 1
            ['jenjang',            'jenjang',            false],  // S1/S2/S3
            ['institusi',          'institusi',          false],
            ['prodi_pendidikan',   'prodi_pendidikan',   false],
            ['akreditas',          'akreditas',          false],
            ['no_ijazah',          'no_ijazah',          false],
            ['ipk',                'ipk',                false],

            // Pendidikan 2
            ['jenjang_2',          'jenjang_2',          false],
            ['institusi_2',        'institusi_2',        false],
            ['prodi_pendidikan_2', 'prodi_pendidikan_2', false],
            ['akreditas_2',        'akreditas_2',        false],
            ['no_ijazah_2',        'no_ijazah_2',        false],
            ['ipk_2',              'ipk_2',              false],

            // Pendidikan 3
            ['jenjang_3',          'jenjang_3',          false],
            ['institusi_3',        'institusi_3',        false],
            ['prodi_pendidikan_3', 'prodi_pendidikan_3', false],
            ['akreditas_3',        'akreditas_3',        false],
            ['no_ijazah_3',        'no_ijazah_3',        false],
            ['ipk_3',              'ipk_3',              false],

            // Bahasa & Sertifikat
            ['jenis_tes_bahasa',   'jenis_tes_bahasa',   false],  // PBT/TOEFL_ITP/EPrT/CBT/IBT/IELTS/AcEPT
            ['skor_bahasa',        'skor_bahasa',        false],
            ['tanggal_tes_bahasa', 'tanggal_tes_bahasa', false],
            ['kategori_sertifikat','kategori_sertifikat',false],  // kompetensi/keahlian_khusus

            // Akademik
            ['nidn',               'nidn',               false],
            ['homebase',           'homebase',           false],
            ['jabatan_akademik',   'jabatan_akademik',   false],  // asisten_ahli/lektor/lektor_kepala/guru_besar/non_jabatan
            ['minat_riset',        'minat_riset',        false],
            ['h_index',            'h_index',            false],
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

        // ── Contoh data pelamar 1 (baris 2) ──────────────────────────────
        $row3 = [
            'Budi Santoso',
            '3201011501900001',
            'Jakarta',
            '1990-01-15',
            '081234567890',
            'L',
            'WNI',
            'Menikah',
            'Jl. Merdeka No. 123, Jakarta Selatan',
            'Jl. Pahlawan No. 5, Jakarta Pusat',
            'S3',
            'Universitas Indonesia',
            'Teknik Informatika',
            'A',
            'IJ-UI-2018-001',
            '3.82',
            'S2',
            'Institut Teknologi Bandung',
            'Teknik Komputer',
            'A',
            'IJ-ITB-2013-045',
            '3.65',
            '',
            '',
            '',
            '',
            '',
            '',
            'TOEFL_ITP',
            '570',
            '2023-06-15',
            'kompetensi',
            '0023015001',
            'Teknik Informatika',
            'lektor',
            'Artificial Intelligence, Machine Learning',
            '12',
        ];

        // ── Contoh data pelamar 2 (baris 4) ──────────────────────────────
        $row4 = [
            'Siti Rahayu',
            '3578024504950002',
            'Surabaya',
            '1995-04-25',
            '089876543210',
            'P',
            'WNI',
            'Belum Menikah',
            'Jl. Ahmad Yani No. 88, Surabaya',
            'Jl. Ahmad Yani No. 88, Surabaya',
            'S2',
            'Institut Teknologi Sepuluh Nopember',
            'Sistem Informasi',
            'A',
            'IJ-ITS-2020-099',
            '3.91',
            'S1',
            'Institut Teknologi Sepuluh Nopember',
            'Sistem Informasi',
            'A',
            'IJ-ITS-2016-055',
            '3.78',
            '',
            '',
            '',
            '',
            '',
            '',
            'IELTS',
            '6.5',
            '2024-03-10',
            '',
            '',
            '',
            '',
            'Data Science, Big Data Analytics',
            '3',
        ];

        foreach ($row3 as $idx => $value) {
            $sheet->getCellByColumnAndRow($idx + 1, 2)->setValue($value);
        }
        foreach ($row4 as $idx => $value) {
            $sheet->getCellByColumnAndRow($idx + 1, 3)->setValue($value);
        }

        // ── Lebar kolom & freeze ─────────────────────────────────────────
        foreach ($columns as $idx => $_) {
            $sheet->getColumnDimensionByColumn($idx + 1)->setWidth(22);
        }
        $sheet->freezePane('A2');

        // ── Legenda kecil di baris 1 setelah kolom terakhir ──────────────
        $legendCol = count($columns) + 2;
        $sheet->getCellByColumnAndRow($legendCol, 1)->setValue('KETERANGAN WARNA:');
        $sheet->getCellByColumnAndRow($legendCol, 2)->setValue('Merah  = WAJIB diisi');
        $sheet->getCellByColumnAndRow($legendCol, 3)->setValue('Hijau  = OPSIONAL (boleh kosong)');
        $sheet->getCellByColumnAndRow($legendCol, 4)->setValue('Kuning = Keterangan kolom (baris 2)');
        $sheet->getCellByColumnAndRow($legendCol, 5)->setValue('File (foto, ijazah, dll) tidak diimpor lewat Excel.');

        // ── Simpan ────────────────────────────────────────────────────────
        $writer = new Xlsx($spreadsheet);
        $path   = public_path('templates/pelamar_template.xlsx');

        if (!is_dir(public_path('templates'))) {
            mkdir(public_path('templates'), 0755, true);
        }

        $writer->save($path);
        $this->info('Template berhasil dibuat: ' . $path);
    }
}
