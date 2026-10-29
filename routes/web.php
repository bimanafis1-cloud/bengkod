<?php

use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        Route::resource('polis', AdminPoliController::class)->except(['show'])->names('polis');
        Route::resource('dokter', DokterController::class)->except(['show'])->names('dokter');
        Route::resource('pasien', PasienController::class)->except(['show'])->names('pasien');
    });

    Route::resource('obat', ObatController::class)->except(['show']);
});

Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::view('/dashboard', 'dokter.dashboard')->name('dokter.dashboard');
    Route::resource('jadwal-periksa', JadwalPeriksaController::class)
        ->except(['show'])
        ->parameters(['jadwal-periksa' => 'jadwalPeriksa'])
        ->names('jadwal-periksa');
});

Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::view('/dashboard', 'pasien.dashboard')->name('pasien.dashboard');
});
