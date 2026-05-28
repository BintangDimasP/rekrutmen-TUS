<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MicroRawSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data) { $this->data = $data; }

    public function title(): string { return 'micro'; }

    public function array(): array
    {
        $rows = [];

        // Header row
        $rows[] = [
            'Nama Penilai', 'Nama Kandidat', 'Prodi Tujuan',
            // Perencanaan Pembelajaran (k1: 2 items)
            'PP.1. Calon dosen menyampaikan rencana pembelajaran yang mencakup materi, tujuan, dan aturan kegiatan pembelajaran serta penilaian hasil belajar',
            'PP.2. Calon dosen menyampaikan outline mengenai materi yang akan disampaikan',
            // Penguasaan Materi (k2: 3 items)
            'PM.3. Calon dosen menunjukkan penguasaan materi pembelajaran',
            'PM.4. Materi yang disampaikan terupdate dengan isu terkini dan relevan terhadap kebutuhan kompetensi yang ditetapkan prodi',
            'PM.5. Calon dosen mengaitkan materi dengan keilmuan lain yang relevan',
            // Sistematika (k3: 6 items)
            'Sis.6. Calon dosen menjelaskan materi secara sistematis / runtut',
            'Sis.7. Calon dosen menjelaskan materi dengan memberikan contoh konkret/nyata',
            'Sis.8. Calon dosen menggunakan metode mengajar yang variatif',
            'Sis.9. Calon dosen menggunakan bahasa lisan dan tulis secara jelas, baik, dan benar',
            'Sis.10. Calon dosen mengkolaborasikan beberapa media dan atau software dalam penyampaian materi',
            'Sis.11. Calon dosen memberikan refleksi dari materi yang disampaikan',
            // Pengelolaan Kelas (k4: 3 items)
            'PKI.12. Calon dosen memberikan kesempatan untuk adanya interaksi (tanya jawab dan diskusi)',
            'PKI.13. Calon dosen mampu menciptakan kelas yang interaktif dan menarik perhatian',
            'PKI.14. Calon dosen melaksanakan pembelajaran sesuai dengan alokasi waktu yang direncanakan',
            // Sikap dan Etika (k5: 1 item)
            'SE.15. Calon dosen berpakaian sopan dan bersikap profesional selama melaksanakan pembelajaran',
            // Lainnya
            'Catatan Penilai', 'Rekomendasi', 'Rekomendasi Prodi Tujuan', 'Kelompok Keahlian', 'Bidang Keahlian Kandidat',
        ];

        foreach ($this->data['microRows'] as $r) {
            $rows[] = array_merge(
                [$r['penguji'], $r['kandidat'], $r['prodi_tujuan']],
                $r['items'],
                [$r['catatan'], $r['rekomendasi'], $r['prodi_rek'], $r['kelompok'], $r['bidang']]
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
