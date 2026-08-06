<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');
Route::get('/lowongan', [\App\Http\Controllers\LandingController::class, 'lowonganList'])->name('landing.lowongan.index');
Route::get('/lowongan/{lowongan}', [\App\Http\Controllers\LandingController::class, 'show'])->name('landing.lowongan.show');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        // Jika role null atau tidak dikenal, logout dan arahkan ke login
        $validRoles = ['admin', 'pelamar', 'penguji', 'kaprodi'];
        if (!$role || !in_array($role, $validRoles)) {
            \Illuminate\Support\Facades\Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Akun Anda belum memiliki akses. Hubungi administrator.']);
        }

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
        Route::post('admin/user', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.user.store');
        Route::put('admin/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.user.update');
        Route::delete('admin/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.user.destroy');

        // Penguji Management
        Route::get('admin/penguji', [\App\Http\Controllers\Admin\PengujiController::class, 'index'])->name('admin.penguji.index');
        Route::get('admin/penguji/{penguji}', [\App\Http\Controllers\Admin\PengujiController::class, 'show'])->name('admin.penguji.show');
        Route::post('admin/penguji', [\App\Http\Controllers\Admin\PengujiController::class, 'store'])->name('admin.penguji.store');
        Route::delete('admin/penguji/{penguji}', [\App\Http\Controllers\Admin\PengujiController::class, 'destroy'])->name('admin.penguji.destroy');

        Route::resource('admin/lowongan', \App\Http\Controllers\Admin\LowonganController::class)
            ->names('admin.lowongan')
            ->parameters(['lowongan' => 'lowongan']);
        Route::patch('admin/lowongan/{lowongan}/toggle-status', [\App\Http\Controllers\Admin\LowonganController::class, 'toggleStatus'])->name('admin.lowongan.toggleStatus');
        Route::get('admin/lowongan/{lowongan}/berita-acara', [\App\Http\Controllers\Admin\LowonganController::class, 'beritaAcara'])->name('admin.lowongan.beritaAcara');

        // Lamaran Management (View/Update/Hapus)
        Route::get('admin/lowongan/{lowongan}/lamaran', [\App\Http\Controllers\Admin\LamaranController::class, 'index'])->name('admin.lamaran.index');
        Route::get('admin/lowongan/{lowongan}/lamaran/export', [\App\Http\Controllers\Admin\LamaranController::class, 'exportExcel'])->name('admin.lamaran.export');
        Route::get('admin/lowongan/{lowongan}/lamaran/export-nilai', [\App\Http\Controllers\Admin\LamaranController::class, 'exportNilai'])->name('admin.lamaran.exportNilai');
        Route::get('admin/lamaran/{lamaran}', [\App\Http\Controllers\Admin\LamaranController::class, 'show'])->name('admin.lamaran.show');
        Route::get('admin/lamaran/{lamaran}/cetak', [\App\Http\Controllers\Admin\LamaranController::class, 'cetak'])->name('admin.lamaran.cetak');
        Route::put('admin/lamaran/{lamaran}', [\App\Http\Controllers\Admin\LamaranController::class, 'update'])->name('admin.lamaran.update');
        Route::delete('admin/lamaran/{lamaran}', [\App\Http\Controllers\Admin\LamaranController::class, 'destroy'])->name('admin.lamaran.destroy');
        Route::delete('admin/lowongan/{lowongan}/lamaran/withdrawn', [\App\Http\Controllers\Admin\LamaranController::class, 'destroyWithdrawn'])->name('admin.lamaran.destroyWithdrawn');
        Route::get('admin/lowongan/{lowongan}/lamaran/filter', [\App\Http\Controllers\Admin\LamaranController::class, 'filter'])->name('admin.lamaran.filter');

        // Pelamar Management (Global)
        Route::get('admin/pelamar', [\App\Http\Controllers\Admin\PelamarController::class, 'index'])->name('admin.pelamar.index');
        Route::post('admin/pelamar/import', [\App\Http\Controllers\Admin\PelamarController::class, 'import'])->name('admin.pelamar.import');
        Route::get('admin/pelamar/{pelamar}', [\App\Http\Controllers\Admin\PelamarController::class, 'show'])->name('admin.pelamar.show');

        // Jadwal Seleksi Manajemen
        Route::get('admin/jadwal', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'index'])->name('admin.jadwal.index');
        Route::get('admin/jadwal/create', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'create'])->name('admin.jadwal.create');
        Route::post('admin/jadwal', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'store'])->name('admin.jadwal.store');
        Route::put('admin/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'update'])->name('admin.jadwal.update');
        Route::put('admin/jadwal-group', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'updateGroup'])->name('admin.jadwal.updateGroup');
        Route::delete('admin/jadwal-group', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'destroyGroup'])->name('admin.jadwal.destroyGroup');
        Route::delete('admin/jadwal/{jadwal}', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'destroy'])->name('admin.jadwal.destroy');

        // API Endpoints (penjadwalan)
        Route::get('admin/api/lowongan-by-prodi', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiLowongan'])->name('admin.api.lowongan');
        Route::get('admin/api/penguji-by-prodi', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiPenguji'])->name('admin.api.penguji');
        Route::get('admin/api/pelamar-by-lowongan', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiPelamar'])->name('admin.api.pelamar');
        Route::get('admin/api/sesi-tersedia', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiAvailableSessions'])->name('admin.api.sesi');
        Route::get('admin/api/sesi-taken-all', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiSesiTakenAll'])->name('admin.api.sesi.taken');
        Route::get('admin/api/sesi-taken-pelamar', [\App\Http\Controllers\Admin\JadwalSeleksiController::class, 'apiSesiTakenPelamar'])->name('admin.api.sesi.taken.pelamar');
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
        Route::put('/pelamar/history/{lamaran}/withdraw', [\App\Http\Controllers\Pelamar\HistoryController::class, 'withdraw'])->name('pelamar.history.withdraw');
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
        Route::get('/pelamar/filter', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'filterPelamar'])->name('pelamar.filter');
        Route::get('/pelamar/{pelamar}', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'showPelamar'])->name('pelamar.show');
        Route::patch('/lamaran/{lamaran}/toggle-rekomendasi', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'toggleRekomendasi'])->name('lamaran.toggleRekomendasi');
    });

    // Notifikasi (semua role)
    Route::get('/notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/baca', [\App\Http\Controllers\NotifikasiController::class, 'markRead'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [\App\Http\Controllers\NotifikasiController::class, 'markAllRead'])->name('notifikasi.baca.semua');

    // Switch role
    Route::post('/role/switch', [\App\Http\Controllers\RoleSwitchController::class, 'switch'])->name('role.switch');

    // Pengaturan akun (semua role: admin, pelamar, penguji, kaprodi)
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.password.update');
    Route::post('/settings/foto', [\App\Http\Controllers\SettingController::class, 'updateFoto'])->name('settings.foto.update');
    Route::delete('/settings/foto', [\App\Http\Controllers\SettingController::class, 'deleteFoto'])->name('settings.foto.delete');
});

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
