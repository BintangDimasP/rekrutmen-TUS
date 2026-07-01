<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\NotificationMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PhoneVerificationOtpController extends Controller
{
    use NotificationMessage;

    private const OTP_VALID_MINUTES = 5;

    private const OTP_RESEND_COOLDOWN = 60; // detik

    private const MAX_ATTEMPTS = 5;

    /**
     * Kirim OTP ke nomor WhatsApp pelamar.
     */
    public function sendOtp(Request $request)
    {
        // Tolak jika verifikasi WA dinonaktifkan
        if (! config('services.wappin.enabled')) {
            return response()->json(['message' => 'Fitur verifikasi WhatsApp sedang dinonaktifkan.'], 403);
        }

        $user = auth()->user();

        if (! $user || ! $user->pelamar) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pelamar = $user->pelamar;
        $phone = $pelamar->no_telepon;

        if (empty($phone)) {
            return response()->json(['message' => 'Nomor telepon belum diisi. Silakan lengkapi profil terlebih dahulu.'], 400);
        }

        if ($pelamar->phone_verified_at !== null) {
            return response()->json(['message' => 'Nomor telepon sudah terverifikasi.'], 400);
        }

        // Normalisasi nomor untuk key database
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '62' . substr($normalizedPhone, 1);
        }

        // Cek cooldown
        $lastOtp = DB::table('phone_verification_otps')
            ->where('phone', $normalizedPhone)
            ->latest('created_at')
            ->first();

        if ($lastOtp) {
            $createdAt = Carbon::parse($lastOtp->created_at);
            $elapsedSec = $createdAt->diffInSeconds(now(), false);

            if ($elapsedSec >= 0 && $elapsedSec < self::OTP_RESEND_COOLDOWN) {
                $waitSec = self::OTP_RESEND_COOLDOWN - $elapsedSec;

                return response()->json(['message' => "Tunggu {$waitSec} detik sebelum meminta kode baru."], 429);
            }
        }

        // Generate OTP 6 digit
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama
        DB::table('phone_verification_otps')->where('phone', $normalizedPhone)->delete();

        // Simpan record baru
        DB::table('phone_verification_otps')->insert([
            'phone' => $normalizedPhone,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::OTP_VALID_MINUTES),
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kirim OTP via WhatsApp (Fonnte)
        $result = $this->sendWhatsapp('-', $normalizedPhone, 'rekrutmen_informasi_send_otp', [$otp]);
        $sent = $result['status'] ?? false;

        if (! $sent) {
            DB::table('phone_verification_otps')->where('phone', $normalizedPhone)->delete();

            return response()->json(['message' => 'Gagal mengirim OTP via WhatsApp. Pastikan nomor aktif dan terhubung.'], 500);
        }

        return response()->json(['message' => 'OTP berhasil dikirim ke WhatsApp Anda.']);
    }

    /**
     * Verifikasi OTP yang diinput pelamar.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth()->user();

        if (! $user || ! $user->pelamar) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pelamar = $user->pelamar;

        if ($pelamar->phone_verified_at !== null) {
            return response()->json(['message' => 'Nomor telepon sudah terverifikasi.'], 400);
        }

        $phone = $pelamar->no_telepon;
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($normalizedPhone, '0')) {
            $normalizedPhone = '62' . substr($normalizedPhone, 1);
        }
        $inputOtp = $request->otp;

        $record = DB::table('phone_verification_otps')
            ->where('phone', $normalizedPhone)
            ->latest('created_at')
            ->first();

        if (! $record) {
            return response()->json(['message' => 'Kode OTP tidak ditemukan atau sudah kadaluarsa.'], 400);
        }

        if (now()->greaterThan($record->expires_at)) {
            DB::table('phone_verification_otps')->where('phone', $normalizedPhone)->delete();

            return response()->json(['message' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.'], 400);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            DB::table('phone_verification_otps')->where('phone', $normalizedPhone)->delete();

            return response()->json(['message' => 'Terlalu banyak percobaan salah. Silakan minta kode OTP baru.'], 400);
        }

        if (! Hash::check($inputOtp, $record->otp_hash)) {
            DB::table('phone_verification_otps')
                ->where('id', $record->id)
                ->increment('attempts');

            return response()->json(['message' => 'Kode OTP tidak valid.'], 400);
        }

        // OTP Valid
        DB::table('phone_verification_otps')->where('phone', $normalizedPhone)->delete();

        // Mark phone as verified
        $pelamar->phone_verified_at = now();
        $pelamar->save();

        session()->flash('success', 'Nomor WhatsApp berhasil diverifikasi!');

        return response()->json(['message' => 'Nomor WhatsApp berhasil diverifikasi!']);
    }
}
