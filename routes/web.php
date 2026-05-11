<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Default landing page
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Autentikasi group
Route::middleware(['auth', 'verified'])->group(function () {

    // Smart redirect: /dashboard akan otomatis mengarahkan ke dashboard yang sesuai rolenya
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return redirect()->route("{$role}.dashboard");
    })->name('dashboard');

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        // Prodi Management
        Route::resource('admin/prodi', \App\Http\Controllers\Admin\ProdiController::class)
            ->names('admin.prodi')
            ->parameters(['prodi' => 'prodi']);

        // Dosen Management
        Route::post('admin/prodi/{prodi}/dosen', [\App\Http\Controllers\Admin\DosenController::class, 'store'])->name('admin.dosen.store');
        Route::post('admin/prodi/{prodi}/dosen/import', [\App\Http\Controllers\Admin\DosenController::class, 'import'])->name('admin.dosen.import');
        Route::put('admin/dosen/{dosen}', [\App\Http\Controllers\Admin\DosenController::class, 'update'])->name('admin.dosen.update');
        Route::delete('admin/dosen/{dosen}', [\App\Http\Controllers\Admin\DosenController::class, 'destroy'])->name('admin.dosen.destroy');

        // User Management
        Route::get('admin/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.user.index');
        Route::put('admin/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.user.update');

        // Penguji Management
        Route::get('admin/penguji', [\App\Http\Controllers\Admin\PengujiController::class, 'index'])->name('admin.penguji.index');
        Route::get('admin/penguji/{penguji}', [\App\Http\Controllers\Admin\PengujiController::class, 'show'])->name('admin.penguji.show');
        Route::post('admin/penguji', [\App\Http\Controllers\Admin\PengujiController::class, 'store'])->name('admin.penguji.store');
        Route::delete('admin/penguji/{penguji}', [\App\Http\Controllers\Admin\PengujiController::class, 'destroy'])->name('admin.penguji.destroy');

        Route::resource('admin/lowongan', \App\Http\Controllers\Admin\LowonganController::class)
            ->names('admin.lowongan')
            ->parameters(['lowongan' => 'lowongan']);
        Route::get('admin/lowongan/{lowongan}/berita-acara', [\App\Http\Controllers\Admin\LowonganController::class, 'beritaAcara'])->name('admin.lowongan.beritaAcara');

        // Lamaran Management (View/Update/Hapus)
        Route::get('admin/lamaran/{lamaran}', [\App\Http\Controllers\Admin\LamaranController::class, 'show'])->name('admin.lamaran.show');
        Route::put('admin/lamaran/{lamaran}', [\App\Http\Controllers\Admin\LamaranController::class, 'update'])->name('admin.lamaran.update');
        Route::delete('admin/lamaran/{lamaran}', [\App\Http\Controllers\Admin\LamaranController::class, 'destroy'])->name('admin.lamaran.destroy');

        // Pelamar Management (Global)
        Route::get('admin/pelamar', [\App\Http\Controllers\Admin\PelamarController::class, 'index'])->name('admin.pelamar.index');
        Route::get('admin/pelamar/{pelamar}', [\App\Http\Controllers\Admin\PelamarController::class, 'show'])->name('admin.pelamar.show');

        // Jadwal Seleksi Management
        Route::get('admin/jadwal', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'index'])->name('admin.jadwal.index');
        Route::get('admin/jadwal/create', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'create'])->name('admin.jadwal.create');
        Route::post('admin/jadwal', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'store'])->name('admin.jadwal.store');
        Route::put('admin/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'update'])->name('admin.jadwal.update');
        Route::put('admin/jadwal-group', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'updateGroup'])->name('admin.jadwal.updateGroup');
        Route::delete('admin/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'destroy'])->name('admin.jadwal.destroy');

        // API Endpoints (JSON - untuk AJAX di form penjadwalan)
        Route::get('admin/api/lowongan-by-prodi', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiLowongan'])->name('admin.api.lowongan');
        Route::get('admin/api/penguji-by-prodi', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiPenguji'])->name('admin.api.penguji');
        Route::get('admin/api/pelamar-by-lowongan', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiPelamar'])->name('admin.api.pelamar');
        Route::get('admin/api/sesi-tersedia', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiAvailableSessions'])->name('admin.api.sesi');
        Route::get('admin/api/sesi-taken-all', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiSesiTakenAll'])->name('admin.api.sesi.taken');
        Route::get('admin/api/sesi-pelamar', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiSesiPelamar'])->name('admin.api.sesi.pelamar');
    });

    // Pelamar
    Route::middleware('role:pelamar')->group(function () {
        Route::get('/pelamar/dashboard', [\App\Http\Controllers\Pelamar\DashboardController::class, 'index'])->name('pelamar.dashboard');

        Route::get('/pelamar/profil', [\App\Http\Controllers\Pelamar\ProfilController::class, 'index'])->name('pelamar.profil.index');
        Route::put('/pelamar/profil', [\App\Http\Controllers\Pelamar\ProfilController::class, 'update'])->name('pelamar.profil.update');

        Route::get('/pelamar/lowongan', [\App\Http\Controllers\Pelamar\LowonganController::class, 'index'])->name('pelamar.lowongan.index');
        Route::get('/pelamar/lowongan/{lowongan}', [\App\Http\Controllers\Pelamar\LowonganController::class, 'show'])->name('pelamar.lowongan.show');
        Route::get('/pelamar/lowongan/{lowongan}/apply', [\App\Http\Controllers\Pelamar\LowonganController::class, 'apply'])->name('pelamar.lowongan.apply');
        Route::post('/pelamar/lowongan/{lowongan}/apply', [\App\Http\Controllers\Pelamar\LowonganController::class, 'storeApply'])->name('pelamar.lowongan.storeApply');
        Route::post('/pelamar/lowongan/{lowongan}/save', [\App\Http\Controllers\Pelamar\LowonganController::class, 'toggleSave'])->name('pelamar.lowongan.save');

        Route::get('/pelamar/history', [\App\Http\Controllers\Pelamar\HistoryController::class, 'index'])->name('pelamar.history.index');
        Route::get('/pelamar/history/{lamaran}', [\App\Http\Controllers\Pelamar\HistoryController::class, 'show'])->name('pelamar.history.show');
    });

    // Penguji
    Route::middleware('role:penguji')->group(function () {
        Route::get('/penguji/dashboard', [\App\Http\Controllers\Penguji\PengujiController::class, 'dashboard'])->name('penguji.dashboard');
        Route::get('/penguji/pengujian', [\App\Http\Controllers\Penguji\PengujiController::class, 'index'])->name('penguji.pengujian.index');
        Route::get('/penguji/pengujian/{jadwal}', [\App\Http\Controllers\Penguji\PengujiController::class, 'show'])->name('penguji.pengujian.show');
        Route::get('/penguji/pengujian/{jadwal}/uji', [\App\Http\Controllers\Penguji\PengujiController::class, 'uji'])->name('penguji.pengujian.uji');
        Route::post('/penguji/pengujian/{jadwal}', [\App\Http\Controllers\Penguji\PengujiController::class, 'storeNilai'])->name('penguji.pengujian.storeNilai');
    });

    // Kaprodi
    Route::middleware('role:kaprodi')->prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'dashboard'])->name('dashboard');
        Route::get('/pelamar', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'pelamar'])->name('pelamar.index');
        Route::get('/pelamar/{pelamar}', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'showPelamar'])->name('pelamar.show');
    });

    // Notifikasi (semua role)
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/baca', [\App\Http\Controllers\NotifikasiController::class, 'markRead'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [\App\Http\Controllers\NotifikasiController::class, 'markAllRead'])->name('notifikasi.baca.semua');
});

// Profile Management (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
