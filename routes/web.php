<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Default landing page
Route::get('/', function () {
    return view('landing');
});

// Autentikasi group
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Smart redirect: /dashboard akan otomatis mengarahkan ke dashboard yang sesuai rolenya
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return redirect()->route("{$role}.dashboard");
    })->name('dashboard');

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Prodi Management
        Route::resource('admin/prodi', \App\Http\Controllers\Admin\ProdiController::class)
            ->names('admin.prodi')
            ->parameters(['prodi' => 'prodi']);

        // Dosen Management
        Route::post('admin/prodi/{prodi}/dosen', [\App\Http\Controllers\Admin\DosenController::class, 'store'])->name('admin.dosen.store');
        Route::put('admin/dosen/{dosen}', [\App\Http\Controllers\Admin\DosenController::class, 'update'])->name('admin.dosen.update');
        Route::delete('admin/dosen/{dosen}', [\App\Http\Controllers\Admin\DosenController::class, 'destroy'])->name('admin.dosen.destroy');

        // User Management
        Route::get('admin/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.user.index');
        Route::put('admin/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.user.update');

        // Penguji Management
        Route::get('admin/penguji', [\App\Http\Controllers\Admin\PengujiController::class, 'index'])->name('admin.penguji.index');
        Route::post('admin/penguji', [\App\Http\Controllers\Admin\PengujiController::class, 'store'])->name('admin.penguji.store');
        Route::delete('admin/penguji/{penguji}', [\App\Http\Controllers\Admin\PengujiController::class, 'destroy'])->name('admin.penguji.destroy');
    });

    // Pelamar
    Route::middleware('role:pelamar')->get('/pelamar/dashboard', function () {
        return view('pelamar.dashboard');
    })->name('pelamar.dashboard');

    // Penguji
    Route::middleware('role:penguji')->get('/penguji/dashboard', function () {
        return view('penguji.dashboard');
    })->name('penguji.dashboard');

    // Kaprodi
    Route::middleware('role:kaprodi')->get('/kaprodi/dashboard', function () {
        return view('kaprodi.dashboard');
    })->name('kaprodi.dashboard');
});

// Profile Management (Breeze default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
