<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\KaderAuthController;
use App\Http\Controllers\Auth\WargaAuthController;
use App\Http\Controllers\warga\WargaController;
use App\Http\Controllers\warga\fitur_utama\LokasiController;
use App\Http\Controllers\warga\fitur_utama\KeluhanController;
use App\Http\Controllers\warga\fitur_utama\InformasiController;
use App\Http\Controllers\warga\fitur_utama\ForumController;
use App\Http\Controllers\Warga\profile\ProfileController;
use App\Http\Controllers\Warga\profile\EditProfileController;
use App\Http\Controllers\Warga\fitur_utama\TrackingController;
use App\Http\Controllers\Warga\register\RegisterWargaController;
use App\Http\Controllers\Warga\register\datadiriController;
use App\Http\Controllers\Warga\register\inputfotoController;
use App\Http\Controllers\Warga\register\otpController;


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

    Route::get('/register/signup', [RegisterWargaController::class, 'showSignupForm'])->name('register.signup');
    Route::post('/register/signup', [RegisterWargaController::class, 'storeSignup'])->name('register.signup.submit');

    Route::get('/otp', [otpController::class, 'showOtpForm'])->name('register.otp');
    Route::post('/otp', [otpController::class, 'verifyOtp'])->name('register.otp.submit');
    Route::get('/otp/resend', [otpController::class, 'resendOtp'])->name('register.otp.resend');
    Route::get('/register/data-diri', [datadiriController::class, 'showDataDiriForm'])->name('register.data_diri');
    Route::post('/register/data-diri', [datadiriController::class, 'storeDataDiri'])->name('register.datadiri.store');
    
    // AJAX routes
    Route::post('/register/get-kelurahan', [datadiriController::class, 'getKelurahan'])->name('register.get.kelurahan');
    Route::post('/register/get-rw', [datadiriController::class, 'getRw'])->name('register.get.rw');
    Route::post('/register/get-rt', [datadiriController::class, 'getRt'])->name('register.get.rt');

    Route::get('/register/upload-foto', [inputfotoController::class, 'showUploadFotoForm'])->name('register.upload-foto');
    Route::post('/register/upload-foto', [inputfotoController::class, 'storeUploadFoto'])->name('register.upload-foto.store');
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
        Route::delete('/eventsaya/{id}/cancel', [WargaController::class, 'cancelEvent'])->name('warga.event.cancel');
        Route::post('/daftar-event', [WargaController::class, 'daftarEvent'])->name('warga.daftar-event');
        
        // Location routes
       // Halaman utama lokasi
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('warga.lokasi');
    
    // AJAX endpoints untuk dropdown
    Route::post('/lokasi/kelurahan', [LokasiController::class, 'getKelurahan'])->name('lokasi.kelurahan');
    Route::post('/lokasi/rw', [LokasiController::class, 'getRw'])->name('lokasi.rw');
    Route::post('/lokasi/rt', [LokasiController::class, 'getRt'])->name('lokasi.rt');
    
    // AJAX endpoint untuk mendapatkan koordinat wilayah
    Route::post('/lokasi/coordinates', [LokasiController::class, 'getWilayahCoordinates'])->name('lokasi.coordinates');
        
        // Additional warga routes
        Route::get('/riwayat', [WargaController::class, 'index'])->name('warga.riwayat');
        Route::get('/pelaporan', [WargaController::class, 'index'])->name('warga.pelaporan');

        Route::get('/informasi', [InformasiController::class, 'index'])->name('warga.informasi.index');
        Route::get('/informasi/{id}', [InformasiController::class, 'show'])->name('warga.informasi.show');

        Route::get('/forum', [ForumController::class, 'index'])->name('warga.forum.index');
        Route::post('/forum', [ForumController::class, 'store'])->name('warga.forum.store');

                // Route untuk warga
        Route::get('/profile', [ProfileController::class, 'index'])->name('warga.profile');       
        Route::get('/profile/edit', [EditProfileController::class, 'edit'])->name('warga.profile.edit');
        
                // Route untuk menyimpan perubahan profile
        Route::put('/profile/update', [EditProfileController::class, 'update'])->name('warga.profile.update');
        Route::get('/riwayat-pengecekan', [TrackingController::class, 'riwayat'])->name('warga.riwayat');
        });
});