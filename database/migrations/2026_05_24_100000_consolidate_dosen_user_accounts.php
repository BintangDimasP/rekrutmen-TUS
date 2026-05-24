<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Konsolidasi: 1 dosen = 1 akun User (email @pengajar.telkomuniversity.ac.id),
     * dengan flags is_penguji & is_kaprodi yang bisa keduanya true (rangkap).
     */
    public function up(): void
    {
        // 1. Tambah kolom flags + jadikan role nullable
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_penguji')) {
                $table->boolean('is_penguji')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'is_kaprodi')) {
                $table->boolean('is_kaprodi')->default(false)->after('is_penguji');
            }
        });

        // role jadi nullable supaya dosen tanpa role aktif bisa disimpan
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->change();
        });

        $domain = 'pengajar.telkomuniversity.ac.id';

        // 2. Konsolidasi setiap dosen: gabung jadi 1 akun
        $dosenIds = DB::table('dosens')->pluck('id');

        foreach ($dosenIds as $dosenId) {
            $accounts = DB::table('users')
                ->where('dosen_id', $dosenId)
                ->orderBy('id')
                ->get();

            $hasPenguji = $accounts->contains(fn($u) => $u->role === 'penguji');
            $hasKaprodi = $accounts->contains(fn($u) => $u->role === 'kaprodi');

            // Pilih akun yang dipertahankan: prioritaskan kaprodi, lalu penguji, lalu yang pertama
            $kept = $accounts->firstWhere('role', 'kaprodi')
                ?? $accounts->firstWhere('role', 'penguji')
                ?? $accounts->first();

            // Hapus akun lain milik dosen ini (selain $kept)
            if ($kept) {
                DB::table('users')
                    ->where('dosen_id', $dosenId)
                    ->where('id', '!=', $kept->id)
                    ->delete();
            }

            // Generate email pengajar yang unik
            $dosen = DB::table('dosens')->where('id', $dosenId)->first();
            if (!$dosen) continue;

            $prefix = $this->generateEmailPrefix($dosen->nama);
            $newEmail = $this->generateUniqueEmail($prefix, $domain, $kept?->id);

            // Tentukan role aktif: prioritaskan kaprodi
            $activeRole = $hasKaprodi ? 'kaprodi' : ($hasPenguji ? 'penguji' : null);

            if ($kept) {
                DB::table('users')->where('id', $kept->id)->update([
                    'email'      => $newEmail,
                    'is_penguji' => $hasPenguji,
                    'is_kaprodi' => $hasKaprodi,
                    'role'       => $activeRole,
                ]);
            } else {
                // Dosen tanpa akun → buat akun pengajar baru tanpa role/akses
                DB::table('users')->insert([
                    'name'       => $dosen->nama,
                    'email'      => $newEmail,
                    'password'   => Hash::make(Str::random(60)), // random, tak bisa login
                    'role'       => null,
                    'is_penguji' => false,
                    'is_kaprodi' => false,
                    'prodi_id'   => $dosen->prodi_id,
                    'dosen_id'   => $dosen->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_penguji')) {
                $table->dropColumn('is_penguji');
            }
            if (Schema::hasColumn('users', 'is_kaprodi')) {
                $table->dropColumn('is_kaprodi');
            }
        });
    }

    private function generateEmailPrefix(string $nama): string
    {
        $parts = preg_split('/\s+/', trim($nama));
        $prefix = strtolower(implode('', array_slice($parts, 0, 2)));
        return preg_replace('/[^a-z0-9]/', '', $prefix);
    }

    private function generateUniqueEmail(string $prefix, string $domain, ?int $exceptUserId = null): string
    {
        $email = $prefix . '@' . $domain;
        $counter = 1;

        while (
            DB::table('users')
                ->where('email', $email)
                ->when($exceptUserId, fn($q) => $q->where('id', '!=', $exceptUserId))
                ->exists()
        ) {
            $email = $prefix . $counter . '@' . $domain;
            $counter++;
        }

        return $email;
    }
};
