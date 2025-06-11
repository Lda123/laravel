<?php

namespace App\Http\Controllers\kader;

use App\Http\Controllers\Controller;
use App\Models\SavedVideo;
use Illuminate\Support\Facades\Auth;

class VideoSayaController extends Controller
{
    public function index()
    {
        $kader = Auth::guard('kader')->user();

        // Mengambil video yang disimpan dengan eager loading
        $savedVideos = SavedVideo::where('kader_id', $kader->id)
            ->with(['video' => function($query) {
                $query->select('id', 'judul', 'isi', 'kategori', 'tautan', 'views');
            }])
            ->orderBy('saved_at', 'desc')
            ->paginate(9);

        // Menghitung total video yang disimpan dengan query terpisah
        $totalSaved = SavedVideo::where('kader_id', $kader->id)->count();

        return view('kader.video_saya', [
            'savedVideos' => $savedVideos,
            'totalSaved' => $totalSaved
        ]);
    }

    public function destroy($id)
    {
        $savedVideo = SavedVideo::where('kader_id', Auth::id())
                                ->where('id', $id)
                                ->firstOrFail();

        $savedVideo->delete();

        return back()->with('success', 'Video berhasil dihapus dari koleksi Anda');
    }
}