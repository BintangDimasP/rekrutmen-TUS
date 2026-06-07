<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('menunggu','seleksi_tahap1','seleksi_tahap2','diterima','ditolak','mengundurkan_diri') NOT NULL DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('menunggu','seleksi_tahap1','seleksi_tahap2','diterima','ditolak') NOT NULL DEFAULT 'menunggu'");
    }
};
