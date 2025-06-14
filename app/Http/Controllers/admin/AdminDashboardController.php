<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan admin yang sedang login
        $admin = Auth::guard('admin')->user();
        
        // Menentukan greeting berdasarkan waktu
        $hour = date('H');
        if ($hour < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        // Query untuk materi edukasi dengan filter
        $educations = Edukasi::query()
            ->when($request->type === 'video', function ($query) {
                return $query->where('tipe', 'Video');
            })
            ->when($request->type === 'article', function ($query) {
                return $query->where('tipe', 'Artikel');
            })
            ->when($request->audience === 'warga', function ($query) {
                return $query->where('kategori_pengguna', 'Warga');
            })
            ->when($request->audience === 'kader', function ($query) {
                return $query->where('kategori_pengguna', 'Kader');
            })
            ->with(['savedByKaders', 'savedByWargas'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($education) {
                // Transform thumbnail_url to full URL if it's stored in storage
                if ($education->thumbnail_url && !filter_var($education->thumbnail_url, FILTER_VALIDATE_URL)) {
                    $education->thumbnail_url = Storage::url($education->thumbnail_url);
                }
                return $education;
            });

        return view('admin.dashboard', [
            'admin' => $admin,
            'greeting' => $greeting,
            'educations' => $educations
        ]);
    }
}