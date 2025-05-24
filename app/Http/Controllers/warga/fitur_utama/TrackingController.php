<?php

namespace App\Http\Controllers\Warga\fitur_utama;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function riwayat()
    {
        $id_warga = Auth::guard('warga')->id();

        // Ambil semua riwayat tracking berdasarkan warga
        $tracking = DB::table('tracking_harian')
            ->where('warga_id', $id_warga)
            ->orderByDesc('tanggal')
            ->orderByDesc('dibuat_pada')
            ->get();

        // Ambil data tracking terbaru
        $latest = DB::table('tracking_harian')
            ->where('warga_id', $id_warga)
            ->orderByDesc('tanggal')
            ->orderByDesc('dibuat_pada')
            ->first();

        // Statistik berdasarkan kategori_masalah saja
        $stats = DB::table('tracking_harian')
            ->selectRaw('
                COUNT(*) as total,
                SUM(kategori_masalah = "Tidak Aman") as tidak_aman_count,
                SUM(kategori_masalah = "Aman") as aman_count,
                SUM(kategori_masalah = "Belum Dicek") as belum_dicek_count
            ')
            ->where('warga_id', $id_warga)
            ->first();

        return view('warga.riwayat', compact('tracking', 'latest', 'stats'));
    }
}
