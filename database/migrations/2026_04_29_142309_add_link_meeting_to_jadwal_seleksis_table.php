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
        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            $table->string('link_meeting')->nullable()->after('sesi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_seleksis', function (Blueprint $table) {
            $table->dropColumn('link_meeting');
        });
    }
};
