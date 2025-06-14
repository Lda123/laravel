<?php

namespace App\Http\Controllers\Warga\fitur_utama;

use App\Http\Controllers\Controller;
use App\Models\SavedEdukasiWarga;
use Illuminate\Support\Facades\Auth;

class InformasiSayaController extends Controller
{
    public function index()
    {
        $warga = Auth::guard('warga')->user();

        // Mengambil edukasi yang disimpan dengan eager loading
        $savedEdukasi = SavedEdukasiWarga::where('warga_id', $warga->id)
            ->with(['edukasi' => function($query) {
                $query->select('id', 'judul', 'isi', 'kategori', 'tautan', 'tipe', 'views'); // Changed 'jenis' to 'tipe'
            }])
            ->orderBy('saved_at', 'desc')
            ->paginate(9);

        // Menghitung total yang disimpan
        $totalSaved = SavedEdukasiWarga::where('warga_id', $warga->id)->count();

        return view('warga.informasi_saya', [
            'savedEdukasi' => $savedEdukasi,
            'totalSaved' => $totalSaved
        ]);
    }

    public function destroy($id)
    {
        $savedEdukasi = SavedEdukasiWarga::where('warga_id', Auth::id())
                                ->where('id', $id)
                                ->firstOrFail();

        $savedEdukasi->delete();

        return back()->with('success', 'Edukasi berhasil dihapus dari koleksi Anda');
    }
}