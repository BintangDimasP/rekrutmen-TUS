<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationOtpMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationOtpController extends Controller
{
    private const OTP_VALID_MINUTES = 5;

    private const OTP_RESEND_COOLDOWN = 60; // detik

    private const MAX_ATTEMPTS = 5;

    public function sendOtp(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $email = $user->email;

        // Cek cooldown
        $lastOtp = DB::table('email_verification_otps')
            ->where('email', $email)
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
        DB::table('email_verification_otps')->where('email', $email)->delete();

        // Simpan record baru
        DB::table('email_verification_otps')->insert([
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::OTP_VALID_MINUTES),
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($email)->send(new EmailVerificationOtpMail($otp, self::OTP_VALID_MINUTES, $user->name));
        } catch (\Throwable $e) {
            DB::table('email_verification_otps')->where('email', $email)->delete();

            return response()->json(['message' => 'Gagal mengirim email OTP. Silakan coba lagi.'], 500);
        }

        return response()->json(['message' => 'OTP berhasil dikirim ke email Anda.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $email = $user->email;
        $inputOtp = $request->otp;

        $record = DB::table('email_verification_otps')
            ->where('email', $email)
            ->latest('created_at')
            ->first();

        if (! $record) {
            return response()->json(['message' => 'Kode OTP tidak ditemukan atau sudah kadaluarsa.'], 400);
        }

        if (now()->greaterThan($record->expires_at)) {
            DB::table('email_verification_otps')->where('email', $email)->delete();

            return response()->json(['message' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.'], 400);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            DB::table('email_verification_otps')->where('email', $email)->delete();

            return response()->json(['message' => 'Terlalu banyak percobaan salah. Silakan minta kode OTP baru.'], 400);
        }

        if (! Hash::check($inputOtp, $record->otp_hash)) {
            DB::table('email_verification_otps')
                ->where('id', $record->id)
                ->increment('attempts');

            return response()->json(['message' => 'Kode OTP tidak valid.'], 400);
        }

        // OTP Valid
        DB::table('email_verification_otps')->where('email', $email)->delete();

        // Mark as verified
        $user->email_verified_at = now();
        $user->save();

        session()->flash('success', 'Email berhasil diverifikasi!');

        return response()->json(['message' => 'Email berhasil diverifikasi!']);
    }
}
