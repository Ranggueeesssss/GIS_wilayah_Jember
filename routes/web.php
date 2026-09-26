<?php

use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\StatistikController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('kecamatan.index');
});

// Resource routes untuk manajemen Kecamatan (CRUD, Search, Sort)
Route::resource('kecamatan', KecamatanController::class);

// Resource routes untuk manajemen Data Statistik BPS
Route::resource('statistik', StatistikController::class)->except(['show']);

