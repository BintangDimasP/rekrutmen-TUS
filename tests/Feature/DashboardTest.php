<?php

/**
 * WHITE BOX TESTING - DASHBOARD PENGGUNA
 * Teknik     : Branch Coverage
 * Controllers:
 *   Admin   → Admin\DashboardController@index
 *   Pelamar → Pelamar\DashboardController@index
 *   Penguji → Penguji\PengujiController@dashboard
 *   Kaprodi → Kaprodi\KaprodiController@dashboard
 *
 * ============================================================
 * ANALISIS BRANCH PER ROLE:
 * ============================================================
 *
 * [Admin — DashboardController@index]
 *
 *   $activeLowongan = ...->filter(fn($l) => $l->status === 'aktif') // Branch 1
 *   $acceptanceRate = $totalLamaran > 0 ? round(...) : 0            // Branch 2
 *   $statusCounts['menunggu'] ?? 0  (dan status lainnya)            // Branch 3
 *   DB::getDriverName() === 'sqlite'                                 // Branch 4
 *   if ($lamaranCount > $maxChartValue)                              // Branch 5
 *   $maxChartValue = max(10, $maxChartValue)                         // Branch 6
 *
 * Branch 1 — Filter lowongan aktif
 *   TRUE  : Ada lowongan aktif → activeLowongan > 0
 *   FALSE : Tidak ada lowongan aktif → activeLowongan = 0
 *
 * Branch 2 — Perhitungan acceptance rate
 *   TRUE  : Ada lamaran → hitung persentase
 *   FALSE : Tidak ada lamaran → return 0
 *
 * Branch 3 — Null coalescing status lamaran
 *   TRUE  : Ada data status → ambil nilainya
 *   FALSE : Status tidak ada → return 0
 *
 * Branch 4 — Driver database
 *   TRUE  : SQLite → gunakan strftime
 *   FALSE : MySQL → gunakan MONTH()
 *
 * Branch 5 — Update maxChartValue
 *   TRUE  : lamaranCount > maxChartValue → update
 *   FALSE : Tidak ada data bulanan lebih besar
 *
 * Branch 6 — Minimal maxChartValue
 *   TRUE  : maxChartValue < 10 → gunakan 10
 *   FALSE : maxChartValue >= 10 → gunakan nilai asli
 *
 * ------------------------------------------------------------
 *
 * [Pelamar — DashboardController@index]
 *
 *   if ($pelamar)                                                    // Branch 7
 *   if ($request->session()->pull('show_profile_reminder', false))   // Branch 8
 *   if (empty(email_verified_at))                                    // Branch 9
 *   if (empty($pelamar->no_telepon))                                 // Branch 10
 *   foreach dataDiri → if (empty($field))                           // Branch 11
 *   foreach pendidikan → if (empty($field))                         // Branch 12
 *   foreach dokumen → if (empty($field))                            // Branch 13
 *   foreach akademik → if (empty($field))                           // Branch 14
 *   if (empty($incompleteSections))                                  // Branch 15
 *
 * Branch 7 — User memiliki data pelamar
 *   TRUE  : Ada relasi pelamar → tampilkan statistik
 *   FALSE : Tidak ada relasi → semua statistik 0
 *
 * Branch 8 — Session show_profile_reminder
 *   TRUE  : Pertama kali login → cek kelengkapan profil
 *   FALSE : Bukan pertama kali → tidak cek
 *
 * Branch 15 — Semua profil lengkap
 *   TRUE  : Semua lengkap → showProfileModal = false
 *   FALSE : Ada yang kosong → showProfileModal = true
 *
 * ------------------------------------------------------------
 *
 * [Penguji — PengujiController@dashboard]
 *
 *   if (!$dosen)                                                     // Branch 16
 *
 * Branch 16 — User terdaftar sebagai dosen
 *   TRUE  : dosen_id ada → tampilkan dashboard
 *   FALSE : dosen_id null → abort 403
 *
 * ------------------------------------------------------------
 *
 * [Kaprodi — KaprodiController@dashboard]
 *
 *   $lowonganIds berdasarkan prodi_id kaprodi                        // Branch 17
 *
 * Branch 17 — Ada lowongan di prodi kaprodi
 *   TRUE  : Ada lowongan → tampilkan statistik
 *   FALSE : Tidak ada lowongan → semua statistik 0
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * [Admin]
 * TC-01 : B1=T, B2=T, B3=T, B5=T, B6=T → data lengkap semua status
 * TC-02 : B1=F, B2=F, B3=F, B6=T       → tidak ada data apapun
 * TC-03 : B2=T (diterima=0)             → ada lamaran tapi nol diterima
 *
 * [Pelamar]
 * TC-04 : B7=T, B8=F                    → pelamar ada, bukan pertama login
 * TC-05 : B7=T, B8=T, B15=F            → pertama login, profil tidak lengkap
 * TC-06 : B7=T, B8=T, B15=T            → pertama login, profil lengkap
 *
 * [Penguji]
 * TC-07 : B16=T (dosen ada, jadwal ada) → penguji dengan data jadwal
 * TC-08 : B16=T (dosen ada, jadwal nol) → penguji tanpa jadwal
 *
 * [Kaprodi]
 * TC-09 : B17=T (ada lowongan di prodi) → kaprodi dengan data lengkap
 * TC-10 : B17=F (tidak ada lowongan)    → kaprodi tanpa lowongan di prodi
 */

