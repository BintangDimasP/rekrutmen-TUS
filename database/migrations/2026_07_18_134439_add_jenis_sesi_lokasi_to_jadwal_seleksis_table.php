<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            $table->enum('jenis_sesi', ['online', 'offline'])->default('online')->after('link_meeting');
            $table->string('lokasi')->nullable()->after('jenis_sesi');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            $table->dropColumn(['jenis_sesi', 'lokasi']);
        });
    }
};
