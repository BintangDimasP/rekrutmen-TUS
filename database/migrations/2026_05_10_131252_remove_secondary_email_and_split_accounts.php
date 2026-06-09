<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Split accounts before dropping column
        $usersToSplit = User::whereNotNull('secondary_email')->get();
        foreach ($usersToSplit as $user) {
            User::create([
                'name' => $user->name,
                'email' => $user->secondary_email,
                'password' => Hash::make('penguji123'),
                'role' => 'penguji',
                'prodi_id' => $user->prodi_id,
                'dosen_id' => $user->dosen_id,
            ]);
        }

        // 2. Drop the column (drop unique index first for SQLite compatibility)
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['secondary_email']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('secondary_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('secondary_email')->nullable()->after('email')->unique();
        });
        
        // This won't perfectly merge them back on down(), but it's okay for now.
    }
};