use App\Models\Dosen;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ================================================================
// DASHBOARD ADMIN
// ================================================================

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = Prodi::factory()->create();
});

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=T : Ada lowongan aktif
// B2=T : Ada lamaran → acceptance rate dihitung
// B3=T : Semua status lamaran ada
// B5=T : lamaranCount > maxChartValue
// B6=T : maxChartValue >= 10
// ---------------------------------------------------------------
test('TC-01: Admin mengakses dashboard dengan data lengkap, sistem menampilkan dashboard admin dengan statistik dan grafik bulanan', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(30), // Pastikan belum tutup
        'kuota'         => 20, // Kuota cukup besar
    ]);

    $pelamars = Pelamar::factory()->count(10)->create();

    foreach (range(0, 2) as $i) {
        Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id'  => $pelamars[$i]->id,
            'status'      => 'menunggu',
            'created_at'  => Carbon::create(now()->year, 3, 10),
        ]);
    }
    foreach (range(3, 5) as $i) {
        Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id'  => $pelamars[$i]->id,
            'status'      => 'diterima',
            'created_at'  => Carbon::create(now()->year, 6, 5),
        ]);
    }
    foreach (range(6, 8) as $i) {
        Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id'  => $pelamars[$i]->id,
            'status'      => 'ditolak',
            'created_at'  => Carbon::create(now()->year, 6, 5),
        ]);
    }

    // Act
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    // Assert
    $response->assertStatus(200)->assertViewIs('admin.dashboard');
    $response->assertViewHas('activeLowongan', 1);
    $response->assertViewHas('totalLamaran', 9);
    $response->assertViewHas('totalDiterima', 3);

    $acceptanceRate = $response->viewData('acceptanceRate');
    expect($acceptanceRate)->toBeGreaterThan(0);

    $chartData = $response->viewData('chartData');
    expect($chartData)->toHaveCount(12);

    $maxChartValue = $response->viewData('maxChartValue');
    expect($maxChartValue)->toBeGreaterThanOrEqual(10);
});

// ---------------------------------------------------------------
// TC-02 | Unhappy Path
// B1=F : Tidak ada lowongan aktif
// B2=F : Tidak ada lamaran → acceptance rate = 0
// B3=F : Semua status ?? 0
// B6=T : maxChartValue < 10 → gunakan 10
// ---------------------------------------------------------------
test('TC-02: Admin mengakses dashboard tanpa data, sistem menampilkan dashboard admin dengan statistik bernilai 0', function () {
    // Act
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('activeLowongan', 0);
    $response->assertViewHas('totalLamaran', 0);
    $response->assertViewHas('acceptanceRate', 0);

    $statusData = $response->viewData('statusData');
    expect($statusData['menunggu'])->toBe(0);
    expect($statusData['proses'])->toBe(0);
    expect($statusData['diterima'])->toBe(0);

    $maxChartValue = $response->viewData('maxChartValue');
    expect($maxChartValue)->toBe(10);
});

// ---------------------------------------------------------------
// TC-03 | Edge Case
// B2=T : Ada lamaran tapi tidak ada yang diterima → grafik diterima 0
// ---------------------------------------------------------------
test('TC-03: Admin mengakses dashboard dengan lamaran tanpa ada yang diterima, sistem menampilkan dashboard admin dengan grafik pelamar diterima 0', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
    $pelamars = Pelamar::factory()->count(5)->create();

    foreach ($pelamars as $pelamar) {
        Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id'  => $pelamar->id,
            'status'      => 'ditolak',
        ]);
    }

    // Act
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalLamaran', 5);
    $response->assertViewHas('totalDiterima', 0);
    $response->assertViewHas('acceptanceRate', 0.0);
});

