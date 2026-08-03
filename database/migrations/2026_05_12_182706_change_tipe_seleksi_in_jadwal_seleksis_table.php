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
        DB::table('jadwal_seleksis')->where('tipe_seleksi', 'tahap1')->update(['tipe_seleksi' => 'wawancara']);
        DB::table('jadwal_seleksis')->where('tipe_seleksi', 'tahap2')->update(['tipe_seleksi' => 'micro_teaching']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data first
        DB::table('jadwal_seleksis')->where('tipe_seleksi', 'wawancara')->update(['tipe_seleksi' => 'tahap1']);
        DB::table('jadwal_seleksis')->where('tipe_seleksi', 'micro_teaching')->update(['tipe_seleksi' => 'tahap2']);

        // Revert column type to ENUM
        DB::statement("ALTER TABLE jadwal_seleksis MODIFY tipe_seleksi ENUM('tahap1', 'tahap2')");
    }
};
