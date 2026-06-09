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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('menunggu','seleksi_tahap1','seleksi_tahap2','diterima','ditolak','mengundurkan_diri') NOT NULL DEFAULT 'menunggu'");
        } else {
            // SQLite (testing) — kolom sudah berupa string, pastikan tetap string
            Schema::table('lamarans', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('menunggu','seleksi_tahap1','seleksi_tahap2','diterima','ditolak') NOT NULL DEFAULT 'menunggu'");
        } else {
            Schema::table('lamarans', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->change();
            });
        }
    }
};
