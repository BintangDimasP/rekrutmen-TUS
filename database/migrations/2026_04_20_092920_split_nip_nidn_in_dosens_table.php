<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            $table->renameColumn('nip_nidn', 'nip');
            $table->string('nidn')->nullable()->after('nip');
        });
    }

    public function down(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            $table->dropColumn('nidn');
            $table->renameColumn('nip', 'nip_nidn');
        });
    }
};
