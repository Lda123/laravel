<?php
namespace App\Http\Controllers\warga\register;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
class registerWargaController extends Controller
{
    public function showSignupForm()
    {
        return view('register.signup');
    }

    public function storeSignup(Request $request)
    {
        $validated = $request->validate([
            'telepon' => 'required|string|max:20|unique:warga,telepon',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Simpan telepon dan password ke session
        Session::put('telepon', $validated['telepon']);
        Session::put('password', $validated['password']);

        // Redirect ke halaman OTP
        return redirect()->route('register.otp');
    }
}
