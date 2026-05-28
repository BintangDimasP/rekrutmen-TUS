<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel OTP untuk forgot password.
 * Terpisah dari password_reset_tokens (bawaan Laravel) agar tidak konflik.
 *
 * Flow:
 *  1. User minta reset → record dibuat dengan otp_hash + expires_at
 *  2. User masukkan OTP → diverifikasi, lalu verified_at di-set
 *  3. User submit password baru → record dihapus
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp_hash');                    // hash 6-digit OTP
            $table->timestamp('expires_at');               // batas berlaku OTP
            $table->timestamp('verified_at')->nullable();  // ditandai saat OTP cocok
            $table->string('reset_token', 64)->nullable()->index(); // token untuk step terakhir
            $table->unsignedTinyInteger('attempts')->default(0);    // anti brute-force
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
