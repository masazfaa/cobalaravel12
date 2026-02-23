<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsSuperadmin;
use App\Http\Controllers\Home;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/', [Home::class, 'index']);
Route::get('/geoserver', [Home::class, 'geoserver']);
Route::get('/cesium', [Home::class, 'cesium']);
Route::get('/cesiumion', [Home::class, 'cesiumion']);

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =======================================================
    // -- CRUD TAB NON GEOSERVER --
    // =======================================================

    // 1. Admin / Batas Wilayah (Polygon)
    Route::get('/dashboard/admin-kw/export', [DashboardController::class, 'exportAdminKw'])->name('admin-kw.export');
    Route::post('/dashboard/admin-kw', [DashboardController::class, 'storeAdminKw'])->name('admin-kw.store');
    Route::put('/dashboard/admin-kw/{id}', [DashboardController::class, 'updateAdminKw'])->name('admin-kw.update');
    Route::post('/dashboard/admin-kw/import', [DashboardController::class, 'importAdminKw'])->name('admin-kw.import');
    Route::delete('/dashboard/admin-kw/{id}', [DashboardController::class, 'destroyAdminKw'])->name('admin-kw.destroy');

    // 2. Jalan (LineString)
    Route::get('/dashboard/jalan-kw/export', [DashboardController::class, 'exportJalanKw'])->name('jalan-kw.export');
    Route::post('/dashboard/jalan-kw', [DashboardController::class, 'storeJalanKw'])->name('jalan-kw.store');
    Route::put('/dashboard/jalan-kw/{id}', [DashboardController::class, 'updateJalanKw'])->name('jalan-kw.update');
    Route::post('/dashboard/jalan-kw/import', [DashboardController::class, 'importJalanKw'])->name('jalan-kw.import');
    Route::delete('/dashboard/jalan-kw/{id}', [DashboardController::class, 'destroyJalanKw'])->name('jalan-kw.destroy');

    // 3. Masjid (Point)
    Route::get('/dashboard/masjid-kw/export', [DashboardController::class, 'exportMasjidKw'])->name('masjid-kw.export');
    Route::post('/dashboard/masjid-kw', [DashboardController::class, 'storeMasjidKw'])->name('masjid-kw.store');
    Route::put('/dashboard/masjid-kw/{id}', [DashboardController::class, 'updateMasjidKw'])->name('masjid-kw.update');
    Route::post('/dashboard/masjid-kw/import', [DashboardController::class, 'importMasjidKw'])->name('masjid-kw.import');
    Route::delete('/dashboard/masjid-kw/{id}', [DashboardController::class, 'destroyMasjidKw'])->name('masjid-kw.destroy');
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(IsSuperadmin::class)->group(function () {

            Route::get('/kelola-user', [UserController::class, 'index'])->name('user.index');

            Route::patch('/kelola-user/{id}/approve', [UserController::class, 'approve'])->name('user.approve');

            Route::delete('/kelola-user/{id}/delete', [UserController::class, 'destroy'])->name('user.destroy');

        });

});

require __DIR__.'/auth.php';
