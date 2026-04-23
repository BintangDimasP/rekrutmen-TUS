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
        Schema::table('lamarans', function (Blueprint $table) {
            $table->date('tanggal_wawancara')->nullable()->after('status');
            $table->string('link_zoom')->nullable()->after('tanggal_wawancara');
            $table->text('catatan_admin')->nullable()->after('link_zoom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_wawancara', 'link_zoom', 'catatan_admin']);
        });
    }
};
