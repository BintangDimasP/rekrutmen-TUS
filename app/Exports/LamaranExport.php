<?php

namespace App\Exports;

use App\Models\Lowongan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Collection;

class LamaranExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithMapping, WithEvents
{
    protected Lowongan $lowongan;

    // Kolom-kolom yang berisi URL file (0-indexed dari kolom A=0)
    // Akan diisi saat mapping, lalu diproses di AfterSheet
    protected array $urlCells = [];

    // Indeks kolom (0-based) yang berisi URL
    protected array $urlColumnIndexes = [
        17, 18,         // Link Ijazah 1, Link Transkrip 1
        25, 26,         // Link Ijazah 2, Link Transkrip 2
        33, 34,         // Link Ijazah 3, Link Transkrip 3
        35, 36, 37,     // Link CV, Pas Foto, KTP
        39,             // Link Sertifikat
        43,             // Link Sertifikat Bahasa
        44, 45, 46,     // Link Surat Lamaran, SK Penyetaraan, Surat Pemberhentian
        52, 53, 54, 55, 56, 57, 58, 59, // Link dokumen dosen
    ];

    public function __construct(Lowongan $lowongan)
    {
        $this->lowongan = $lowongan;
    }

    public function title(): string
    {
        return substr('Lamaran - ' . $this->lowongan->nama_posisi, 0, 31);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap', 'NIK', 'No. Telepon / WA', 'Jenis Kelamin',
            'Tempat Lahir', 'Tanggal Lahir', 'Kewarganegaraan', 'Status Pernikahan',
            'Alamat Domisili', 'Alamat KTP',
            // Pendidikan 1
            'Jenjang 1', 'Institusi 1', 'Prodi 1', 'Akreditasi 1', 'No. Ijazah 1', 'IPK 1',
            'Link Ijazah 1', 'Link Transkrip 1',
            // Pendidikan 2
            'Jenjang 2', 'Institusi 2', 'Prodi 2', 'Akreditasi 2', 'No. Ijazah 2', 'IPK 2',
            'Link Ijazah 2', 'Link Transkrip 2',
            // Pendidikan 3
            'Jenjang 3', 'Institusi 3', 'Prodi 3', 'Akreditasi 3', 'No. Ijazah 3', 'IPK 3',
            'Link Ijazah 3', 'Link Transkrip 3',
            // Dokumen Pendukung
            'Link CV', 'Link Pas Foto', 'Link KTP',
            'Kategori Sertifikat', 'Link Sertifikat',
            'Jenis Tes Bahasa', 'Skor Bahasa', 'Tanggal Tes Bahasa',
            'Link Sertifikat Bahasa',
            'Link Surat Lamaran', 'Link SK Penyetaraan', 'Link Surat Pemberhentian',
            // Akademik Dosen
            'NIDN', 'Homebase', 'Jabatan Fungsional Akademik', 'H-Index', 'Minat Riset',
            'Link Kartu Dosen', 'Link SK JAD', 'Link SK PAK',
            'Link Registrasi Dosen', 'Link SK Inpassing',
            'Link Serdik', 'Link SKPP Serdos', 'Link Pernyataan Lolos Butuh',
            // Lamaran
            'Status Lamaran', 'Tanggal Melamar',
        ];
    }

    public function collection(): Collection
    {
        $this->lowongan->load(['lamarans.pelamar.user', 'lamarans']);
        return $this->lowongan->lamarans;
    }

    private function fileUrl(?string $path): string
    {
        return $path ? asset('storage/' . $path) : '';
    }

    public function map($lamaran): array
    {
        static $no = 0;
        $no++;
        $p = $lamaran->pelamar;

        $jfaLabels = [
            'guru_besar'    => 'Guru Besar (GB)',
            'lektor_kepala' => 'Lektor Kepala (LK)',
            'lektor'        => 'Lektor (L)',
            'asisten_ahli'  => 'Asisten Ahli (AA)',
            'non_jabatan'   => 'Non Jabatan (NJAD)',
        ];

        $statusLabels = [
            'menunggu'       => 'Menunggu',
            'seleksi_tahap1' => 'Seleksi Tahap 1',
            'seleksi_tahap2' => 'Seleksi Tahap 2',
            'diterima'       => 'Diterima',
            'ditolak'        => 'Ditolak',
        ];

        return [
            $no,
            $p->nama ?? '-',
            $p->nik ?? '-',
            $p->no_telepon ?? '-',
            $p->jenis_kelamin == 'L' ? 'Laki-laki' : ($p->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
            $p->tempat_lahir ?? '-',
            $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : '-',
            $p->kewarganegaraan ?? '-',
            $p->status_pernikahan ?? '-',
            $p->alamat_domisili ?? '-',
            $p->alamat_ktp ?? '-',
            // Pendidikan 1
            $p->jenjang ?? '-',
            $p->institusi ?? '-',
            $p->prodi_pendidikan ?? '-',
            $p->akreditas ?? '-',
            $p->no_ijazah ?? '-',
            $p->ipk ?? '-',
            $this->fileUrl($p->file_ijazah),
            $this->fileUrl($p->file_transkrip),
            // Pendidikan 2
            $p->jenjang_2 ?? '-',
            $p->institusi_2 ?? '-',
            $p->prodi_pendidikan_2 ?? '-',
            $p->akreditas_2 ?? '-',
            $p->no_ijazah_2 ?? '-',
            $p->ipk_2 ?? '-',
            $this->fileUrl($p->file_ijazah_2),
            $this->fileUrl($p->file_transkrip_2),
            // Pendidikan 3
            $p->jenjang_3 ?? '-',
            $p->institusi_3 ?? '-',
            $p->prodi_pendidikan_3 ?? '-',
            $p->akreditas_3 ?? '-',
            $p->no_ijazah_3 ?? '-',
            $p->ipk_3 ?? '-',
            $this->fileUrl($p->file_ijazah_3),
            $this->fileUrl($p->file_transkrip_3),
            // Dokumen Pendukung
            $this->fileUrl($p->file_cv),
            $this->fileUrl($p->file_pas_foto),
            $this->fileUrl($p->file_ktp),
            $p->kategori_sertifikat ?? '-',
            $this->fileUrl($p->file_sertifikat),
            $p->jenis_tes_bahasa ?? '-',
            $p->skor_bahasa ?? '-',
            $p->tanggal_tes_bahasa ? $p->tanggal_tes_bahasa->format('d/m/Y') : '-',
            $this->fileUrl($p->file_sertifikat_bahasa),
            $this->fileUrl($lamaran->file_surat_lamaran),
            $this->fileUrl($lamaran->file_sk_penyetaraan),
            $this->fileUrl($lamaran->file_surat_pemberhentian),
            // Akademik Dosen
            $p->nidn ?? '-',
            $p->homebase ?? '-',
            $jfaLabels[$p->jabatan_akademik] ?? ($p->jabatan_akademik ?? '-'),
            $p->h_index ?? '-',
            $p->minat_riset ?? '-',
            $this->fileUrl($p->file_kartu_dosen),
            $this->fileUrl($p->file_jad),
            $this->fileUrl($p->file_pak),
            $this->fileUrl($p->file_registrasi_dosen),
            $this->fileUrl($p->file_inpassing),
            $this->fileUrl($p->file_serdik),
            $this->fileUrl($p->file_skpp_serdos),
            $this->fileUrl($p->file_pernyataan_lolos_butuh),
            // Lamaran
            $statusLabels[$lamaran->status] ?? $lamaran->status,
            $lamaran->created_at->format('d/m/Y'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Convert 0-based column indexes to Excel column letters
                $colLetters = [];
                foreach ($this->urlColumnIndexes as $idx) {
                    // +1 because Excel columns are 1-based, then convert to letter
                    $colLetters[] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
                }

                // Loop through data rows (skip header row 1)
                for ($row = 2; $row <= $highestRow; $row++) {
                    foreach ($colLetters as $col) {
                        $cell = $sheet->getCell($col . $row);
                        $value = $cell->getValue();

                        if ($value && str_starts_with($value, 'http')) {
                            // Set as hyperlink with display text "Buka File"
                            $cell->setValue('Buka File');
                            $cell->getHyperlink()->setUrl($value);
                            $cell->getHyperlink()->setTooltip($value);

                            // Style: blue underline like a real link
                            $sheet->getStyle($col . $row)->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => '0563C1'],
                                    'underline' => Font::UNDERLINE_SINGLE,
                                ],
                            ]);
                        }
                    }
                }

                // Freeze header row
                $sheet->freezePane('A2');

                // Auto-filter on header
                $sheet->setAutoFilter('A1:' . $highestCol . '1');
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '8B1515']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 28,
            'C' => 18,
            'D' => 18,
            'E' => 14,
            'F' => 16,
            'G' => 14,
            'H' => 16,
            'I' => 18,
            'J' => 30,
            'K' => 30,
        ];
    }
}
