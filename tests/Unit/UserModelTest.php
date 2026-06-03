<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk Model User.
 *
 * Menggunakan PHPUnit\Framework\TestCase (bukan Laravel TestCase)
 * karena kita hanya menguji logika pure PHP, tidak butuh database.
 */
class UserModelTest extends TestCase
{
    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Buat instance User palsu tanpa menyentuh database.
     */
    private function makeUser(array $attributes = []): User
    {
        $user = new User();

        // Isi properti secara langsung (no factory, no DB)
        foreach ($attributes as $key => $value) {
            $user->$key = $value;
        }

        return $user;
    }

    // ── isAdmin() ────────────────────────────────────────────────────────

    public function test_isAdmin_returns_true_when_role_is_admin(): void
    {
        $user = $this->makeUser(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_isAdmin_returns_false_when_role_is_not_admin(): void
    {
        $user = $this->makeUser(['role' => 'pelamar']);

        $this->assertFalse($user->isAdmin());
    }

    // ── isPelamar() ──────────────────────────────────────────────────────

    public function test_isPelamar_returns_true_when_role_is_pelamar(): void
    {
        $user = $this->makeUser(['role' => 'pelamar']);

        $this->assertTrue($user->isPelamar());
    }

    public function test_isPelamar_returns_false_when_role_is_admin(): void
    {
        $user = $this->makeUser(['role' => 'admin']);

        $this->assertFalse($user->isPelamar());
    }

    // ── isPenguji() ──────────────────────────────────────────────────────

    public function test_isPenguji_returns_true_when_role_is_penguji(): void
    {
        $user = $this->makeUser(['role' => 'penguji']);

        $this->assertTrue($user->isPenguji());
    }

    // ── isKaprodi() ──────────────────────────────────────────────────────

    public function test_isKaprodi_returns_true_when_role_is_kaprodi(): void
    {
        $user = $this->makeUser(['role' => 'kaprodi']);

        $this->assertTrue($user->isKaprodi());
    }

    // ── hasMultipleRoles() ───────────────────────────────────────────────

    public function test_hasMultipleRoles_returns_true_when_both_flags_are_true(): void
    {
        $user = $this->makeUser([
            'is_penguji' => true,
            'is_kaprodi' => true,
        ]);

        $this->assertTrue($user->hasMultipleRoles());
    }

    public function test_hasMultipleRoles_returns_false_when_only_penguji(): void
    {
        $user = $this->makeUser([
            'is_penguji' => true,
            'is_kaprodi' => false,
        ]);

        $this->assertFalse($user->hasMultipleRoles());
    }

    public function test_hasMultipleRoles_returns_false_when_only_kaprodi(): void
    {
        $user = $this->makeUser([
            'is_penguji' => false,
            'is_kaprodi' => true,
        ]);

        $this->assertFalse($user->hasMultipleRoles());
    }

    public function test_hasMultipleRoles_returns_false_when_neither_flag_set(): void
    {
        $user = $this->makeUser([
            'is_penguji' => false,
            'is_kaprodi' => false,
        ]);

        $this->assertFalse($user->hasMultipleRoles());
    }

    // ── availableDosenRoles() ────────────────────────────────────────────

    public function test_availableDosenRoles_returns_both_roles_when_both_flags_true(): void
    {
        $user = $this->makeUser([
            'is_penguji' => true,
            'is_kaprodi' => true,
        ]);

        $this->assertSame(['penguji', 'kaprodi'], $user->availableDosenRoles());
    }

    public function test_availableDosenRoles_returns_only_penguji(): void
    {
        $user = $this->makeUser([
            'is_penguji' => true,
            'is_kaprodi' => false,
        ]);

        $this->assertSame(['penguji'], $user->availableDosenRoles());
    }

    public function test_availableDosenRoles_returns_only_kaprodi(): void
    {
        $user = $this->makeUser([
            'is_penguji' => false,
            'is_kaprodi' => true,
        ]);

        $this->assertSame(['kaprodi'], $user->availableDosenRoles());
    }

    public function test_availableDosenRoles_returns_empty_array_when_neither(): void
    {
        $user = $this->makeUser([
            'is_penguji' => false,
            'is_kaprodi' => false,
        ]);

        $this->assertSame([], $user->availableDosenRoles());
    }

    // ── isKaprodiOf() ────────────────────────────────────────────────────

    public function test_isKaprodiOf_returns_true_when_role_kaprodi_and_prodi_matches(): void
    {
        $user = $this->makeUser([
            'role'     => 'kaprodi',
            'prodi_id' => 5,
        ]);

        $this->assertTrue($user->isKaprodiOf(5));
    }

    public function test_isKaprodiOf_returns_false_when_prodi_does_not_match(): void
    {
        $user = $this->makeUser([
            'role'     => 'kaprodi',
            'prodi_id' => 5,
        ]);

        $this->assertFalse($user->isKaprodiOf(99));
    }

    public function test_isKaprodiOf_returns_false_when_role_is_not_kaprodi(): void
    {
        $user = $this->makeUser([
            'role'     => 'admin',
            'prodi_id' => 5,
        ]);

        $this->assertFalse($user->isKaprodiOf(5));
    }
}
