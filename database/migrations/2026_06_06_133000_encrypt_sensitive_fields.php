<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus kolom password_plain dari users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'password_plain')) {
                $table->dropColumn('password_plain');
            }
        });

        // 2. Ubah nik dan no_telepon di pelamars menjadi TEXT
        //    agar bisa menampung hasil enkripsi yang panjang
        Schema::table('pelamars', function (Blueprint $table) {
            $table->text('nik')->change();
            $table->text('no_telepon')->nullable()->change();
        });

        // 3. Ubah no_telepon di dosens menjadi TEXT
        Schema::table('dosens', function (Blueprint $table) {
            $table->text('no_telepon')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password_plain')->nullable();
        });

        Schema::table('pelamars', function (Blueprint $table) {
            $table->string('nik', 16)->change();
            $table->string('no_telepon', 20)->nullable()->change();
        });

        Schema::table('dosens', function (Blueprint $table) {
            $table->string('no_telepon', 20)->nullable()->change();
        });
    }
};
