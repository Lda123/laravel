<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warga;
use Illuminate\Support\Facades\Hash;

class WargaAuthController extends Controller
{
    /**
     * Menampilkan form login warga
     */
    public function showLoginForm()
    {
        return view('auth.login_warga');
    }

    /**
     * Proses login warga dengan pengecekan password yang diperbarui
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'telepon' => 'required|string',
            'password' => 'required|string',
        ], [
            'telepon.required' => 'Nomor telepon wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Cari warga berdasarkan telepon
        $warga = Warga::where('telepon', $credentials['telepon'])->first();

        // Verifikasi password manual untuk memastikan password terbaru bisa digunakan
        if ($warga && Hash::check($credentials['password'], $warga->password)) {
            Auth::guard('warga')->login($warga);
            $request->session()->regenerate();
            return redirect()->intended('/warga/dashboard');
        }

        return back()->withErrors([
            'telepon' => 'Nomor telepon atau password salah!',
        ])->withInput($request->only('telepon'));
    }

    /**
     * Proses logout warga
     */
    public function logout(Request $request)
    {
        Auth::guard('warga')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/warga/login');
    }
}