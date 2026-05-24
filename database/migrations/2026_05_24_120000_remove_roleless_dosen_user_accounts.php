<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hapus akun user dosen yang role-nya null (dosen biasa yang tidak punya jabatan apapun).
     * Dosen biasa tidak seharusnya memiliki akun di tabel users.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('dosen_id')
            ->whereNull('role')
            ->delete();
    }

    public function down(): void
    {
        // Tidak reversibel — akun-akun ini memang tidak seharusnya ada
    }
};
