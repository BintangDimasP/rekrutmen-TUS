<?php

namespace App\Console\Commands;

use App\Models\Dosen;
use App\Models\Pelamar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class EncryptExistingData extends Command
{
    protected $signature   = 'data:encrypt-existing';
    protected $description = 'Enkripsi data NIK dan No. Telepon yang sudah ada di database (jalankan sekali saja)';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info(' Enkripsi Data Sensitif yang Sudah Ada');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // ── PELAMARS ────────────────────────────────────────────────
        $this->info('[1/2] Mengenkripsi data Pelamar...');

        $pelamars     = \DB::table('pelamars')->get();
        $totalPelamar = $pelamars->count();
        $skippedPelamar = 0;
        $encryptedPelamar = 0;

        $bar = $this->output->createProgressBar($totalPelamar);
        $bar->start();

        foreach ($pelamars as $row) {
            $update = [];

            // Cek apakah NIK sudah terenkripsi
            // (Hasil enkripsi Laravel selalu diawali 'eyJpdiI6')
            if ($row->nik && ! $this->isEncrypted($row->nik)) {
                $update['nik'] = Crypt::encryptString($row->nik);
            }

            if ($row->no_telepon && ! $this->isEncrypted($row->no_telepon)) {
                $update['no_telepon'] = Crypt::encryptString($row->no_telepon);
            }

            if (! empty($update)) {
                \DB::table('pelamars')->where('id', $row->id)->update($update);
                $encryptedPelamar++;
            } else {
                $skippedPelamar++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("   ✓ {$encryptedPelamar} baris dienkripsi, {$skippedPelamar} sudah terenkripsi / dilewati.");
        $this->newLine();

        // ── DOSENS ──────────────────────────────────────────────────
        $this->info('[2/2] Mengenkripsi data Dosen...');

        $dosens        = \DB::table('dosens')->whereNotNull('no_telepon')->get();
        $totalDosen    = $dosens->count();
        $skippedDosen  = 0;
        $encryptedDosen = 0;

        $bar2 = $this->output->createProgressBar($totalDosen);
        $bar2->start();

        foreach ($dosens as $row) {
            if ($row->no_telepon && ! $this->isEncrypted($row->no_telepon)) {
                \DB::table('dosens')->where('id', $row->id)->update([
                    'no_telepon' => Crypt::encryptString($row->no_telepon),
                ]);
                $encryptedDosen++;
            } else {
                $skippedDosen++;
            }

            $bar2->advance();
        }

        $bar2->finish();
        $this->newLine();
        $this->line("   ✓ {$encryptedDosen} baris dienkripsi, {$skippedDosen} sudah terenkripsi / dilewati.");
        $this->newLine();

        $this->info('═══════════════════════════════════════════════════════');
        $this->info(' Selesai! Semua data sensitif berhasil dienkripsi.');
        $this->info('═══════════════════════════════════════════════════════');

        return self::SUCCESS;
    }

    /**
     * Deteksi apakah nilai sudah pernah dienkripsi oleh Laravel Crypt.
     * Hasil enkripsi Laravel adalah JSON base64, selalu diawali 'eyJ'.
     */
    private function isEncrypted(string $value): bool
    {
        // Coba decode → jika berhasil dan punya struktur iv/value/mac, sudah terenkripsi
        try {
            $decoded = json_decode(base64_decode($value), true);

            return is_array($decoded)
                && isset($decoded['iv'], $decoded['value'], $decoded['mac']);
        } catch (\Throwable) {
            return false;
        }
    }
}
