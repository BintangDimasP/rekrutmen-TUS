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
            $table->string('file_sk_penyetaraan')->nullable()->after('file_surat_lamaran');
            $table->string('file_surat_pemberhentian')->nullable()->after('file_sk_penyetaraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->dropColumn(['file_sk_penyetaraan', 'file_surat_pemberhentian']);
        });
    }
};
