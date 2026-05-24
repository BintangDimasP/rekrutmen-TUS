<?php

use App\Models\Dosen;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Reset password user dosen aktif (penguji/kaprodi/rangkap) ke 'dosen123'.
     * Dosen yang belum punya role aktif dibiarkan dengan password random
     * supaya akunnya tidak bisa login sebelum ditunjuk role.
     */
    public function up(): void
    {
        $default = Dosen::DEFAULT_PASSWORD; // 'dosen123'
        $hashed  = Hash::make($default);

        DB::table('users')
            ->whereNotNull('dosen_id')
            ->whereIn('role', ['penguji', 'kaprodi'])
            ->update([
                'password'       => $hashed,
                'password_plain' => $default,
                'updated_at'     => now(),
            ]);
    }

    public function down(): void
    {
        // Tidak reversibel — password lama tidak disimpan
    }
};
