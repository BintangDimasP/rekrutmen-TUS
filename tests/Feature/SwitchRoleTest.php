<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SwitchRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_kaprodi_dapat_pindah_role_ke_penguji(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_kaprodi' => true, 'is_penguji' => true]);
        $user = User::factory()->create([
            'role' => 'kaprodi',
            'dosen_id' => $dosen->id,
            'is_kaprodi' => true,
            'is_penguji' => true,
            'prodi_id' => $prodi->id,
        ]);

        $response = $this->actingAs($user)->post(route('role.switch'), ['role' => 'penguji']);

        $response->assertRedirect(route('penguji.dashboard'));
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('penguji', $user->role);
    }

    public function test_penguji_dapat_pindah_role_ke_kaprodi(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_kaprodi' => true, 'is_penguji' => true]);
        $user = User::factory()->create([
            'role' => 'penguji',
            'dosen_id' => $dosen->id,
            'is_kaprodi' => true,
            'is_penguji' => true,
            'prodi_id' => $prodi->id,
        ]);

        $response = $this->actingAs($user)->post(route('role.switch'), ['role' => 'kaprodi']);

        $response->assertRedirect(route('kaprodi.dashboard'));
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('kaprodi', $user->role);
    }
}
