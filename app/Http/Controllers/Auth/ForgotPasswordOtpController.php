<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * ForgotPasswordOtpController
 *
 * Flow 3 langkah berbasis OTP:
 *  1. showEmailForm  → user input email
 *     sendOtp        → validasi email terdaftar, generate OTP 6-digit, kirim ke email,
 *                      simpan hash + expires_at di tabel password_reset_otps
 *  2. showOtpForm    → tampilkan halaman input OTP
 *     verifyOtp      → validasi OTP, set verified_at + reset_token
 *  3. showResetForm  → tampilkan form password baru (butuh reset_token valid)
 *     resetPassword  → update password user, hapus record OTP, redirect ke login
 *
 * Role yang bisa pakai: pelamar, penguji, kaprodi (semua role yang login pakai email).
 * Admin TIDAK bisa pakai forgot password — admin harus reset via cara internal.
 */
class ForgotPasswordOtpController extends Controller
{
    private const OTP_VALID_MINUTES   = 1;
    private const OTP_RESEND_COOLDOWN = 60; // detik
    private const MAX_ATTEMPTS        = 5;
    private const ALLOWED_ROLES       = ['pelamar', 'penguji', 'kaprodi'];

    /**
     * Domain email internal yang tidak boleh reset password sendiri.
     */
    private const BLOCKED_DOMAINS = [
        'admin.telkomuniversity.ac.id',
        'pengajar.telkomuniversity.ac.id',
        'penguji.telkomuniversity.ac.id',
        'kaprodi.telkomuniversity.ac.id',
    ];

    // ═══════════════════════════════════════════════════════════════
    // STEP 1: Email
    // ═══════════════════════════════════════════════════════════════

    public function showEmailForm()
    {
        return view('auth.forgot-password.email');
    }

    /**
     * Tampilkan halaman blokir untuk domain internal.
     */
    public function showBlockedPage()
    {
        return view('auth.forgot-password.blocked');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $email = strtolower(trim($request->email));

        // Cek apakah email menggunakan domain internal (admin/dosen) → blokir
        foreach (self::BLOCKED_DOMAINS as $domain) {
            if (str_ends_with($email, '@' . $domain)) {
                return redirect()->route('password.otp.blocked');
            }
        }

        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        // Jika user tidak ada atau role tidak diizinkan → tetap kembalikan error sama
        // (jangan bocorkan info bahwa email ada tapi role admin)
        if (!$user || !in_array($user->role, self::ALLOWED_ROLES)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak terdaftar di sistem.']);
        }

        // Cek cooldown: jangan boleh kirim ulang dalam 60 detik
        $lastOtp = DB::table('password_reset_otps')
            ->where('email', $email)
            ->latest('created_at')
            ->first();

        if ($lastOtp) {
            $createdAt  = \Carbon\Carbon::parse($lastOtp->created_at);
            $elapsedSec = $createdAt->diffInSeconds(now(), false); // false = signed (bisa negatif)

            if ($elapsedSec >= 0 && $elapsedSec < self::OTP_RESEND_COOLDOWN) {
                // OTP masih dalam cooldown — langsung arahkan ke step 2 tanpa kirim ulang
                session(['fp_email' => $email]);
                return redirect()
                    ->route('password.otp.form')
                    ->with('info', 'Kode OTP sudah dikirim. Silakan cek email Anda.');
            }
        }

        // Generate OTP 6 digit
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama untuk email ini (bersih-bersih)
        DB::table('password_reset_otps')->where('email', $email)->delete();

        // Simpan record baru
        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(self::OTP_VALID_MINUTES),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kirim email
        try {
            Mail::to($email)->send(new PasswordResetOtpMail($otp, self::OTP_VALID_MINUTES, $user->name));
        } catch (\Throwable $e) {
            // Hapus record agar user bisa coba lagi tanpa cooldown
            DB::table('password_reset_otps')->where('email', $email)->delete();

            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal mengirim email. Coba lagi beberapa saat.']);
        }

        // Simpan email di session untuk step berikutnya
        session(['fp_email' => $email]);

        return redirect()
            ->route('password.otp.form')
            ->with('success', 'Kode OTP telah dikirim ke email Anda. Cek inbox atau folder spam.');
    }

    // ═══════════════════════════════════════════════════════════════
    // STEP 2: OTP
    // ═══════════════════════════════════════════════════════════════

