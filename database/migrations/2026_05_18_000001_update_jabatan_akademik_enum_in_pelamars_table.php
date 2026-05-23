<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah nilai lama 'profesor' -> 'guru_besar' sebelum alter enum
        DB::table('pelamars')
            ->where('jabatan_akademik', 'profesor')
            ->update(['jabatan_akademik' => 'guru_besar']);

        DB::statement("ALTER TABLE pelamars MODIFY COLUMN jabatan_akademik ENUM('asisten_ahli','lektor','lektor_kepala','guru_besar','non_jabatan') NULL");
    }

    public function down(): void
    {
        // Kembalikan nilai 'guru_besar' -> 'profesor' sebelum revert
        DB::table('pelamars')
            ->where('jabatan_akademik', 'guru_besar')
            ->update(['jabatan_akademik' => 'profesor']);

        // Hapus 'non_jabatan' (set null dulu karena tidak ada di enum lama)
        DB::table('pelamars')
            ->where('jabatan_akademik', 'non_jabatan')
            ->update(['jabatan_akademik' => null]);

        DB::statement("ALTER TABLE pelamars MODIFY COLUMN jabatan_akademik ENUM('asisten_ahli','lektor','lektor_kepala','profesor') NULL");
    }
};
