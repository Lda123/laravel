<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kader;

class KaderAuthController extends Controller
{
    /**
     * Show the form for authenticating a kader.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(): \Illuminate\Contracts\View\View
    {
        return view('auth.login_kader');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama_lengkap' => 'required|string',
            'password' => 'required|string',
        ], [
            'nama_lengkap.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        if (Auth::guard('kader')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/kader/dashboard');
        }

        return back()
            ->withErrors([
                'nama_lengkap' => 'Username atau password salah',
            ])
            ->withInput($request->only('nama_lengkap'));
    }

    public function logout(Request $request)
    {
        Auth::guard('kader')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/kader/login');
    }
}