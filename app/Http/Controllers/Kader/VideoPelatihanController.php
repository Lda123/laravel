<?php

namespace App\Http\Controllers\kader;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use App\Models\SavedVideo; // Make sure this model exists
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoPelatihanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category', 'all');
        
        $videos = Edukasi::query()
            ->video() // Now this scope is defined in the model
            ->forKader() // This scope is also defined in the model
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%$search%")
                      ->orWhere('isi', 'like', "%$search%");
                });
            })
            ->when($category !== 'all', function($query) use ($category) {
                return $query->where('kategori', $category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(9);
                
        $categories = Edukasi::query()
            ->video()
            ->forKader()
            ->select('kategori')
            ->distinct()
            ->pluck('kategori');
                
        return view('kader.video_pelatihan', [
            'videos' => $videos,
            'categories' => $categories,
            'search' => $search,
            'category' => $category,
            'savedVideos' => Auth::user()->savedVideos->pluck('id')->toArray()
        ]);
    }

    public function view($id)
    {
        $video = Edukasi::findOrFail($id);
        $saved = SavedVideo::where('kader_id', Auth::id())
                            ->where('video_id', $id)
                            ->exists();

        return view('kader.video_pelatihan', [
            'video' => $video,
            'isSaved' => $saved
        ]);
    }
    
    public function incrementViews($id)
    {
        $video = Edukasi::findOrFail($id);
        $video->increment('views');
        
        return response()->json([
            'status' => 'success',
            'views' => $video->views
        ]);
    }

    // VideoPelatihanController.php

    public function saveVideo(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:edukasi,id',
            'action' => 'required|in:save,unsave'
        ]);
        
        $kaderId = Auth::id();
        $videoId = $request->video_id;
        
        try {
            if ($request->action === 'save') {
                // Cek apakah video sudah disimpan sebelumnya
                $existing = SavedVideo::where('kader_id', $kaderId)
                                    ->where('video_id', $videoId)
                                    ->first();
                
                if (!$existing) {
                    SavedVideo::create([
                        'kader_id' => $kaderId,
                        'video_id' => $videoId,
                        'saved_at' => now()
                    ]);
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Video berhasil disimpan',
                    'action' => 'unsave' // Memberitahu frontend action berikutnya
                ]);
            } else {
                SavedVideo::where('kader_id', $kaderId)
                        ->where('video_id', $videoId)
                        ->delete();
                    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Video berhasil dihapus dari simpanan',
                    'action' => 'save' // Memberitahu frontend action berikutnya
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}