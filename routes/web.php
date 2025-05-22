<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\KaderAuthController;
use App\Http\Controllers\Auth\WargaAuthController;
use App\Http\Controllers\warga\WargaController;
use App\Http\Controllers\warga\LokasiController;
use App\Http\Controllers\warga\KeluhanController;
use App\Http\Controllers\warga\InformasiController;
use App\Http\Controllers\warga\ForumController;
use App\Http\Controllers\Warga\ProfileController;
use App\Http\Controllers\Warga\EditProfileController;
use App\Http\Controllers\Warga\TrackingController;

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

// Kader Routes
Route::prefix('kader')->group(function() {
    Route::get('/login', [KaderAuthController::class, 'showLoginForm'])->name('kader.login');
    Route::post('/login', [KaderAuthController::class, 'login'])->name('kader.login.submit');
    Route::post('/logout', [KaderAuthController::class, 'logout'])->name('kader.logout');
    
    // Protected routes for kader
    Route::middleware(['auth:kader'])->group(function() {
        Route::get('/dashboard', function() {
            return view('kader.dashboard');
        })->name('kader.dashboard');
    });
});

// Warga Routes
Route::prefix('warga')->group(function () {
    // Authentication Routes
    Route::get('/login', [WargaAuthController::class, 'showLoginForm'])->name('warga.login');
    Route::post('/login', [WargaAuthController::class, 'login'])->name('warga.login.submit');
    Route::get('/register', [WargaAuthController::class, 'showRegistrationForm'])->name('warga.register');
    Route::post('/logout', [WargaAuthController::class, 'logout'])->name('warga.logout');
    
    // Protected routes for warga
    Route::middleware(['auth:warga'])->group(function() {
        // Dashboard
        Route::get('/dashboard', [WargaController::class, 'dashboard'])->name('warga.dashboard');
        Route::get('/home-warga', [WargaController::class, 'dashboard']);
        
       // Keluhan routes
    Route::get('/keluhan', [KeluhanController::class, 'index'])->name('keluhan');
    Route::post('/keluhan', [KeluhanController::class, 'store'])->name('keluhan.store');
        
        // Event management routes
        Route::get('/eventsaya', [WargaController::class, 'eventSaya'])->name('warga.eventsaya');
        Route::post('/eventsaya/{id}/cancel', [WargaController::class, 'cancel'])->name('warga.event.cancel');
        Route::post('/daftar-event', [WargaController::class, 'daftarEvent'])->name('warga.daftar-event');
        
        // Location routes
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('warga.lokasi');
        Route::post('/wilayah/coordinates', [LokasiController::class, 'getWilayahCoordinates'])->name('wilayah.coordinates');
        Route::post('/dropdown/kelurahan', [LokasiController::class, 'getKelurahan'])->name('dropdown.kelurahan');
        Route::post('/dropdown/rw', [LokasiController::class, 'getRw'])->name('dropdown.rw');
        Route::post('/dropdown/rt', [LokasiController::class, 'getRt'])->name('dropdown.rt');
        
        // Additional warga routes
        Route::get('/riwayat', [WargaController::class, 'index'])->name('warga.riwayat');
        Route::get('/pelaporan', [WargaController::class, 'index'])->name('warga.pelaporan');

        Route::get('/informasi', [InformasiController::class, 'index'])->name('warga.informasi.index');
        Route::get('/informasi/{id}', [InformasiController::class, 'show'])->name('warga.informasi.show');

        Route::get('/forum', [ForumController::class, 'index'])->name('warga.forum.index');
        Route::post('/forum', [ForumController::class, 'store'])->name('warga.forum.store');
        });

        // Route untuk warga
        Route::get('/profile', [ProfileController::class, 'index'])->name('warga.profile');       
    
    // Route untuk menampilkan form edit profile
        Route::get('/profile/edit', [EditProfileController::class, 'edit'])->name('warga.profile.edit');

        
        // Route untuk menyimpan perubahan profile
        Route::put('/profile/update', [EditProfileController::class, 'update'])->name('warga.profile.update');

    Route::get('/riwayat-pengecekan', [TrackingController::class, 'riwayat'])->name('warga.riwayat');


});