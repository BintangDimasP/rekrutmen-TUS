<?php

use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;

/**
 * WHITEBOX TESTING: Halaman Utama (Landing)
 * 
 * FLOWGRAPH:
 *   N1 (START) → N2 (Query lowongans) → N3 (Hitung totalPendaftar) → N4 (Return view)
 * 
 * CYCLOMATIC COMPLEXITY: V(G) = 1
 *   - Total Nodes: 4
 *   - Total Edges: 3
 *   - Decision Points: 0
 *   - V(G) = 0 + 1 = 1
 * 
 * BASIS PATH: 1 path
 *   Path 1: N1 → N2 → N3 → N4 (Happy path - menampilkan lowongan aktif)
 */

describe('Landing Page', function () {
    
    beforeEach(function () {
        // Setup: Buat data prodi untuk relasi
        $this->prodi = Prodi::factory()->create();
    });

    /**
     * PATH 1: N1 → N2 → N3 → N4
     * Test halaman landing dapat diakses dan menampilkan data lowongan aktif
     */
    it('dapat menampilkan halaman landing dengan lowongan aktif', function () {
        // Arrange: Buat 8 lowongan (6 aktif, 2 ditutup)
        Lowongan::factory()->count(6)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->addDays(7),
        ]);

        Lowongan::factory()->count(2)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'ditutup',
            'tanggal_tutup' => now()->addDays(7),
        ]);

        // Buat 10 data pelamar
        Pelamar::factory()->count(10)->create();

        // Act: Akses halaman landing
        $response = $this->get(route('landing'));

        // Assert: Response sukses
        $response->assertStatus(200);
        
        // Assert: View yang benar ditampilkan
        $response->assertViewIs('landing');
        
        // Assert: Variabel lowongans ada dan hanya 6 item (meski ada 8 total)
        $response->assertViewHas('lowongans', function ($lowongans) {
            return $lowongans->count() === 6;
        });
        
        // Assert: Semua lowongan yang ditampilkan berstatus aktif
        $response->assertViewHas('lowongans', function ($lowongans) {
            return $lowongans->every(fn($lowongan) => $lowongan->status === 'aktif');
        });
        
        // Assert: Total pendaftar = 10
        $response->assertViewHas('totalPendaftar', 10);
    });

    /**
     * PATH 1 (Edge Case): Tidak ada lowongan aktif
     */
    it('dapat menampilkan halaman landing meskipun tidak ada lowongan aktif', function () {
        // Arrange: Hanya buat lowongan ditutup atau sudah lewat tanggal
        Lowongan::factory()->count(3)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'ditutup',
            'tanggal_tutup' => now()->addDays(7),
        ]);

        Lowongan::factory()->count(2)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->subDays(1), // Sudah tutup
        ]);

        // Act
        $response = $this->get(route('landing'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('landing');
        
        // Assert: Lowongans kosong (0 item)
        $response->assertViewHas('lowongans', function ($lowongans) {
            return $lowongans->count() === 0;
        });
    });

    /**
     * PATH 1 (Edge Case): Tidak ada pelamar
     */
    it('dapat menampilkan halaman landing dengan total pendaftar = 0', function () {
        // Arrange: Buat lowongan tapi tidak ada pelamar
        Lowongan::factory()->count(3)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->addDays(7),
        ]);

        // Act
        $response = $this->get(route('landing'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('totalPendaftar', 0);
    });

    /**
     * PATH 1 (Edge Case): Lebih dari 6 lowongan aktif
     */
    it('hanya menampilkan maksimal 6 lowongan terbaru', function () {
        // Arrange: Buat 10 lowongan aktif
        Lowongan::factory()->count(10)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->addDays(7),
        ]);

        // Act
        $response = $this->get(route('landing'));

        // Assert: Hanya 6 yang ditampilkan
        $response->assertViewHas('lowongans', function ($lowongans) {
            return $lowongans->count() === 6;
        });
    });

    /**
     * PATH 1 (Edge Case): Filter tanggal tutup bekerja dengan benar
     */
    it('tidak menampilkan lowongan yang sudah melewati tanggal tutup', function () {
        // Arrange: Buat lowongan expired
        Lowongan::factory()->count(3)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->subDays(1), // Kemarin
        ]);

        // Buat lowongan valid
        Lowongan::factory()->count(2)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->addDays(7), // Masih berlaku
        ]);

        // Act
        $response = $this->get(route('landing'));

        // Assert: Hanya 2 lowongan yang ditampilkan
        $response->assertViewHas('lowongans', function ($lowongans) {
            return $lowongans->count() === 2;
        });
    });

    /**
     * PATH 1: Test relasi prodi di-load (eager loading)
     */
    it('memuat relasi prodi untuk setiap lowongan', function () {
        // Arrange
        Lowongan::factory()->count(3)->create([
            'prodi_id' => $this->prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->addDays(7),
        ]);

        // Act
        $response = $this->get(route('landing'));

        // Assert: Relasi prodi sudah di-load (tidak ada N+1 query)
        $response->assertViewHas('lowongans', function ($lowongans) {
            return $lowongans->first()->relationLoaded('prodi');
        });
    });
});
