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
        if (!Schema::hasColumn('prodis', 'logo')) {
            Schema::table('prodis', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('kode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('prodis', 'logo')) {
            Schema::table('prodis', function (Blueprint $table) {
                $table->dropColumn('logo');
            });
        }
    }
};
