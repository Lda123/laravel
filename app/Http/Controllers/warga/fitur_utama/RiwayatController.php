<?php

namespace App\Http\Controllers\warga\fitur_utama;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RiwayatController extends Controller
{
    public function index()
    {
        $warga = Auth::guard('warga')->user();
        
        if (!$warga) {
            return redirect()->route('login')->with('error', 'Anda harus login sebagai warga.');
        }
        
        $laporans = Laporan::with(['rt', 'rw'])
            ->where('warga_id', $warga->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('warga.riwayat', compact('laporans', 'warga'));
    }

    public function show($id)
    {
        try {
            $warga = Auth::guard('warga')->user();
            
            if (!$warga) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

            $laporan = Laporan::with(['rt', 'rw', 'kelurahan', 'kecamatan'])
                ->where('id', $id)
                ->where('warga_id', $warga->id)
                ->first();
            
            if (!$laporan) {
                return response()->json([
                    'success' => false,
                    'error' => 'Data laporan tidak ditemukan'
                ], 404);
            }

            $data = [
                'id' => $laporan->id,
                'created_at' => $laporan->created_at->format('Y-m-d H:i:s'),
                'jenis_laporan' => $laporan->jenis_laporan,
                'alamat_detail' => $laporan->alamat_detail,
                'rt' => $laporan->rt ? $laporan->rt->nomor_rt : null,
                'rw' => $laporan->rw ? $laporan->rw->nomor_rw : null,
                'deskripsi' => $laporan->deskripsi,
                'status' => $laporan->status,
                'foto_pelaporan' => $laporan->foto_pelaporan_url,
                'tindakan' => $laporan->tindakan
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing laporan detail: ' . $e->getMessage(), [
                'laporan_id' => $id,
                'warga_id' => Auth::guard('warga')->id(),
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}