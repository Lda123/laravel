<?php
namespace App\Http\Controllers\warga\register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Warga;
use App\Models\Kecamatan;

class RegisterWargaController extends Controller
{
    
    public function showSignupForm()
    {
        return view('register.signup');
    }

    public function processSignup(Request $request)
    {
        $validated = $request->validate([
            'telepon' => 'required|numeric|unique:warga,telepon',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*]{8,}$/'
            ],
            'confirm_password' => 'required|same:password',
        ], [
            'telepon.unique' => 'Nomor telepon sudah terdaftar!',
            'password.regex' => 'Password harus mengandung huruf dan angka!',
            'confirm_password.same' => 'Konfirmasi kata sandi tidak sesuai!',
        ]);

        // Simpan sementara ke session
        Session::put('telepon', $request->telepon);
        Session::put('password', Hash::make($request->password));
        $otp = rand(10000, 99999);
        Session::put('kode_otp', $otp);

        return redirect()->route('register.otp');
    }

    public function showOtpForm()
    {
        if (!Session::has('kode_otp')) {
            return redirect()->route('register.signup');
        }
        return view('register.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        if ($request->otp == Session::get('kode_otp')) {
           // Berhasil OTP, tetap simpan sesi untuk data berikutnya
        Session::forget('kode_otp');
        return redirect()->route('register.data_diri');

        } else {
            return back()->with('error', 'Kode OTP salah, silakan coba lagi!');
        }
    }

    public function showDataDiriForm()
{
    $kecamatan = Kecamatan::orderBy('nama_kecamatan')->get();
    return view('register.data_diri', compact('kecamatan'));
}

public function submitDataDiri(Request $request)
{
    $validated = $request->validate([
        'nik' => $request->nik,
        'nama_lengkap' => $request->nama_lengkap,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'jenis_kelamin' => $request->jenis_kelamin,
        'alamat_lengkap' => $request->alamat_lengkap,
        'rt_id' => $request->rt_id
    ]);

    session($validated);

    return redirect()->route('register.upload_ktp');
}

    public function showFotoForm()
{
    if (!Session::has('nik')) {
        return redirect()->route('warga.register')->with('error', 'Sesi pendaftaran tidak ditemukan.');
    }

    return view('warga.input_foto');
}

public function submitFoto(Request $request)
{
    $request->validate([
        'ktp' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        'foto_diri' => 'required|image|mimes:jpg,jpeg,png|max:10240',
    ]);

    $nik = Session::get('nik');
    $nama = Session::get('nama_lengkap');
    $kecamatan = Session::get('kecamatan');
    $kelurahan = Session::get('kelurahan');
    $rt_rw = Session::get('rt_rw');

    $timestamp = now()->format('His_dmY');
    $baseFilename = strtolower(preg_replace('/[^a-z0-9]/i', '_', "{$nama}_{$kecamatan}_{$kelurahan}_{$rt_rw}_{$timestamp}"));

    $ktpPath = $request->file('ktp')->storeAs('public/foto_ktp', "ktp_{$baseFilename}.png");
    $fotoDiriPath = $request->file('foto_diri')->storeAs('public/foto_diri_ktp', "diri_{$baseFilename}.png");

    $warga = new Warga();
    $warga->nik = $nik;
    $warga->nama_lengkap = Session::get('nama_lengkap');
    $warga->tempat_lahir = Session::get('tempat_lahir');
    $warga->tanggal_lahir = Session::get('tanggal_lahir');
    $warga->jenis_kelamin = Session::get('jenis_kelamin');
    $warga->alamat_lengkap = Session::get('alamat_lengkap');
    $warga->rt_id = Session::get('rt_id');
    $warga->telepon = Session::get('telepon');
    $warga->password = Hash::make(Session::get('password'));
    $warga->foto_ktp = basename($ktpPath);
    $warga->foto_diri_ktp = basename($fotoDiriPath);

    $warga->save();

    Session::flush();

    return redirect()->route('warga.login')->with('success', 'Pendaftaran berhasil! Silakan login.');
}
}
