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
        Schema::table('lowongans', function (Blueprint $table) {
            if (!Schema::hasColumn('lowongans', 'materi_micro_teaching')) {
                $table->text('materi_micro_teaching')->nullable();
            }
        });

        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_seleksis', 'materi_micro_teaching')) {
                $table->text('materi_micro_teaching')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lowongans', function (Blueprint $table) {
            if (Schema::hasColumn('lowongans', 'materi_micro_teaching')) {
                $table->dropColumn('materi_micro_teaching');
            }
        });

        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_seleksis', 'materi_micro_teaching')) {
                $table->dropColumn('materi_micro_teaching');
            }
        });
    }
};
