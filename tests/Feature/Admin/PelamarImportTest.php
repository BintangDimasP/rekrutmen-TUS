<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/**
 * Test: Admin — Import Pelamar
 */
class PelamarImportTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_import_requires_file(): void
    {
        $this->actingAs($this->adminUser())
             ->post(route('admin.pelamar.import'), [])
             ->assertSessionHasErrors('file');
    }

    public function test_import_rejects_invalid_file_type(): void
    {
        $file = UploadedFile::fake()->create('data.pdf', 100, 'application/pdf');

        $this->actingAs($this->adminUser())
             ->post(route('admin.pelamar.import'), ['file' => $file])
             ->assertSessionHasErrors('file');
    }

    public function test_import_accepts_valid_xlsx(): void
    {
        Excel::fake();

        $file = UploadedFile::fake()->create('pelamar.xlsx', 100, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($this->adminUser())
             ->post(route('admin.pelamar.import'), ['file' => $file])
             ->assertSessionHasNoErrors()
             ->assertRedirect();
    }

    public function test_non_admin_cannot_import(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);
        $file    = UploadedFile::fake()->create('pelamar.xlsx', 100);

        $this->actingAs($pelamar)
             ->post(route('admin.pelamar.import'), ['file' => $file])
             ->assertRedirect(route('pelamar.dashboard'));
    }
}
