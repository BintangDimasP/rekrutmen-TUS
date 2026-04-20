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
            // Level 2
            $table->enum('jenjang_2', ['S1', 'S2', 'S3'])->nullable()->after('file_transkrip');
            $table->string('institusi_2')->nullable()->after('jenjang_2');
            $table->string('prodi_pendidikan_2')->nullable()->after('institusi_2');
            $table->decimal('ipk_2', 3, 2)->nullable()->after('prodi_pendidikan_2');
            $table->string('file_ijazah_2')->nullable()->after('ipk_2');
            $table->string('file_transkrip_2')->nullable()->after('file_ijazah_2');

            // Level 3
            $table->enum('jenjang_3', ['S1', 'S2', 'S3'])->nullable()->after('file_transkrip_2');
            $table->string('institusi_3')->nullable()->after('jenjang_3');
            $table->string('prodi_pendidikan_3')->nullable()->after('institusi_3');
            $table->decimal('ipk_3', 3, 2)->nullable()->after('prodi_pendidikan_3');
            $table->string('file_ijazah_3')->nullable()->after('ipk_3');
            $table->string('file_transkrip_3')->nullable()->after('file_ijazah_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->dropColumn([
                'jenjang_2', 'institusi_2', 'prodi_pendidikan_2', 'ipk_2', 'file_ijazah_2', 'file_transkrip_2',
                'jenjang_3', 'institusi_3', 'prodi_pendidikan_3', 'ipk_3', 'file_ijazah_3', 'file_transkrip_3'
            ]);
        });
    }
};
