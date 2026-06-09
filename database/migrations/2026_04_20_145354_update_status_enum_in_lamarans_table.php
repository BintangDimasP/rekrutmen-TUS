<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // MySQL tidak bisa ALTER ENUM langsung dengan Doctrine, pakai raw SQL
            DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('menunggu','seleksi_tahap1','seleksi_tahap2','diterima','ditolak') NOT NULL DEFAULT 'menunggu'");
        } else {
            // SQLite (testing) tidak mendukung MODIFY ENUM — longgarkan jadi string
            Schema::table('lamarans', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lamarans MODIFY COLUMN status ENUM('menunggu','proses','diterima','ditolak') NOT NULL DEFAULT 'menunggu'");
        } else {
            Schema::table('lamarans', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->change();
            });
        }
    }
};
