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
        Schema::table('pelamars', function (Blueprint $table) {
            $table->string('file_jad')->nullable()->after('file_kartu_dosen');
            $table->string('file_pak')->nullable()->after('file_jad');
            $table->string('file_registrasi_dosen')->nullable()->after('file_pak');
            $table->string('file_inpassing')->nullable()->after('file_registrasi_dosen');
            $table->string('file_serdik')->nullable()->after('file_inpassing');
            $table->string('file_skpp_serdos')->nullable()->after('file_serdik');
            $table->string('file_pernyataan_lolos_butuh')->nullable()->after('file_skpp_serdos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->dropColumn([
                'file_jad',
                'file_pak',
                'file_registrasi_dosen',
                'file_inpassing',
                'file_serdik',
                'file_skpp_serdos',
                'file_pernyataan_lolos_butuh'
            ]);
        });
    }
};
