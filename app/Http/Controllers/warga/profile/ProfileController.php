<?php

namespace App\Http\Controllers\Warga\profile;

use App\Http\Controllers\Controller;
use App\Models\EventWarga;
use App\Models\Warga;
use App\Models\TrackingHarian;
use App\Models\SavedEdukasiWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $warga = Auth::guard('warga')->user();
        
        // Get latest home condition status from tracking_harian
        $homeCondition = TrackingHarian::where('warga_id', $warga->id)
            ->latest('tanggal')
            ->first();    
            
        // Determine status display
        $statusDisplay = [
            'Aman' => 'Aman',
            'Tidak Aman' => 'Tidak Aman',
            'Belum Dicek' => 'Belum Dicek'
        ];
        
        // Generate correct profile picture URL
        $profilePictureUrl = null;
        if ($warga->profile_pict) {
            $normalizedPath = str_replace('profile_pictures/warga/profile_pictures/warga/', 'profile_pictures/warga/', $warga->profile_pict);
            $profilePictureUrl = Storage::disk('public')->exists($normalizedPath) 
                ? asset('storage/'.$normalizedPath) 
                : null;
        }

        // Count saved edukasi and events
        $savedEdukasiCount = SavedEdukasiWarga::where('warga_id', $warga->id)->count();
        $savedEventCount = EventWarga::where('id_warga', $warga->id)->count();

        return view('warga.profile', [
            'user' => $warga,
            'home_condition' => $homeCondition,
            'status_display' => $statusDisplay,
            'profile_picture_url' => $profilePictureUrl,
            'savedEdukasiCount' => $savedEdukasiCount,
            'savedEventCount' => $savedEventCount
        ]);
    }
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        /** @var Warga $warga */
        $warga = Auth::guard('warga')->user();

        try {
            DB::beginTransaction();
            
            // Hapus foto lama jika ada
            if ($warga->profile_pict) {
                $oldPath = str_replace('profile_pictures/warga/profile_pictures/warga/', 'profile_pictures/warga/', $warga->profile_pict);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            // Generate unique filename
            $fileName = 'warga_'.$warga->id.'_'.now()->timestamp.'.'.$request->file('profile_picture')->extension();
            
            // Simpan ke folder yang benar
            $path = $request->file('profile_picture')->storeAs(
                'profile_pictures/warga', 
                $fileName, 
                'public'
            );
            
            // Update database dengan path yang benar
            $warga->update(['profile_pict' => $path]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'image_url' => asset('storage/'.$path)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Profile picture upload error: '.$e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto profil: '.$e->getMessage()
            ], 500);
        }
    }

    public function getProfileData()
    {
        $warga = Auth::guard('warga')->user();
        
        return response()->json([
            'success' => true,
            'profile' => [
                'nama_lengkap' => $warga->nama_lengkap,
                'telepon' => $warga->telepon,
                'alamat_lengkap' => $warga->alamat_lengkap,
                'profile_picture' => $warga->profile_pict ? asset('storage/' . $warga->profile_pict) : null,
                'home_condition' => $this->getHomeConditionData($warga)
            ]
        ]);
    }

    /**
     * Mendapatkan data kondisi rumah
     */
    private function getHomeConditionData($warga)
    {
        $homeCondition = TrackingHarian::where('warga_id', $warga->id)
            ->latest('tanggal')
            ->first();

        if (!$homeCondition) {
            return null;
        }

        return [
            'status' => $homeCondition->kategori_masalah,
            'date' => \Carbon\Carbon::parse($homeCondition->tanggal)->format('d M Y'),
            'description' => $homeCondition->deskripsi ?? 'Tidak ada deskripsi tambahan'
        ];
    }
}