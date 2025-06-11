<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Kader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class ProfileKaderController extends Controller
{
    /**
     * Menampilkan halaman profil kader
     */
    public function show(): View
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();
       
        // Load relationships dengan nested loading untuk informasi lengkap RT, Kelurahan, dan Kecamatan
        $kader->load([
            'rt', 
            'rt.rw',
            'rt.rw.kelurahan',
            'rt.rw.kelurahan.kecamatan'
        ]);
       
        // Hitung jumlah video yang disimpan
        $savedCount = $kader->savedVideos()->count();
        
        // Hitung jumlah event yang diikuti
        $eventCount = $kader->events()->count();
        
        // Data statistik tambahan
        $stats = [
            'saved_videos' => $savedCount,
            'joined_events' => $eventCount,
            'days_active' => $kader->created_at ? $kader->created_at->diffInDays(now()) : 
                           ($kader->dibuat_pada ? $kader->dibuat_pada->diffInDays(now()) : 0),
            'profile_completion' => $this->calculateProfileCompletion($kader)
        ];

        // Debug info untuk gambar profil
        $profileImageInfo = [
            'foto_profil' => $kader->foto_profil,
            'profil_pict' => $kader->profil_pict ?? null,
            'foto_profil_exists' => $kader->foto_profil ? Storage::disk('public')->exists($kader->foto_profil) : false,
            'profil_pict_exists' => ($kader->profil_pict ?? null) ? Storage::disk('public')->exists($kader->profil_pict) : false,
        ];

        // Log untuk debugging
        \Log::info('Profile Image Debug', $profileImageInfo);
       
        return view('kader.profile', compact('kader', 'savedCount', 'eventCount', 'stats'));
    }

    /**
     * Menghitung persentase kelengkapan profil
     */
    private function calculateProfileCompletion(Kader $kader): int
    {
        $fields = [
            'nama_lengkap' => !empty($kader->nama_lengkap),
            'telepon' => !empty($kader->telepon),
            'rt_id' => !empty($kader->rt_id),
            'password' => !empty($kader->password),
            'foto_profil' => !empty($kader->foto_profil) || !empty($kader->profil_pict),
        ];
        
        $completedFields = array_filter($fields);
        $completionPercentage = (count($completedFields) / count($fields)) * 100;
        
        return (int) $completionPercentage;
    }

    /**
     * Mendapatkan informasi lokasi kader
     */
    public function getLocationInfo(): array
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();
        
        $kader->load([
            'rt', 
            'rt.rw',
            'rt.rw.kelurahan',
            'rt.rw.kelurahan.kecamatan'
        ]);

        $locationInfo = [
            'rt' => $kader->rt->nomor_rt ?? null,
            'rw' => $kader->rt->rw->nomor_rw ?? null,
            'kelurahan' => $kader->rt->rw->kelurahan->nama_kelurahan ?? null,
            'kecamatan' => $kader->rt->rw->kelurahan->kecamatan->nama_kecamatan ?? null,
        ];

        return $locationInfo;
    }

    /**
     * Menampilkan halaman edit profil
     */
    public function edit(): View
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();
        
        return view('kader.profile-edit', compact('kader'));
    }

    /**
     * Update profil kader
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validated = $request->validate($rules);

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($kader->foto_profil && Storage::disk('public')->exists($kader->foto_profil)) {
                Storage::disk('public')->delete($kader->foto_profil);
            }
            
            // Simpan foto baru
            $path = $request->file('foto_profil')->store('profile-pictures', 'public');
            $validated['foto_profil'] = $path;
        }

        // Update data kader
        $kader->update($validated);

        return redirect()->route('kader.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Upload atau update foto profil via AJAX
     */
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();

        try {
            // Hapus foto lama jika ada
            if ($kader->foto_profil && Storage::disk('public')->exists($kader->foto_profil)) {
                Storage::disk('public')->delete($kader->foto_profil);
            }
            
            // Simpan foto baru dengan nama unik
            $fileName = 'profile_' . $kader->id . '_' . time() . '.' . $request->file('foto_profil')->getClientOriginalExtension();
            $path = $request->file('foto_profil')->storeAs('profile-pictures', $fileName, 'public');
            
            // Update record kader
            $kader->update(['foto_profil' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'image_url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            \Log::error('Profile picture upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah foto profil'
            ], 500);
        }
    }

    /**
     * Hapus foto profil
     */
    public function deleteProfilePicture(): RedirectResponse
    {
        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();

        try {
            // Hapus file dari storage
            if ($kader->foto_profil && Storage::disk('public')->exists($kader->foto_profil)) {
                Storage::disk('public')->delete($kader->foto_profil);
            }
            
            // Update record kader
            $kader->update(['foto_profil' => null]);

            return redirect()->route('kader.profile')->with('success', 'Foto profil berhasil dihapus');

        } catch (\Exception $e) {
            \Log::error('Profile picture delete error: ' . $e->getMessage());
            
            return redirect()->route('kader.profile')->with('error', 'Gagal menghapus foto profil');
        }
    }

    /**
     * Ganti password kader
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var Kader $kader */
        $kader = Auth::guard('kader')->user();

        // Cek password lama
        if (!Hash::check($request->current_password, $kader->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        // Update password baru
        $kader->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('kader.profile')->with('success', 'Password berhasil diubah');
    }

    private function getProfilePictureUrl(Kader $kader): ?string
    {
        if ($kader->foto_profil) {
            // Cek di storage public
            $publicPath = 'profile_pictures/' . basename($kader->foto_profil);
            if (Storage::disk('public')->exists($publicPath)) {
                return Storage::disk('public')->url($publicPath);
            }
            
            // Cek path langsung jika tidak ada di profile_pictures
            if (Storage::disk('public')->exists($kader->foto_profil)) {
                return Storage::disk('public')->url($kader->foto_profil);
            }
        }
        
        return null;
    }
}