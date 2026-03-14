<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailTambangController;
use App\Http\Controllers\WilayahTambangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminJenisTambangRefController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminAboutController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Dashboard / Map view (public)
Route::get('/', [DashboardController::class, 'show'])->name('dashboard');
Route::get('/about', [AboutController::class, 'show'])->name('about');
Route::get('/mining-area/{publicUid}', [DashboardController::class, 'showShared'])->name('dashboard.shared');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.wilayah-tambang.index');
        });

        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::get('/about', [AdminAboutController::class, 'edit'])->name('about.edit');
        Route::put('/about', [AdminAboutController::class, 'update'])->name('about.update');
        Route::get('/jenis-tambang', [AdminJenisTambangRefController::class, 'index'])->name('jenis-tambang.index');
        Route::post('/jenis-tambang', [AdminJenisTambangRefController::class, 'store'])->name('jenis-tambang.store');
        Route::put('/jenis-tambang/{jenisTambang}', [AdminJenisTambangRefController::class, 'update'])->name('jenis-tambang.update');
        Route::delete('/jenis-tambang/{jenisTambang}', [AdminJenisTambangRefController::class, 'destroy'])->name('jenis-tambang.destroy');

        Route::middleware('role:superadmin')->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });

        // Wilayah Tambang CRUD + GeoJSON Upload
        Route::resource('wilayah-tambang', WilayahTambangController::class);
        Route::post('wilayah-tambang/upload-geojson', [WilayahTambangController::class, 'uploadGeojson'])
            ->name('wilayah-tambang.upload-geojson');
    });

    // Detail Tambang CRUD
    Route::resource('detail-tambang', DetailTambangController::class);
});
