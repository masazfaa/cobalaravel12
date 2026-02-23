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

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
