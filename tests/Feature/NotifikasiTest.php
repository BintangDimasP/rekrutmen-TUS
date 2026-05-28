<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Notifikasi — index (JSON), mark read, mark all read, isolasi kepemilikan.
 */
class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'pelamar'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makeNotif(int $userId, bool $dibaca = false): Notifikasi
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'judul'   => 'Test Notifikasi',
            'pesan'   => 'Isi pesan notifikasi.',
            'tipe'    => 'info',
            'dibaca'  => $dibaca,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // Index — JSON response
    // ══════════════════════════════════════════════════════════════

    public function test_notifikasi_index_returns_json(): void
    {
        $user = $this->makeUser();
        $this->makeNotif($user->id);
        $this->makeNotif($user->id);

        $response = $this->actingAs($user)
            ->getJson(route('notifikasi.index'));

        $response->assertOk()
            ->assertJsonStructure(['notifikasis', 'belum_dibaca']);
    }

    public function test_notifikasi_index_only_returns_own_notifikasi(): void
    {
        $user1 = $this->makeUser();
        $user2 = $this->makeUser();

        $this->makeNotif($user1->id);
        $this->makeNotif($user1->id);
        $this->makeNotif($user2->id); // milik user lain

        $response = $this->actingAs($user1)
            ->getJson(route('notifikasi.index'));

        $response->assertOk();
        $this->assertCount(2, $response->json('notifikasis'));
    }

    public function test_belum_dibaca_count_is_correct(): void
    {
        $user = $this->makeUser();
        $this->makeNotif($user->id, false); // belum dibaca
        $this->makeNotif($user->id, false); // belum dibaca
        $this->makeNotif($user->id, true);  // sudah dibaca

        $response = $this->actingAs($user)
            ->getJson(route('notifikasi.index'));

        $response->assertOk();
        $this->assertEquals(2, $response->json('belum_dibaca'));
    }

    public function test_guest_cannot_access_notifikasi(): void
    {
        // Route notifikasi adalah JSON API — guest mendapat 401 (Unauthorized), bukan redirect
        $this->getJson(route('notifikasi.index'))
            ->assertUnauthorized();
    }

    // ══════════════════════════════════════════════════════════════
    // Mark read — satu notifikasi
    // ══════════════════════════════════════════════════════════════

    public function test_user_can_mark_own_notifikasi_as_read(): void
    {
        $user  = $this->makeUser();
        $notif = $this->makeNotif($user->id, false);

        $this->actingAs($user)
            ->postJson(route('notifikasi.baca', $notif))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue((bool) Notifikasi::find($notif->id)->dibaca);
    }

    public function test_user_cannot_mark_other_user_notifikasi_as_read(): void
    {
        $user1 = $this->makeUser();
        $user2 = $this->makeUser();
        $notif = $this->makeNotif($user2->id, false);

        $this->actingAs($user1)
            ->postJson(route('notifikasi.baca', $notif))
            ->assertStatus(403);

        $this->assertFalse((bool) Notifikasi::find($notif->id)->dibaca);
    }

    public function test_marking_already_read_notifikasi_is_idempotent(): void
    {
        $user  = $this->makeUser();
        $notif = $this->makeNotif($user->id, true); // sudah dibaca

        $this->actingAs($user)
            ->postJson(route('notifikasi.baca', $notif))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue((bool) Notifikasi::find($notif->id)->dibaca);
    }

    // ══════════════════════════════════════════════════════════════
    // Mark all read
    // ══════════════════════════════════════════════════════════════

    public function test_user_can_mark_all_notifikasi_as_read(): void
    {
        $user = $this->makeUser();
        $this->makeNotif($user->id, false);
        $this->makeNotif($user->id, false);
        $this->makeNotif($user->id, false);

        $this->actingAs($user)
            ->postJson(route('notifikasi.baca.semua'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $belumDibaca = Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->count();

        $this->assertEquals(0, $belumDibaca);
    }

    public function test_mark_all_read_does_not_affect_other_users(): void
    {
        $user1 = $this->makeUser();
        $user2 = $this->makeUser();

        $this->makeNotif($user1->id, false);
        $notif2 = $this->makeNotif($user2->id, false); // milik user2

        $this->actingAs($user1)
            ->postJson(route('notifikasi.baca.semua'))
            ->assertOk();

        // Notifikasi user2 harus tetap belum dibaca
        $this->assertFalse((bool) Notifikasi::find($notif2->id)->dibaca);
    }

    public function test_mark_all_read_with_no_notifikasi_returns_ok(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->postJson(route('notifikasi.baca.semua'))
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    // ══════════════════════════════════════════════════════════════
    // Notifikasi accessible by all roles
    // ══════════════════════════════════════════════════════════════

    public function test_all_roles_can_access_notifikasi(): void
    {
        foreach (['admin', 'pelamar', 'penguji', 'kaprodi'] as $role) {
            $user = $this->makeUser($role);

            $this->actingAs($user)
                ->getJson(route('notifikasi.index'))
                ->assertOk("Role {$role} tidak bisa akses notifikasi");
        }
    }
}
