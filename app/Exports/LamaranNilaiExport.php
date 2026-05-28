<?php

namespace App\Exports;

use App\Models\JadwalSeleksi;
use App\Models\Lowongan;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LamaranNilaiExport implements WithMultipleSheets
{
    protected Lowongan $lowongan;
    protected array $data; // pre-computed, shared across sheets

    public function __construct(Lowongan $lowongan)
    {
        $this->lowongan = $lowongan;
        $this->data     = $this->buildData();
    }

    public function sheets(): array
    {
        return [
            new Sheets\MicroDataSheet($this->data),
            new Sheets\WawancaraDataSheet($this->data),
            new Sheets\KualifikasiSheet($this->data),
            new Sheets\HasilAkhirSheet($this->data, $this->lowongan),
        ];
    }

    // ── Build shared data ────────────────────────────────────────────────────

    private function buildData(): array
    {
        $this->lowongan->load(['lamarans.pelamar', 'prodi']);

        $microRows     = [];
        $wawancaraRows = [];

        $jfaSkorMap  = ['guru_besar' => 5, 'lektor_kepala' => 4, 'lektor' => 3, 'asisten_ahli' => 2, 'non_jabatan' => 1];
        $jfaLabelMap = ['guru_besar' => 'Guru Besar (GB)', 'lektor_kepala' => 'Lektor Kepala (LK)', 'lektor' => 'Lektor (L)', 'asisten_ahli' => 'Asisten Ahli (AA)', 'non_jabatan' => 'Non Jabatan (NJAD)'];
        $statusRekMap = ['on_going' => 'On Going', 'praktisi_part_time' => 'Praktisi Part Time', 'profesional_full_time' => 'Profesional Full Time'];
        $rekMap       = ['direkomendasikan' => 'Direkomendasikan', 'tidak_direkomendasikan' => 'Tidak Direkomendasikan', 'perlu_dipertimbangkan' => 'Perlu Dipertimbangkan'];

        foreach ($this->lowongan->lamarans as $lamaran) {
            // Use snapshot data (data saat melamar) for profile fields
            $pelamar    = $lamaran->effectivePelamar;
            $pelamarId  = $lamaran->pelamar_id;

            $jadwals = JadwalSeleksi::with(['penguji', 'penilaian'])
                ->where('pelamar_id', $pelamarId)
                ->where('lowongan_id', $this->lowongan->id)
                ->get();

            // ── Micro rows ──────────────────────────────────────────────────
            foreach ($jadwals->where('tipe_seleksi', 'micro_teaching') as $j) {
                if (!$j->penilaian) continue;
                $p  = $j->penilaian;
                $dv = $p->detail_nilai ?? [];

                // 15 items: k1(2), k2(3), k3(6), k4(3), k5(1)
                $items = [];
                foreach ([1=>2, 2=>3, 3=>6, 4=>3, 5=>1] as $k => $cnt) {
                    for ($i = 1; $i <= $cnt; $i++) {
                        $raw = $dv["k{$k}_item_{$i}"] ?? null;
                        $items[] = is_numeric($raw) ? (float) $raw : '-';
                    }
                }

                // Use stored decimal category scores for accurate SUM/AVG
                $catScores = array_filter([
                    (float) ($p->kategori_1 ?? 0),
                    (float) ($p->kategori_2 ?? 0),
                    (float) ($p->kategori_3 ?? 0),
                    (float) ($p->kategori_4 ?? 0),
                    (float) ($p->kategori_5 ?? 0),
                ]);
                $sum = round(array_sum($catScores), 2);
                $avg = count($catScores) > 0 ? round($sum / count($catScores), 2) : '-';

                $microRows[] = [
                    'penguji'           => $j->penguji->nama ?? '-',
                    'kandidat'          => $pelamar->nama,
                    'prodi_tujuan'      => $p->prodi_tujuan ?? '-',
                    'items'             => $items,
                    'catatan'           => $p->catatan ?? '',
                    'rekomendasi'       => $rekMap[$p->rekomendasi] ?? ($p->rekomendasi ?? '-'),
                    'prodi_rek'         => $p->prodi_tujuan ?? '-',
                    'kelompok'          => strtoupper($p->kelompok_keahlian ?? '-'),
                    'bidang'            => $p->bidang_keahlian ?? '-',
                    'sum'               => $sum,
                    'avg'               => $avg,
                    'kategori_1'        => (float) $p->kategori_1,
                    'kategori_2'        => (float) $p->kategori_2,
                    'kategori_3'        => (float) $p->kategori_3,
                    'kategori_4'        => (float) $p->kategori_4,
                    'kategori_5'        => (float) $p->kategori_5,
                    'total_nilai'       => (float) $p->total_nilai,
                ];
            }

            // ── Wawancara rows ──────────────────────────────────────────────
            foreach ($jadwals->where('tipe_seleksi', 'wawancara') as $j) {
                if (!$j->penilaian) continue;
                $p  = $j->penilaian;
                $dv = $p->detail_nilai ?? [];

                $items = [];
                for ($i = 1; $i <= 8; $i++) {
                    $raw = $dv["k1_item_{$i}"] ?? null;
                    $items[] = is_numeric($raw) ? (float) $raw : '-';
                }
                $numericItems = array_filter($items, 'is_numeric');
                $sum = count($numericItems) > 0 ? round(array_sum($numericItems), 2) : '-';
                // Use stored decimal total_nilai for accurate average
                $avg = (float) ($p->total_nilai ?? 0);

                $wawancaraRows[] = [
                    'penguji'      => $j->penguji->nama ?? '-',
                    'kandidat'     => $pelamar->nama,
                    'prodi_tujuan' => $p->prodi_tujuan ?? '-',
                    'items'        => $items,
                    'catatan'      => $p->catatan ?? '',
                    'rekomendasi'  => $rekMap[$p->rekomendasi] ?? ($p->rekomendasi ?? '-'),
                    'prodi_rek'    => $p->prodi_tujuan ?? '-',
                    'sum'          => $sum,
                    'avg'          => $avg,
                    'total_nilai'  => (float) $p->total_nilai,
                    'status_rekrutmen' => $statusRekMap[$p->status_rekrutmen] ?? '-',
                ];
            }
        }

        // ── Kualifikasi per kandidat ────────────────────────────────────────
        $kualifikasiRows = [];
        $no = 1;
        foreach ($this->lowongan->lamarans as $lamaran) {
            // Use snapshot data (data saat melamar) for profile fields
            $pelamar    = $lamaran->effectivePelamar;
            $pelamarId  = $lamaran->pelamar_id;

            $jadwals = JadwalSeleksi::with(['penilaian'])
                ->where('pelamar_id', $pelamarId)
                ->where('lowongan_id', $this->lowongan->id)
                ->get();

            $wawancaraDinilai = $jadwals->where('tipe_seleksi', 'wawancara')
                ->filter(fn($j) => $j->penilaian !== null)->values();

            $statusRekrutmenNilai = $wawancaraDinilai->first()?->penilaian?->status_rekrutmen ?? null;

            // Jenjang tertinggi — determine by actual content, not field order
            $jenjangPriority = function ($val) {
                if (!$val) return 0;
                $v = strtolower(trim($val));
                if (str_contains($v, 's3') || str_contains($v, 'doktor')) return 3;
                if (str_contains($v, 's2') || str_contains($v, 'magister') || str_contains($v, 'master')) return 2;
                return 1; // S1, D4, or other
            };
            $allJenjang = [
                ['val' => $pelamar->jenjang,   'priority' => $jenjangPriority($pelamar->jenjang)],
                ['val' => $pelamar->jenjang_2, 'priority' => $jenjangPriority($pelamar->jenjang_2)],
                ['val' => $pelamar->jenjang_3, 'priority' => $jenjangPriority($pelamar->jenjang_3)],
            ];
            $highest = collect($allJenjang)->filter(fn($j) => $j['val'])->sortByDesc('priority')->first();
            $jenjangTertinggi = $highest ? strtolower(trim($highest['val'])) : null;
            $isS3 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's3') || str_contains($jenjangTertinggi, 'doktor'));
            $isS2 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's2') || str_contains($jenjangTertinggi, 'magister') || str_contains($jenjangTertinggi, 'master'));
            $jenjangDisplay = $isS3 ? 'S3' : ($isS2 ? 'S2' : ($highest['val'] ?? '-'));

            $sptSkor = 0;
            $sptLabel = '-';
            if ($isS3) {
                $sptSkor  = match($statusRekrutmenNilai) { 'profesional_full_time' => 5, 'praktisi_part_time' => 4, default => 3 };
                $sptLabel = match($statusRekrutmenNilai) { 'profesional_full_time' => 'S3 Profesional Full Time', 'praktisi_part_time' => 'S3 Praktisi Part Time', default => 'S3 On Going' };
            } elseif ($isS2) {
                $sptSkor  = $statusRekrutmenNilai === 'profesional_full_time' ? 2 : 1;
                $sptLabel = $statusRekrutmenNilai === 'profesional_full_time' ? 'S2 Profesional Full Time' : 'S2 Praktisi Part Time';
            }

            $jfaKey   = $pelamar->jabatan_akademik ?? 'non_jabatan';
            $jfaSkor  = $jfaSkorMap[$jfaKey] ?? 1;
            $jfaLabel = $jfaLabelMap[$jfaKey] ?? 'Non Jabatan (NJAD)';

            $hIndex = (int)($pelamar->h_index ?? 0);
            $hSkor  = match(true) { $hIndex > 10 => 5, $hIndex >= 5 => 4, $hIndex >= 2 => 3, $hIndex >= 1 => 2, default => 1 };

            $sum = $sptSkor + $jfaSkor + $hSkor;
            $avg = $sum > 0 ? round($sum / 3, 4) : 0;

            // Bidang keahlian dari micro penilaian
            $microPenilaian = $jadwals->where('tipe_seleksi', 'micro_teaching')
                ->filter(fn($j) => $j->penilaian !== null)->first()?->penilaian;

            $kualifikasiRows[] = [
                'no'           => $no++,
                'nama'         => $pelamar->nama,
                'bidang'       => $microPenilaian?->bidang_keahlian ?? '-',
                'jenjang'      => $jenjangDisplay,
                'status'       => $statusRekMap[$statusRekrutmenNilai] ?? '-',
                'spt_label'    => $sptLabel,
                'spt_skor'     => $sptSkor,
                'jfa_label'    => $jfaLabel,
                'jfa_skor'     => $jfaSkor,
                'h_index'      => $hIndex,
                'h_skor'       => $hSkor,
                'sum'          => $sum,
                'avg'          => $avg,
            ];
        }

        return compact('microRows', 'wawancaraRows', 'kualifikasiRows');
    }
}
