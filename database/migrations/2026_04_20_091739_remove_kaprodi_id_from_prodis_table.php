<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prodis', function (Blueprint $table) {
            $table->dropForeign(['kaprodi_id']);
            $table->dropColumn('kaprodi_id');
        });
    }

    public function down(): void
    {
        Schema::table('prodis', function (Blueprint $table) {
            $table->foreignId('kaprodi_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
