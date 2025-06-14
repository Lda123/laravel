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
use App\Http\Controllers\Kader\DashboardController;
use App\Http\Controllers\Kader\TrackingHarianController;
use App\Http\Controllers\Kader\LaporanBulananController;
use App\Http\Controllers\Kader\DataWargaController;
use App\Http\Controllers\Kader\ForumKaderController;
use App\Http\Controllers\Kader\BukuPanduanController;
use App\Http\Controllers\Kader\VideoPelatihanController;
use App\Http\Controllers\Kader\ProfileKaderController;
use App\Http\Controllers\Kader\EditProfileKaderController;
use App\Http\Controllers\Kader\VideoSayaController;
use App\Http\Controllers\Warga\fitur_utama\RiwayatController;
use App\Http\Controllers\Warga\fitur_utama\LaporanController;
use App\Http\Controllers\Auth\AdminAuthController;
Use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Warga\fitur_utama\InformasiSayaController;

Route::get('/', function () {
    return view('index');
})->name('welcome');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Admin Dashboard Route (protected)
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
        // Edukasi Management
        Route::get('/edukasi', [AdminDashboardController::class, 'edukasi'])->name('admin.edukasi');
        Route::get('/edukasi/create', [AdminDashboardController::class, 'createEdukasi'])->name('admin.edukasi.create');
        Route::post('/edukasi/store', [AdminDashboardController::class, 'storeEdukasi'])->name('admin.edukasi.store');
        Route::get('/edukasi/{id}/edit', [AdminDashboardController::class, 'editEdukasi'])->name('admin.edukasi.edit');
        Route::put('/edukasi/{id}/update', [AdminDashboardController::class, 'updateEdukasi'])->name('admin.edukasi.update');
        Route::delete('/edukasi/{id}/delete', [AdminDashboardController::class, 'destroyEdukasi'])->name('admin.edukasi.destroy');
        
        // Placeholder routes for other sections
        Route::get('/manajemen-pengguna', function () { return '#'; })->name('admin.manajemen-pengguna');
        Route::get('/manajemen-forum', function () { return '#'; })->name('admin.manajemen-forum');
        Route::get('/aduan-warga', function () { return '#'; })->name('admin.aduan-warga');
    });
});
// Kader Routes
Route::prefix('kader')->group(function() {
    Route::get('/login', [KaderAuthController::class, 'showLoginForm'])->name('kader.login');
    Route::post('/login', [KaderAuthController::class, 'login'])->name('kader.login.submit');
    Route::post('/logout', [KaderAuthController::class, 'logout'])->name('kader.logout');
    
    // Protected routes for kader
    Route::middleware(['auth:kader'])->group(function() {
       
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('kader.dashboard');
    
    // Pelatihan
    Route::get('/pelatihan-saya', [DashboardController::class, 'pelatihanSaya'])->name('pelatihan.saya');
    Route::post('/daftar-pelatihan', [DashboardController::class, 'daftarPelatihan'])->name('daftar-pelatihan');
    Route::post('/daftar-pelatihan-ajax', [DashboardController::class, 'daftarPelatihanAjax'])->name('daftar.pelatihan'); // untuk kompatibilitas AJAX
    Route::post('/batalkan-pelatihan', [DashboardController::class, 'batalkanPelatihan'])->name('batalkan-pelatihan');
    
    // Laporan - FIXED ROUTES
    Route::get('/laporan-harian', [TrackingHarianController::class, 'index'])->name('laporan.harian');
    Route::post('/laporan-harian', [TrackingHarianController::class, 'store'])->name('tracking-harian.store');
    Route::get('/laporan-harian/{tracking}', [TrackingHarianController::class, 'show'])->name('laporan.harian.show');
    Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporan.index');
    Route::get('/laporan-bulanan/download-template', [LaporanBulananController::class, 'downloadTemplate'])->name('laporan.download-template');
    Route::post('/laporan-bulanan/upload', [LaporanBulananController::class, 'uploadLaporan'])->name('laporan.upload');    
    // Data Warga
    Route::get('/data-warga', [DataWargaController::class, 'index'])->name('data-warga');
    Route::post('/get-kelurahan', [DataWargaController::class, 'getKelurahan'])->name('data-warga.get-kelurahan');
    Route::post('/get-rw', [DataWargaController::class, 'getRw'])->name('data-warga.get-rw');
    Route::post('/get-rt', [DataWargaController::class, 'getRt'])->name('data-warga.get-rt');

    Route::get('/forum', [ForumKaderController::class, 'index'])->name('kader.forum');
    Route::post('/forum/post', [ForumKaderController::class, 'storePost'])->name('forum.post.store');
    Route::post('/forum/comment', [ForumKaderController::class, 'storeComment'])->name('forum.comment.store');

    Route::get('/', [BukuPanduanController::class, 'index'])->name('kader.buku-panduan');
    Route::get('/kader/buku-panduan/search', [BukuPanduanController::class, 'search'])->name('buku-panduan.search');
    Route::get('/buku-panduan/cover/{filename}', [BukuPanduanController::class, 'serveCover'])->name('buku-panduan.cover');
    Route::get('/download/{id}', [BukuPanduanController::class, 'downloadPdf'])->name('buku-panduan.download');
    Route::get('/stream/{id}', [BukuPanduanController::class, 'streamPdf'])->name('buku-panduan.stream');
    Route::get('/{id}/info', [BukuPanduanController::class, 'getFileInfo'])->name('buku-panduan.info');
    Route::get('/debug', [BukuPanduanController::class, 'debug'])->name('buku-panduan.debug');
    
    Route::get('/video-pelatihan', [VideoPelatihanController::class, 'index'])->name('kader.video-pelatihan');
    Route::get('/video-pelatihan/{id}', [VideoPelatihanController::class, 'view'])->name('kader.video_detail');
    Route::post('/video-pelatihan/save', [VideoPelatihanController::class, 'saveVideo'])->name('kader.video-pelatihan.save');
    Route::post('/video-pelatihan/{id}/increment-views', [VideoPelatihanController::class, 'incrementViews'])->name('kader.video.increment-views');

    Route::get('/profile', [ProfileKaderController::class, 'show'])->name('kader.profile');
    Route::get('/profile/settings', [EditProfileKaderController::class, 'edit'])->name('kader.settings');
    Route::put('/profile/update', [EditProfileKaderController::class, 'update'])->name('kader.update-profile');
    Route::delete('/profile/photo', [EditProfileKaderController::class, 'deletePhoto'])->name('kader.delete-photo');

    Route::get('/video-saya', [VideoSayaController::class, 'index'])->name('kader.video-saya');
    Route::delete('/video-saya/{id}', [VideoSayaController::class, 'destroy'])->name('kader.video-saya.destroy');
    });
});

