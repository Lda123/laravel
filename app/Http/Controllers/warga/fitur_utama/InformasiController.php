<?php
namespace App\Http\Controllers\Warga\fitur_utama;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use App\Models\SavedEdukasiWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category', 'all');
        $type = $request->input('type', 'all');
        
        $edukasi = Edukasi::query()
            ->when($type !== 'all', function($query) use ($type) {
                return $query->where('tipe', $type);
            })
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
            ->select('kategori')
            ->distinct()
            ->pluck('kategori');
        
        // Get saved items for authenticated users
        $savedItems = [];
        if (Auth::guard('warga')->check()) {
            $savedItems = SavedEdukasiWarga::where('warga_id', Auth::guard('warga')->id())
                ->pluck('edukasi_id')
                ->toArray();
        }
                
        return view('warga.informasi', [
            'edukasiItems' => $edukasi,
            'categories' => $categories,
            'search' => $search,
            'category' => $category,
            'type' => $type,
            'savedItems' => $savedItems
        ]);
    }

    public function view($id)
    {
        $detailItem = Edukasi::findOrFail($id);
        
        // Get saved items for authenticated users
        $savedItems = [];
        if (Auth::guard('warga')->check()) {
            $savedItems = SavedEdukasiWarga::where('warga_id', Auth::guard('warga')->id())
                ->pluck('edukasi_id')
                ->toArray();
        }

        return view('warga.informasi', [
            'detailItem' => $detailItem,
            'savedItems' => $savedItems
        ]);
    }
    
    public function incrementViews($id)
    {
        try {
            $edukasi = Edukasi::findOrFail($id);
            $edukasi->increment('views');
            
            return response()->json([
                'status' => 'success',
                'views' => $edukasi->views,
                'message' => 'Views berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error incrementing views: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui views: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveEdukasi(Request $request)
    {
        if (!Auth::guard('warga')->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login sebagai warga untuk menyimpan edukasi'
            ], 401);
        }

        $request->validate([
            'edukasi_id' => 'required|exists:edukasi,id',
            'action' => 'required|in:save,unsave'
        ]);
        
        $wargaId = Auth::guard('warga')->id();
        $edukasiId = $request->edukasi_id;
        
        try {
            if ($request->action === 'save') {
                // Check if already saved
                $existing = SavedEdukasiWarga::where('warga_id', $wargaId)
                                    ->where('edukasi_id', $edukasiId)
                                    ->first();
                
                if (!$existing) {
                    SavedEdukasiWarga::create([
                        'warga_id' => $wargaId,
                        'edukasi_id' => $edukasiId,
                        'saved_at' => now()
                    ]);
                }
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Edukasi berhasil disimpan',
                    'action' => 'unsave' // Next action will be unsave
                ]);
            } else {
                // Unsave action
                SavedEdukasiWarga::where('warga_id', $wargaId)
                        ->where('edukasi_id', $edukasiId)
                        ->delete();
                    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Edukasi berhasil dihapus dari simpanan',
                    'action' => 'save' // Next action will be save
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