// ================================================================
// DASHBOARD PELAMAR
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Happy Path
// B7=T : User punya relasi pelamar dengan riwayat lamaran
// ---------------------------------------------------------------
test('TC-04: Pelamar mengakses dashboard dengan riwayat lamaran, sistem menampilkan dashboard pelamar dengan status lamaran', function () {
    // Arrange
    $user    = User::factory()->create(['role' => 'pelamar']);
    $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
    $lowongan = Lowongan::factory()->create();

    Lamaran::factory()->create([
        'pelamar_id'  => $pelamar->id,
        'lowongan_id' => $lowongan->id,
        'status'      => 'menunggu',
    ]);
    Lamaran::factory()->create([
        'pelamar_id'  => $pelamar->id,
        'lowongan_id' => Lowongan::factory()->create()->id,
        'status'      => 'diterima',
    ]);

    // Act
    $response = $this->actingAs($user)->get(route('pelamar.dashboard'));

    // Assert
    $response->assertStatus(200)->assertViewIs('pelamar.dashboard');

    $totalLamaran = $response->viewData('totalLamaran');
    expect($totalLamaran)->toBe(2);

    $showProfileModal = $response->viewData('showProfileModal');
    expect($showProfileModal)->toBeFalse();
});

// ---------------------------------------------------------------
// TC-05 | Happy Path + Branch
// B7=T : Ada data pelamar
// B8=T : Pertama login (session show_profile_reminder = true)
// B15=F : Ada section profil yang belum lengkap
// ---------------------------------------------------------------
test('TC-05: Pelamar pertama login dengan profil tidak lengkap, sistem menampilkan dashboard pelamar dengan modal peringatan kelengkapan profil', function () {
    // Arrange
    $user    = User::factory()->unverified()->create(['role' => 'pelamar']);
    $pelamar = Pelamar::factory()->create([
        'user_id'    => $user->id,
        'no_telepon' => null, // sengaja kosong
    ]);

    // Act: dengan session show_profile_reminder
    $response = $this->actingAs($user)
        ->withSession(['show_profile_reminder' => true])
        ->get(route('pelamar.dashboard'));

    // Assert
    $response->assertStatus(200);

    $showProfileModal = $response->viewData('showProfileModal');
    expect($showProfileModal)->toBeTrue();

    $incompleteSections = $response->viewData('incompleteSections');
    expect($incompleteSections)->not->toBeEmpty();
});

// ---------------------------------------------------------------
// TC-06 | Happy Path + Branch
// B7=T : Ada data pelamar
// B8=T : Pertama login
// B15=T : Semua section profil lengkap → modal tidak muncul
// ---------------------------------------------------------------
test('TC-06: Pelamar pertama login dengan profil lengkap, sistem menampilkan dashboard pelamar tanpa modal peringatan', function () {
    // Arrange
    $user    = User::factory()->create(['role' => 'pelamar', 'email_verified_at' => now()]);
    $pelamar = Pelamar::factory()->create([
        'user_id'          => $user->id,
        'no_telepon'       => '081234567890',
        'nik'              => '3201234567890001',
        'nama'             => 'Budi Santoso',
        'tempat_lahir'     => 'Bandung',
        'tanggal_lahir'    => '1995-01-01',
        'jenis_kelamin'    => 'L',
        'alamat_domisili'  => 'Jl. Sudirman No. 1',
        'jenjang'          => 'S2',
        'institusi'        => 'ITB',
        'file_ijazah'      => 'ijazah.pdf',
        'file_transkrip'   => 'transkrip.pdf',
        'file_cv'          => 'cv.pdf',
        'file_pas_foto'    => 'foto.jpg',
        'file_ktp'         => 'ktp.jpg',
        'nidn'             => '0012345678',
        'homebase'         => 'Teknik Informatika',
        'jabatan_akademik' => 'lektor',
    ]);

    // Act
    $response = $this->actingAs($user)
        ->withSession(['show_profile_reminder' => true])
        ->get(route('pelamar.dashboard'));

    // Assert
    $response->assertStatus(200);

    $showProfileModal = $response->viewData('showProfileModal');
    expect($showProfileModal)->toBeFalse();
});

// ================================================================
// DASHBOARD PENGUJI
// ================================================================

