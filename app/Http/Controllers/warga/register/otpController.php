<?php
namespace App\Http\Controllers\warga\register;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class otpController extends Controller
{
    public function showOtpForm()
    {
        if (!Session::has('telepon')) {
            return redirect()->route('register.signup')->with('error', 'Silakan isi nomor telepon terlebih dahulu.');
        }
        
        // Generate OTP 5 digit dan simpan ke session
        $otpCode = rand(10000, 99999); // Generate 5 digit random number
        Session::put('kode_otp', $otpCode);
        
        // Optional: Simpan waktu expired OTP (5 menit dari sekarang)
        Session::put('otp_expires_at', now()->addMinutes(5));
        
        return view('register.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:5', // Ubah ke 5 digit sesuai UI
        ]);

        // Cek apakah OTP sudah expired
        if (Session::has('otp_expires_at') && now() > Session::get('otp_expires_at')) {
            return redirect()->back()->with('error', 'Kode OTP telah expired. Silakan minta kode baru.');
        }

        // Ambil OTP dari session
        $enteredOtp = $request->otp;
        $validOtp = Session::get('kode_otp');

        if (!$validOtp || $enteredOtp != $validOtp) {
            return redirect()->back()->with('error', 'Kode OTP tidak valid.');
        }

        // Set flag bahwa OTP sudah terverifikasi
        Session::put('otp_verified', true);
        
        // Hapus OTP dari session untuk keamanan
        Session::forget(['kode_otp', 'otp_expires_at']);

        // Redirect ke halaman data diri
        return redirect()->route('register.data_diri')->with('success', 'OTP berhasil diverifikasi!');
    }

    // Method untuk generate ulang OTP
    public function resendOtp()
    {
        if (!Session::has('telepon')) {
            return redirect()->route('register.signup')->with('error', 'Silakan isi nomor telepon terlebih dahulu.');
        }

        // Generate OTP baru
        $otpCode = rand(10000, 99999);
        Session::put('kode_otp', $otpCode);
        Session::put('otp_expires_at', now()->addMinutes(5));

        return redirect()->back()->with('success', 'Kode OTP baru telah dikirim!');
    }
}