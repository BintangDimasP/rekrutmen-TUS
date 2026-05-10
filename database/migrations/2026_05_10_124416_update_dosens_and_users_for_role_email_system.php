<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop unique constraint on dosens.email (all dosen biasa will have '-')
        Schema::table('dosens', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('email')->nullable()->change();
        });

        // 2. Add dosen_id to users table to link user accounts to dosen
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('dosen_id')->nullable()->after('prodi_id');
            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
        });

        // 3. Drop penguji_password from users (no longer needed — separate accounts per role)
        if (Schema::hasColumn('users', 'penguji_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('penguji_password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('penguji_password')->nullable()->after('password');
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });

        Schema::table('dosens', function (Blueprint $table) {
            $table->string('email')->unique()->change();
        });
    }
};
