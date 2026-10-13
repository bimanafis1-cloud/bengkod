<?php

use App\Http\Controllers\AuthController;
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
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
});

Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::view('/dashboard', 'dokter.dashboard')->name('dokter.dashboard');
});

Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::view('/dashboard', 'pasien.dashboard')->name('pasien.dashboard');
});
