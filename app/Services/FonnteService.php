<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Kirim pesan WhatsApp via Fonnte API.
     *
     * @param  string  $target   Nomor telepon tujuan (format 08xxx)
     * @param  string  $message  Isi pesan (mendukung *bold*, _italic_)
     * @return bool
     */
    public static function send(string $target, string $message): bool
    {
        // Cek apakah fitur diaktifkan
        if (!config('services.fonnte.enabled')) {
            return false;
        }

        $token = config('services.fonnte.token');

        if (empty($token)) {
            Log::warning('Fonnte: token belum dikonfigurasi.');
            return false;
        }

        // Normalisasi nomor: pastikan diawali 62 (format internasional)
        $target = self::normalizePhoneNumber($target);

        if (empty($target)) {
            Log::warning('Fonnte: nomor telepon tidak valid.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['status']) && $body['status'] === true) {
                    Log::info("Fonnte: pesan terkirim ke {$target}");
                    return true;
                }

                Log::warning("Fonnte: gagal kirim ke {$target}", $body);
                return false;
            }

            Log::error("Fonnte: HTTP error {$response->status()} ke {$target}");
            return false;

        } catch (\Exception $e) {
            Log::error("Fonnte: exception — {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Normalisasi nomor telepon ke format 628xxx.
     * Input bisa: 08xxx, +628xxx, 628xxx
     */
    public static function normalizePhoneNumber(string $phone): string
    {
        // Hapus spasi, strip, dan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return '';
        }

        // 08xxx → 628xxx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Validasi minimal panjang nomor Indonesia
        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return '';
        }

        return $phone;
    }
}
