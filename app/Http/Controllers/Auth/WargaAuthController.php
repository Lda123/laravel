<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warga; // Pastikan model Warga sudah dibuat

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
     * Proses login warga
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'telepon' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba melakukan autentikasi
        if (Auth::guard('warga')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/warga/dashboard');
        }

        return back()->withErrors([
            'telepon' => 'Nomor telepon atau password salah!',
        ])->onlyInput('telepon');
    }

    /**
     * Proses logout warga
     */
    public function logout(Request $request)
    {
        Auth::guard('warga')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/warga/login_warga');
    }
}