// Warga Routes
Route::prefix('warga')->group(function () {
    // Authentication Routes
    Route::get('/login', [WargaAuthController::class, 'showLoginForm'])->name('warga.login');
    Route::post('/login', [WargaAuthController::class, 'login'])->name('warga.login.submit');
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
        Route::prefix('events')->group(function () {
        Route::post('/daftar', [WargaController::class, 'daftarEvent'])->name('warga.daftar-event');
        Route::post('/register/{id}', [WargaController::class, 'registerEvent'])->name('warga.register-event');
        Route::get('/saya', [WargaController::class, 'eventSaya'])->name('warga.eventsaya');
        Route::post('/cancel', [WargaController::class, 'cancelEvent'])->name('warga.cancel-event');
        });
        
        Route::get('/warga/lokasi', [LokasiController::class, 'index'])->name('warga.lokasi');
        Route::post('/lokasi/get-kelurahan', [LokasiController::class, 'getKelurahan'])->name('lokasi.get-kelurahan');
        Route::post('/lokasi/get-rw', [LokasiController::class, 'getRw'])->name('lokasi.get-rw');
        Route::post('/lokasi/get-rt', [LokasiController::class, 'getRt'])->name('lokasi.get-rt');
        Route::post('/lokasi/search', [LokasiController::class, 'cariLokasi'])->name('lokasi.search');

        // Route untuk mendapatkan data peta
        Route::get('/lokasi/map-data', [LokasiController::class, 'getMapData'])->name('lokasi.map-data');

        // Route untuk mendapatkan data chart
        Route::get('/lokasi/chart-data', [LokasiController::class, 'updateChart'])->name('lokasi.chart-data');
        
        // Additional warga routes
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
        Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
        // Pelaporan routes
        Route::get('/pelaporan', [LaporanController::class, 'index'])->name('warga.pelaporan');
        Route::post('/pelaporan', [LaporanController::class, 'store'])->name('pelaporan.store');

        // AJAX routes untuk dropdown hierarkis
        Route::post('/get-kelurahan', [LaporanController::class, 'getKelurahan'])->name('get-kelurahan');
        Route::post('/get-rw', [LaporanController::class, 'getRw'])->name('get-rw');
        Route::post('/get-rt', [LaporanController::class, 'getRt'])->name('get-rt');

        // Riwayat laporan
        Route::get('/riwayat-laporan', [LaporanController::class, 'history'])->name('riwayat-laporan');

        Route::get('/warga/informasi', [InformasiController::class, 'index'])->name('warga.informasi');
        Route::get('/warga/informasi/{id}', [InformasiController::class, 'view'])->name('warga.informasi.view');
        Route::post('/warga/informasi/{id}/increment-views', [InformasiController::class, 'incrementViews'])->name('warga.informasi.increment-views');
        Route::post('/warga/informasi/save', [InformasiController::class, 'saveEdukasi'])->name('warga.informasi.save');

        Route::get('/forum', [ForumController::class, 'index'])->name('warga.forum');
        Route::post('/forum/post', [ForumController::class, 'storePost'])->name('warga.forum.post.store');
        Route::post('/forum/comment', [ForumController::class, 'storeComment'])->name('warga.forum.comment.store');

        Route::get('/', [ProfileController::class, 'index'])->name('warga.profile');
        Route::get('/data', [ProfileController::class, 'getProfileData'])->name('warga.profile.data');
        Route::post('/upload-picture', [ProfileController::class, 'uploadProfilePicture'])->name('warga.profile.upload-picture');
        
        // Edit profile routes
        Route::get('/edit', [EditProfileController::class, 'edit'])->name('warga.profile.edit');
        Route::put('/update', [EditProfileController::class, 'update'])->name('warga.profile.update');
        Route::delete('/delete-photo', [EditProfileController::class, 'deletePhoto'])->name('warga.profile.delete-photo');
        Route::get('/riwayat-pengecekan', [TrackingController::class, 'riwayat'])->name('warga.riwayat');

        Route::get('/warga/informasi-saya', [InformasiSayaController::class, 'index'])->name('warga.informasi-saya');
        Route::delete('/warga/informasi-saya/{id}', [InformasiSayaController::class, 'destroy'])->name('warga.informasi-saya.destroy');
    });
});