    public function showOtpForm()
    {
        if (!session('fp_email')) {
            return redirect()->route('password.otp.email');
        }

        $email = session('fp_email');

        // Hitung sisa cooldown agar countdown di view mulai dari waktu yang tepat
        $remainingCooldown = 0;
        $lastOtp = DB::table('password_reset_otps')
            ->where('email', $email)
            ->latest('created_at')
            ->first();

        if ($lastOtp) {
            $createdAt  = \Carbon\Carbon::parse($lastOtp->created_at);
            $elapsedSec = $createdAt->diffInSeconds(now(), false);
            if ($elapsedSec >= 0 && $elapsedSec < self::OTP_RESEND_COOLDOWN) {
            $remainingCooldown = self::OTP_RESEND_COOLDOWN - (int) $elapsedSec;
            }
        }

        return view('auth.forgot-password.otp', [
            'email'             => $email,
            'remainingCooldown' => $remainingCooldown,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $email = session('fp_email');
        if (!$email) {
            return redirect()->route('password.otp.email');
        }

        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits'   => 'Kode OTP harus 6 digit angka.',
        ]);

        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->latest('created_at')
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP tidak ditemukan. Minta kode baru.']);
        }

        if (now()->greaterThan($record->expires_at)) {
            DB::table('password_reset_otps')->where('id', $record->id)->delete();
            return back()->withErrors(['otp' => 'Kode OTP sudah kedaluwarsa. Minta kode baru.']);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            DB::table('password_reset_otps')->where('id', $record->id)->delete();
            session()->forget('fp_email');
            return redirect()
                ->route('password.otp.email')
                ->withErrors(['email' => 'Terlalu banyak percobaan gagal. Mulai ulang dari awal.']);
        }

        if (!Hash::check($request->otp, $record->otp_hash)) {
            DB::table('password_reset_otps')
                ->where('id', $record->id)
                ->update(['attempts' => $record->attempts + 1, 'updated_at' => now()]);

            $sisa = self::MAX_ATTEMPTS - ($record->attempts + 1);
            return back()->withErrors([
                'otp' => "Kode OTP salah. Sisa percobaan: {$sisa}.",
            ]);
        }

        // OTP cocok — generate reset_token untuk step terakhir
        // Perpanjang expires_at 10 menit agar user punya waktu cukup mengisi password baru
        $resetToken = Str::random(64);

        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update([
                'verified_at' => now(),
                'reset_token' => $resetToken,
                'expires_at'  => now()->addMinutes(10),
                'updated_at'  => now(),
            ]);

        session(['fp_reset_token' => $resetToken]);

        return redirect()->route('password.otp.reset.form');
    }

    // ═══════════════════════════════════════════════════════════════
    // STEP 3: Reset password
    // ═══════════════════════════════════════════════════════════════

    public function showResetForm()
    {
        $token = session('fp_reset_token');
        $email = session('fp_email');

        if (!$token || !$email) {
            return redirect()->route('password.otp.email');
        }

        // Validasi token masih valid
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('reset_token', $token)
            ->whereNotNull('verified_at')
            ->first();

        if (!$record || now()->greaterThan($record->expires_at)) {
            session()->forget(['fp_email', 'fp_reset_token']);
            return redirect()
                ->route('password.otp.email')
                ->withErrors(['email' => 'Sesi reset password sudah berakhir. Mulai ulang dari awal.']);
        }

        return view('auth.forgot-password.reset', ['email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $token = session('fp_reset_token');
        $email = session('fp_email');

        if (!$token || !$email) {
            return redirect()->route('password.otp.email');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        // Verifikasi token sekali lagi
        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('reset_token', $token)
            ->whereNotNull('verified_at')
            ->first();

        if (!$record || now()->greaterThan($record->expires_at)) {
            session()->forget(['fp_email', 'fp_reset_token']);
            return redirect()
                ->route('password.otp.email')
                ->withErrors(['email' => 'Sesi reset password sudah berakhir. Mulai ulang dari awal.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user || !in_array($user->role, self::ALLOWED_ROLES)) {
            session()->forget(['fp_email', 'fp_reset_token']);
            return redirect()
                ->route('password.otp.email')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Bersih-bersih
        DB::table('password_reset_otps')->where('email', $email)->delete();
        session()->forget(['fp_email', 'fp_reset_token']);

        return redirect()
            ->route('login')
            ->with('success', 'Password berhasil diubah. Silakan masuk dengan password baru Anda.');
    }
}
