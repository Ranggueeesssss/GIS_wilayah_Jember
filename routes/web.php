<?php

use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\SpkController;

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

// ── SPK Routes ──────────────────────────────────────────────────────────────
// Halaman Hasil SPK 1: Analisis Potensi & Dinamika Demografi
Route::get('/spk/spk1', [SpkController::class, 'spk1'])->name('spk.spk1');

// Halaman Hasil SPK 2: Prioritas Beban Pelayanan Administrasi
Route::get('/spk/spk2', [SpkController::class, 'spk2'])->name('spk.spk2');

// API JSON endpoint untuk data SPK (dipakai Leaflet.js di Tahap 4)
Route::get('/api/spk/{scenario}', [SpkController::class, 'api'])->name('spk.api');

