<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home;

Route::get('/', [Home::class, 'index']);
Route::get('/geoserver', [Home::class, 'geoserver']);
Route::get('/cesium', [Home::class, 'cesium']);
Route::get('/cesiumion', [Home::class, 'cesiumion']);
