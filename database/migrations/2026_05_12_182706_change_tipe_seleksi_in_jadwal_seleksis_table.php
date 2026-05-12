<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify column type to VARCHAR first
        DB::statement("ALTER TABLE jadwal_seleksis MODIFY tipe_seleksi VARCHAR(50)");

        // Update existing data
        DB::statement("UPDATE jadwal_seleksis SET tipe_seleksi = 'wawancara' WHERE tipe_seleksi = 'tahap1'");
        DB::statement("UPDATE jadwal_seleksis SET tipe_seleksi = 'micro_teaching' WHERE tipe_seleksi = 'tahap2'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data first
        DB::statement("UPDATE jadwal_seleksis SET tipe_seleksi = 'tahap1' WHERE tipe_seleksi = 'wawancara'");
        DB::statement("UPDATE jadwal_seleksis SET tipe_seleksi = 'tahap2' WHERE tipe_seleksi = 'micro_teaching'");

        // Revert column type to ENUM
        DB::statement("ALTER TABLE jadwal_seleksis MODIFY tipe_seleksi ENUM('tahap1', 'tahap2')");
    }
};
