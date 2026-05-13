<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->decimal('kategori_1', 4, 2)->change();
            $table->decimal('kategori_2', 4, 2)->change();
            $table->decimal('kategori_3', 4, 2)->change();
            $table->decimal('kategori_4', 4, 2)->nullable()->change();
            $table->decimal('kategori_5', 4, 2)->nullable()->change();
            $table->decimal('total_nilai', 4, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->integer('kategori_1')->change();
            $table->integer('kategori_2')->change();
            $table->integer('kategori_3')->change();
            $table->integer('kategori_4')->nullable()->change();
            $table->integer('kategori_5')->nullable()->change();
            $table->integer('total_nilai')->change();
        });
    }
};