// ---------------------------------------------------------------
// TC-07 | Happy Path dengan data jadwal pengujian
// B16=T : User punya dosen_id valid → dashboard tampil dengan data
// ---------------------------------------------------------------
test('TC-07: Penguji mengakses dashboard dengan jadwal pengujian, sistem menampilkan dashboard penguji dengan statistik jadwal', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id]);
    $user  = User::factory()->create([
        'role'     => 'penguji',
        'dosen_id' => $dosen->id,
    ]);

    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    $pelamar = Pelamar::factory()->create();
    
    // Buat 3 jadwal: 2 untuk micro teaching, 1 untuk wawancara
    $jadwal1 = \App\Models\JadwalSeleksi::create([
        'lowongan_id'   => $lowongan->id,
        'pelamar_id'    => $pelamar->id,
        'penguji_id'    => $dosen->id,
        'tipe_seleksi'  => 'micro_teaching',
        'tanggal'       => now()->addDays(2),
        'sesi'          => 1,
        'ruangan'       => 'Lab A',
        'link_meeting'  => 'https://meet.example.com/test1',
    ]);
    
    \App\Models\JadwalSeleksi::create([
        'lowongan_id'   => $lowongan->id,
        'pelamar_id'    => $pelamar->id,
        'penguji_id'    => $dosen->id,
        'tipe_seleksi'  => 'micro_teaching',
        'tanggal'       => now()->addDays(3),
        'sesi'          => 2,
        'ruangan'       => 'Lab B',
        'link_meeting'  => 'https://meet.example.com/test2',
    ]);

    // Nilai salah satu jadwal
    \App\Models\Penilaian::create([
        'jadwal_seleksi_id' => $jadwal1->id,
        'kategori_1'        => 4.5,
        'total_nilai'       => 4.5,
        'detail_nilai'      => ['k1_item_1' => 5, 'k1_item_2' => 4],
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'Teknik Informatika',
    ]);

    // Act
    $response = $this->actingAs($user)->get(route('penguji.dashboard'));

    // Assert
    $response->assertStatus(200)->assertViewIs('penguji.dashboard');
    $response->assertViewHas('totalDiuji', 2);
    $response->assertViewHas('totalDinilai', 1);
    $response->assertViewHas('totalBelumDinilai', 1);
    
    $upcomingJadwals = $response->viewData('upcomingJadwals');
    expect($upcomingJadwals)->toHaveCount(2);
});

// ---------------------------------------------------------------
// TC-08 | Unhappy Path tanpa jadwal pengujian
// B16=T : User punya dosen_id valid tapi tidak ada jadwal
// ---------------------------------------------------------------
test('TC-08: Penguji mengakses dashboard tanpa jadwal pengujian, sistem menampilkan dashboard penguji dengan statistik bernilai 0', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id]);
    $user  = User::factory()->create([
        'role'     => 'penguji',
        'dosen_id' => $dosen->id,
    ]);

    // Act
    $response = $this->actingAs($user)->get(route('penguji.dashboard'));

    // Assert
    $response->assertStatus(200)->assertViewIs('penguji.dashboard');
    $response->assertViewHas('totalDiuji', 0);
    $response->assertViewHas('totalDinilai', 0);
    $response->assertViewHas('totalBelumDinilai', 0);
    
    $upcomingJadwals = $response->viewData('upcomingJadwals');
    expect($upcomingJadwals)->toBeEmpty();
});

// ================================================================
// DASHBOARD KAPRODI
// ================================================================

// ---------------------------------------------------------------
// TC-09 | Happy Path dengan data pelamar di prodi
// B17=T : Ada lowongan di prodi kaprodi → statistik dihitung
// ---------------------------------------------------------------
test('TC-09: Kaprodi mengakses dashboard dengan data pelamar di prodinya, sistem menampilkan dashboard kaprodi dengan statistik pelamar', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id]);
    $user  = User::factory()->create([
        'role'     => 'kaprodi',
        'prodi_id' => $prodi->id,
        'dosen_id' => $dosen->id,
    ]);

    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    $pelamar1 = Pelamar::factory()->create();
    $pelamar2 = Pelamar::factory()->create();

    Lamaran::factory()->create([
        'lowongan_id' => $lowongan->id,
        'pelamar_id'  => $pelamar1->id,
        'status'      => 'diterima',
    ]);
    
    Lamaran::factory()->create([
        'lowongan_id' => $lowongan->id,
        'pelamar_id'  => $pelamar2->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($user)->get(route('kaprodi.dashboard'));

    // Assert
    $response->assertStatus(200)->assertViewIs('kaprodi.dashboard');
    $response->assertViewHas('totalPelamar', 2);
    $response->assertViewHas('totalDiterima', 1);
    $response->assertViewHas('totalDitolak', 0);
    
    $lamaranTerbaru = $response->viewData('lamaranTerbaru');
    expect($lamaranTerbaru)->toHaveCount(2);
});

// ---------------------------------------------------------------
// TC-10 | Unhappy Path tanpa data pelamar di prodi
// B17=F : Tidak ada lowongan di prodi kaprodi → semua statistik 0
// ---------------------------------------------------------------
test('TC-10: Kaprodi mengakses dashboard tanpa data pelamar di prodinya, sistem menampilkan dashboard kaprodi dengan statistik bernilai 0', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id]);
    $user  = User::factory()->create([
        'role'     => 'kaprodi',
        'prodi_id' => $prodi->id,
        'dosen_id' => $dosen->id,
    ]);

    // Act: tidak ada lowongan di prodi ini
    $response = $this->actingAs($user)->get(route('kaprodi.dashboard'));

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPelamar', 0);
    $response->assertViewHas('totalDiterima', 0);
    $response->assertViewHas('totalDitolak', 0);
    
    $lamaranTerbaru = $response->viewData('lamaranTerbaru');
    expect($lamaranTerbaru)->toBeEmpty();
});
