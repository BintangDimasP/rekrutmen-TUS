<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_diarahkan_ke_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_dapat_mengakses_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_pelamar_dapat_mengakses_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);

        $response = $this->actingAs($user)->get('/pelamar/dashboard');
        $response->assertStatus(200);
    }

    public function test_penguji_dapat_mengakses_dashboard(): void
    {
        $prodi = \App\Models\Prodi::factory()->create();
        $dosen = \App\Models\Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_penguji' => true]);
        $user = User::factory()->create([
            'role' => 'penguji',
            'is_penguji' => true,
            'prodi_id' => $prodi->id,
            'dosen_id' => $dosen->id,
        ]);

        $response = $this->actingAs($user)->get('/penguji/dashboard');
        $response->assertOk();
    }

    public function test_kaprodi_dapat_mengakses_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi', 'is_kaprodi' => true]);

        $response = $this->actingAs($user)->get('/kaprodi/dashboard');
        $response->assertStatus(200);
    }
}
