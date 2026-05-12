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
            $table->string('kewarganegaraan')->nullable()->after('jenis_kelamin');
            $table->string('status_pernikahan')->nullable()->after('kewarganegaraan');
            $table->text('alamat_domisili')->nullable()->after('status_pernikahan');
            $table->text('alamat_ktp')->nullable()->after('alamat_domisili');
            
            $table->string('akreditas')->nullable()->after('prodi_pendidikan');
            $table->string('no_ijazah')->nullable()->after('akreditas');
            
            $table->string('akreditas_2')->nullable()->after('prodi_pendidikan_2');
            $table->string('no_ijazah_2')->nullable()->after('akreditas_2');
            
            $table->string('akreditas_3')->nullable()->after('prodi_pendidikan_3');
            $table->string('no_ijazah_3')->nullable()->after('akreditas_3');
        });

        // Migrate data
        \Illuminate\Support\Facades\DB::table('pelamars')->update([
            'alamat_domisili' => \Illuminate\Support\Facades\DB::raw('alamat'),
            'alamat_ktp' => \Illuminate\Support\Facades\DB::raw('alamat')
        ]);

        Schema::table('pelamars', function (Blueprint $table) {
            $table->text('alamat')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->dropColumn([
                'kewarganegaraan',
                'status_pernikahan',
                'alamat_domisili',
                'alamat_ktp',
                'akreditas',
                'no_ijazah',
                'akreditas_2',
                'no_ijazah_2',
                'akreditas_3',
                'no_ijazah_3'
            ]);
        });
    }
};
