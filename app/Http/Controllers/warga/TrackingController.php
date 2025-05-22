<?php

namespace App\Http\Controllers\Warga;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{

public function riwayat()
{
    $id_warga = Auth::guard('warga')->id(); // atau session('id_warga')

    // Data pengecekan
    $tracking = DB::table('tracking_harian')
        ->where('warga_id', $id_warga)
        ->orderByDesc('tanggal_pantau')
        ->orderByDesc('dibuat_pada')
        ->get();

    // Data terbaru
    $latest = DB::table('tracking_harian')
        ->where('warga_id', $id_warga)
        ->orderByDesc('tanggal_pantau')
        ->orderByDesc('dibuat_pada')
        ->first();

    // Statistik
    $stats = DB::table('tracking_harian')
        ->selectRaw('
            COUNT(*) as total,
            SUM(status_kesehatan = "Terkena DBD") as dbd_count,
            SUM(status_lingkungan = "Kotor") as kotor_count
        ')
        ->where('warga_id', $id_warga)
        ->first();

        return view('warga.riwayat', compact('tracking', 'latest', 'stats'));
}
}