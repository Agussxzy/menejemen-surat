<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SuratMasukController;
use App\Http\Controllers\API\SuratKeluarController;
use App\Http\Controllers\API\DisposisiController;
use App\Http\Controllers\API\LaporanController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Surat Masuk routes
    Route::apiResource('surat-masuk', SuratMasukController::class);
    Route::get('/surat-masuk/{id}/download', [SuratMasukController::class, 'downloadFile']);

    // Surat Keluar routes
    Route::apiResource('surat-keluar', SuratKeluarController::class);
    Route::get('/surat-keluar/{id}/download', [SuratKeluarController::class, 'downloadFile']);

    // Disposisi routes
    Route::apiResource('disposisi', DisposisiController::class);

    // Laporan routes
    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::get('/laporan/{id}', [LaporanController::class, 'show']);
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy']);
    Route::post('/laporan/surat-masuk/generate', [LaporanController::class, 'generateLaporanSuratMasuk']);
    Route::post('/laporan/surat-keluar/generate', [LaporanController::class, 'generateLaporanSuratKeluar']);
});