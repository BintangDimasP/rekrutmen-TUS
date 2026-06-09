<?php

namespace Tests\Feature\Pelamar;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Test: Change password (pelamar, penguji, kaprodi)
 */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    // ── Halaman settings ─────────────────────────────────────────

    public function test_pelamar_can_view_settings_page(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($user)
             ->get(route('settings.index'))
             ->assertOk();
    }

    public function test_penguji_can_view_settings_page(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);

        $this->actingAs($user)
             ->get(route('settings.index'))
             ->assertOk();
    }

    public function test_kaprodi_can_view_settings_page(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi']);

        $this->actingAs($user)
             ->get(route('settings.index'))
             ->assertOk();
    }

    public function test_admin_cannot_access_settings_page(): void
    {
        // Settings page accessible by ALL roles including admin (password & foto)
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
             ->get(route('settings.index'))
             ->assertOk();
    }

    // ── Update password ──────────────────────────────────────────

    public function test_pelamar_can_change_password(): void
    {
        $user = User::factory()->create([
            'role'     => 'pelamar',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
             ->put(route('settings.password.update'), [
                 'current_password'      => 'password123',
                 'password'              => 'newpassword1',
                 'password_confirmation' => 'newpassword1',
             ])
             ->assertRedirect()
             ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
    }

    public function test_wrong_current_password_rejected(): void
    {
        $user = User::factory()->create([
            'role'     => 'pelamar',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
             ->put(route('settings.password.update'), [
                 'current_password'      => 'salah_banget',
                 'password'              => 'newpassword1',
                 'password_confirmation' => 'newpassword1',
             ])
             ->assertSessionHasErrors('current_password');

        // Password tidak berubah
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    public function test_password_confirmation_mismatch_rejected(): void
    {
        $user = User::factory()->create([
            'role'     => 'pelamar',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
             ->put(route('settings.password.update'), [
                 'current_password'      => 'password123',
                 'password'              => 'newpassword1',
                 'password_confirmation' => 'tidakcocok',
             ])
             ->assertSessionHasErrors('password');
    }

    public function test_password_too_short_rejected(): void
    {
        $user = User::factory()->create([
            'role'     => 'pelamar',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
             ->put(route('settings.password.update'), [
                 'current_password'      => 'password123',
                 'password'              => '123',
                 'password_confirmation' => '123',
             ])
             ->assertSessionHasErrors('password');
    }

    public function test_guest_cannot_access_settings(): void
    {
        $this->get(route('settings.index'))->assertRedirect('/login');
    }
}
